<?php
require_once(__DIR__ . '/../DBModels/activityModel.php');
require_once(__DIR__ . '/../DBModels/assignmentModel.php');
require_once(__DIR__ . '/../DBModels/requirementModel.php');
require_once(__DIR__ . '/../DBModels/siteModel.php');
require_once(__DIR__ . '/../DBModels/activitytypologyModel.php');
require_once(__DIR__ . '/../DBModels/procedureModel.php');
require_once(__DIR__ . '/../DBModels/materialModel.php');
require_once(__DIR__ . '/../DBModels/SMPModel.php');
require_once('servicesExceptions.php');

class ActivityManagementService
{
    private $db;

    /**
     * Create an istance of the service.
     * 
     * The service create a connection with a database, so all the info related to this connection must be provided.
     *
     * @param string $host the host of the database management system.
     * @param string $port the port of the database management system.
     * @param string $dbname the database name.
     * @param string $username the username for accessing to the database.
     * @param string $password  the username for accessing to the database.
     */
    public function __construct($host, $port, $dbname, $username, $password)
    {
        $this->db = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    }

    /**
     * Adds an activity
     *
     * @param array $activityinfo an associative array composed by the following keys:
     * ['activityid', 'description', 'scheduledweek','estimatedtime','site','typology','procedure','material,'interruptible']
     * @return void
     * 
     * @throws WrongDataException if the activity is already present, or if the given materials doesn't exist.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function addActivity($activityinfo)
    {
        $activityModel = new ActivityModel($this->db);
        $requirementModel = new RequirementModel($this->db);
        $materialModel = new MaterialModel($this->db);

        if ($activityModel->get($activityinfo['activityid']) != false)
            throw new WrongDataException();

        $this->db->beginTransaction();
        try {
            $activityModel->insert(
                $activityinfo['activityid'],
                $activityinfo['description'],
                $activityinfo['scheduledweek'],
                $activityinfo['estimatedtime'],
                $activityinfo['site'],
                $activityinfo['typology'],
                $activityinfo['procedure'],
                $activityinfo['interruptible']
            );
            if (!$materialModel->existAll($activityinfo['materials'])) {
                $this->db->rollBack();
                throw new WrongDataException();
            }
            $requirementModel->insertAll($activityinfo['activityid'], $activityinfo['materials']);
        } catch (ServerErrorException | WrongDataFormatException $e) {
            $this->db->rollBack();
            throw $e;
        }

        $this->db->commit();
    }

    /**
     * Deletes an activity
     *
     * @param string $activityid the activity's id.
     * @throws WrongDataException if the activity doesn't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function deleteActivity($activityid)
    {
        $activityModel = new ActivityModel($this->db);
        if (!$activityModel->delete($activityid))
            throw new WrongDataException();
    }

    /**
     * Edits an activity
     *
     * @param string $activityid the activity's id.
     * @param array $activityinfo an associative array composed by the following keys:
     * ['description', 'scheduledweek','estimatedtime','site','typology','procedure','material,'interruptible']
     * @throws WrongDataException if the activity doesn't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function editActivity($activityid, $activityinfo)
    {
        $activityModel = new ActivityModel($this->db);
        $requirementModel = new RequirementModel($this->db);
        $materialModel = new MaterialModel($this->db);

        $this->db->beginTransaction();

        try {
            $result = $activityModel->update(
                $activityid,
                $activityinfo['description'],
                $activityinfo['scheduledweek'],
                $activityinfo['estimatedtime'],
                $activityinfo['site'],
                $activityinfo['typology'],
                $activityinfo['procedure'],
                $activityinfo['interruptible']
            );
            $result = ($result and $materialModel->existAll($activityinfo['materials']));
            if (!$result) {
                $this->db->rollBack();
                throw new WrongDataException();
            }
            $requirementModel->update($activityid, $activityinfo['materials']);
        } catch (ServerErrorException | WrongDataFormatException $e) {
            $this->db->rollBack();
            throw $e;
        }

        $this->db->commit();
    }

    /**
     * Returns all the activities' info
     *
     * @return array an array of associative arrays.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getActivitiesInfo()
    {
        $activityModel = new ActivityModel($this->db);
        $assignmentModel = new AssignmentModel($this->db);
        $requirementModel = new RequirementModel($this->db);
        $smpModel = new SMPModel($this->db);
        $activities = $activityModel->getAll();

        foreach ($activities as $index => $activity) {
            $activities[$index]['materials'] = $requirementModel->get($activity['activityid']);
            $assignedto = $assignmentModel->get($activity['activityid']);
            $activities[$index]['assignedto'] = !$assignedto ? null : $assignedto;

            $smpfile = $smpModel->get($activity['procedure']);
            $activities[$index]['smpfile'] = !$smpfile ? false : true;
        }

        return $activities;
    }

    /**
     * Returns all the activities' info, with additional details.
     *
     * @return array an array of associative arrays.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getActivitiesInfoVerbose()
    {
        $activityModel = new ActivityModel($this->db);
        $assignmentModel = new AssignmentModel($this->db);
        $requirementModel = new RequirementModel($this->db);

        $materialModel = new MaterialModel($this->db);
        $siteModel = new SiteModel($this->db);
        $typologyModel = new ActivityTypologyModel($this->db);
        $procedureModel = new ProcedureModel($this->db);
        $smpModel = new SMPModel($this->db);

        $activities = $activityModel->getAll();

        foreach ($activities as $index => $activity) {
            $materials = $requirementModel->get($activity['activityid']);
            $activities[$index]['materials'] = $materialModel->getMultiple($materials);
            $activities[$index]['site'] = $siteModel->get($activity['site']);
            $activities[$index]['typology'] = $typologyModel->get($activity['typology']);
            $activities[$index]['procedure'] = $procedureModel->get($activity['procedure']);

            $assignedto = $assignmentModel->get($activity['activityid']);
            $activities[$index]['assignedto'] = !$assignedto ? null : $assignedto;

            $smpfile = $smpModel->get($activity['procedure']);
            $activities[$index]['smpfile'] = !$smpfile ? false : true;
        }

        return $activities;
    }

    /**
     * Returns all the activity's info
     *
     * @param string $activityid the activity's id
     * @return array an associative arrays.
     * 
     * @throws WrongDataException if the activity doesn't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getActivityInfo($activityid)
    {
        $activityModel = new ActivityModel($this->db);
        $assignmentModel = new AssignmentModel($this->db);
        $requirementModel = new RequirementModel($this->db);
        $smpModel = new SMPModel($this->db);

        $activity = $activityModel->get($activityid);
        if (!$activity)
            throw new WrongDataException();

        $activity['materials'] = $requirementModel->get($activity['activityid']);
        $assignedto = $assignmentModel->get($activity['activityid']);
        $activity['assignedto'] = !$assignedto ? null : $assignedto;
        $smpfile = $smpModel->get($activity['procedure']);
        $activity['smpfile'] = !$smpfile ? false : true;

        return $activity;
    }

    /**
     * Returns all the activity's info, with additional details.
     *
     * @param string $activityid the activity's id
     * @return array an associative arrays.
     * 
     * @throws WrongDataException if the activity doesn't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getActivityInfoVerbose($activityid)
    {
        $activityModel = new ActivityModel($this->db);
        $activity = $activityModel->get($activityid);
        if (!$activity)
            throw new WrongDataException();

        $assignmentModel = new AssignmentModel($this->db);
        $requirementModel = new RequirementModel($this->db);

        $materialModel = new MaterialModel($this->db);
        $siteModel = new SiteModel($this->db);
        $typologyModel = new ActivityTypologyModel($this->db);
        $procedureModel = new ProcedureModel($this->db);
        $smpModel = new SMPModel($this->db);

        $materials = $requirementModel->get($activity['activityid']);
        $activity['materials'] = $materialModel->getMultiple($materials);
        $activity['site'] = $siteModel->get($activity['site']);
        $activity['typology'] = $typologyModel->get($activity['typology']);
        $activity['procedure'] = $procedureModel->get($activity['procedure']);

        $assignedto = $assignmentModel->get($activity['activityid']);
        $activity['assignedto'] = !$assignedto ? null : $assignedto;
        $smpfile = $smpModel->get($activity['procedure']['procedureid']);
        $activity['smpfile'] = !$smpfile ? false : true;

        return $activity;
    }

    /**
     * Assign the given activity to a maintainer
     *
     * @param string $activityid the activity to be assigned.
     * @param array $assignmentinfo an associative array composed by the following keys:
     * ['userid', 'day', 'starttime', 'endtime']
     * @return void
     * 
     * @throws WrongDataException if the activity or the maintainer doesn't exist.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function addAssignmentActivity($activityid, $assignmentinfo)
    {
        $assignmentModel = new AssignmentModel($this->db);
        if (!$assignmentModel->insert($activityid, $assignmentinfo['userid'],  $assignmentinfo['day'],  $assignmentinfo['starttime'],  $assignmentinfo['endtime']))
            throw new WrongDataException();
    }

    /**
     * Delete the assignment of the given activity
     *
     * @param string $activityid the activity id.
     * @return void
     * 
     * @throws WrongDataException if the assignment doesn't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function deleteAssignmentActivity($activityid)
    {
        $assignmentModel = new AssignmentModel($this->db);
        if (!$assignmentModel->delete($activityid))
            throw new WrongDataException();
    }
}
