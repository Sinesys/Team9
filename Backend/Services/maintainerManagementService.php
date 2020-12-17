<?php
require_once(__DIR__ . '/../DBModels/maintainerModel.php');
require_once(__DIR__ . '/../DBModels/userInfoModel.php');
require_once(__DIR__ . '/../DBModels/assignmentModel.php');
require_once(__DIR__ . '/../DBModels/masteryModel.php');
require_once('servicesExceptions.php');

class MaintainerManagementService
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
      * Get all the maintainers' info, with the unavailabilities filtered by the given temporal range.
      *
      * @param string $startday the starting day.
      * @param string $endday the ending day.
      * @param string $starthour the starting hour.
      * @param string $endhour the ending hour.
      * @return array an array of associative arrays. 
      * 
      * @throws WrongDataFormatException if the parameters are in a wrong format.
      * @throws ServerErrorException if there is a particular error in the database.
      */
    public function getMaintainersUnavailabilitiesRange($startday, $endday, $starthour, $endhour)
    {
        $maintainerModel = new MaintainerModel($this->db);
        $maintainers = $maintainerModel->getAll();

        $users = [];
        $userInfoModel = new UserInfoModel($this->db);
        $masteryModel = new MasteryModel($this->db);
        $assignmentModel = new AssignmentModel($this->db);

        foreach ($maintainers as $maintainer) {
            $info = $userInfoModel->get($maintainer['user_id']);
            $info['competences'] = $masteryModel->get($info['userid']);

            $unavaibilities = $assignmentModel->getByMaintainerFromTo($maintainer['user_id'], $startday, $endday, $starthour, $endhour);
            $info['unavailability'] = [];
            foreach ($unavaibilities as $unavail)
                $info['unavailability'][$unavail['day']][] = ['start' => $unavail['starttime'], 'end' => $unavail['endtime']];

            $users[] = $info;
        }

        return $users;
    }

    /**
     * Returns all the maintainers' info
     *
     * @return array an array of associative arrays.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getMaintainersInfo()
    {
        $maintainerModel = new MaintainerModel($this->db);
        $maintainers = $maintainerModel->getAll();

        $users = [];
        $userInfoModel = new UserInfoModel($this->db);
        $masteryModel = new MasteryModel($this->db);
        $assignmentModel = new AssignmentModel($this->db);

        foreach ($maintainers as $maintainer) {
            $info = $userInfoModel->get($maintainer['user_id']);
            $info['competences'] = $masteryModel->get($info['userid']);
            $info['unavailability'] = $assignmentModel->getByMaintainer($info['userid']);
            $users[] = $info;
        }
        return $users;
    }

    /**
     * Returns all the maintainer's info
     *
     * @return array an array of associative arrays.
     * 
     * @throws WrongDataException if the maintainer doesn't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getMaintainerInfo($id)
    {
        $userInfoModel = new UserInfoModel($this->db);
        $masteryModel = new MasteryModel($this->db);
        $assignmentModel = new AssignmentModel($this->db);

        $info = $userInfoModel->get($id);
        if ($info === false)
            throw new WrongDataException();
        $info['competences'] = $masteryModel->get($info['userid']);
        $info['unavailability'] = $assignmentModel->getByMaintainer($info['userid']);

        return $info;
    }
}
