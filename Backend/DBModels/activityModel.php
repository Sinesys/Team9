<?php
require_once('abstractModel.php');
require_once('dbExceptions.php');

class ActivityModel extends AbstractModel
{
    private $alias =[
        'activity_id' => 'activityid', 
        'description' => null,
        'scheduled_week' => 'scheduledweek', 
        'estimated_time' => 'estimatedtime', 
        'site' => null,
        'typology' => null,
        'procedure' => null,
        'interruptible' => null,
    ];

    /**
     * Get the activity's info from the given id
     *
     * @param string $activityid the id of the activity that must be searched. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return array an associative array composed by the keys: 
     * ['activityid', 'description', 'scheduledweek', 'estimatedtime', 'site', 'typology', 'procedure', 'interruptible']
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function get($id)
    {
        return parent::getSingleAs($this->alias, ['activity_id' => $id]);
    }

    /**
     * Insert an activity
     *
     * @param string $activityid the id of the activity that must be insert. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $description the description of the activity. Can be null;
     * @param int $scheduledweek the week in which the activity was scheduled. Must be a value between 1 and 52.
     * @param int $estimatedtime the estimated time to complete the activity. Must be > 0.
     * @param string, the site in which the activity is to be carried out. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $typology the typology of the activity.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $procedure the standard procedure to perform.
     * Must cointains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param boolean $interruptible a boolean indicating if the activity is interruptible or not. 
     * 
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insert($activityid, $description, $scheduledweek, $estimatedtime, $site, $typology, $procedure, $interruptible)
    {
        $conditions = [
            'activity_id' => $activityid,
            'description' => $description,
            'scheduled_week' => $scheduledweek,
            'estimated_time' => $estimatedtime,
            'site' => $site,
            'typology' => $typology,
            'procedure' => $procedure,
            'interruptible' => (int)$interruptible
        ];
        return parent::genericInsert($conditions);
    }

   /**
     * Delete an activity
     * 
     * @param string $activityid the id of the activity that must be deleted. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return boolean return true if at least one row has been deleted, false if no lines have been deleted.
     * 
     * @throws WrongDataFormatException if the parameters is in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function delete($activityid)
    {
        return parent::genericDelete(['activity_id' => $activityid]);
    }

    /**
     * Update an activity
     *
     * @param string $activityid the id of the activity that must be updated. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $description the description of the activity. Can be null;
     * @param int $scheduledweek the week in which the activity was scheduled. Must be a value between 1 and 52.
     * @param int $estimatedtime the estimated time to complete the activity. Must be > 0.
     * @param string $site, the site in which the activity is to be carried out. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $typology the typology of the activity.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $procedure the standard procedure to perform.
     * Must cointains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param boolean $interruptible a boolean indicating if the activity is interruptible or not. 
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function update($activityid, $description, $scheduledweek, $estimatedtime, $site, $typology, $procedure, $interruptible)
    {
        $values = [
            'description' => $description,
            'scheduled_week' => $scheduledweek,
            'estimated_time' => $estimatedtime,
            'site' => $site,
            'typology' => $typology,
            'procedure' => $procedure,
            'interruptible' => (int)$interruptible
        ];
        return parent::genericUpdate($values, ['activity_id' => $activityid]);
    }

    /**
     * Get all the activities' info
     *
     * @return array an array of associative arrays composed by the keys:
     * ['activityid', 'description', 'scheduledweek', 'estimatedtime', 'site', 'typology', 'procedure', 'interruptible']
     * 
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getAll()
    {
        return parent::getAllAs($this->alias);
    }

    protected function getTableName()
    {
        return "activity";
    }

    protected function validateField($field)
    {
        return in_array($field,['activity_id', 'description', 'scheduled_week', 'estimated_time', 'site', 'typology', 'procedure', 'interruptible']);
    }

    protected function validate($field, $value)
    {
        switch ($field) {
            case 'activity_id':
                return preg_match('/^([0-9a-zA-Z_]){1,20}$/', $value);
                break;
            case 'description':
                return true;
                break;
            case 'scheduled_week':
                return ($value >= 1 and $value <= 52);
                break;
            case 'estimated_time':
                return $value > 0;
                break;
            case 'site':
                return preg_match('/^([0-9a-zA-Z_]){1,20}$/', $value);
                break;
            case 'typology':
                return preg_match('/^([0-9a-zA-Z_]){1,20}$/', $value);
                break;
            case 'procedure':
                return preg_match('/^([0-9a-zA-Z_]){1,20}$/', $value);
                break;
            case 'interruptible':
                return ($value==true or $value==false);
                break;
            default:
                return false;
        }
    }
}
