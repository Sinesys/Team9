<?php
require_once('abstractModel.php');
require_once('dbExceptions.php');

class AssignmentModel extends AbstractModel
{
    private $alias =[
        'maintainer' => 'maintainer', 
        'day' => 'day',
        'start_time' => 'starttime', 
        'end_time' => 'endtime'
    ];

    /**
     * Get the assignment's info for the given activity.
     *
     * @param string $id the id of the activity. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return array an associative array composed by the keys: 
     * ['maintainer', 'day', 'starttime', 'endtime']
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function get($id)
    {
        return parent::getSingleAs($this->alias, ['activity' => $id]);
    }

    /**
     * Get all the assignments' info of the given maintainer.
     *
     * @param string $maintainer the id of the maintainer. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return array an associative array composed by the keys: 
     * ['maintainer', 'day', 'starttime', 'endtime']
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getByMaintainer($maintainer)
    {
        return parent::getAs($this->alias, ['maintainer' => $maintainer]);
    }

     /**
      * Get all the assignments' info of the maintainer, in the given temporal range.
      *
      * @param string $maintainer the id of the maintainer. 
      * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
      * @param string $startday the starting day.
      * It Must be in this form: 'YYYY-MM-DD';
      * @param string $endday the ending day.
      * It Must be in this form: 'YYYY-MM-DD';
      * @param string $starthour the starting hour.
      * It Must be in this form: 'HH:mm';
      * @param string $endhour the ending hour.
      * It Must be in this form: 'HH:mm';
      * @return array an array of associative arrays composed by the keys: 
      * ['day', 'starttime', 'endtime']
      * 
      * @throws WrongDataFormatException if the parameters are in a wrong format.
      * @throws ServerErrorException if there is a particular error in the database.
      */
    public function getByMaintainerFromTo($maintainer, $startday, $endday, $starthour, $endhour){
        $data = [
            'maintainer' => $maintainer,
            'start_time'=> $starthour,
            'end_time'=> $endhour,
        ];
        if (!$this->multipleValidate($data))
            throw new WrongDataFormatException();
        if (!$this->multipleValidate(['day'=> $startday, 'day'=> $endday]))
            throw new WrongDataFormatException();
        $data['start_day'] = $startday;
        $data['end_day'] = $endday;

        $stmt = $this->db->prepare("SELECT day, start_time as starttime, end_time as endtime
                                    FROM " . $this->getTableName() . "
                                    WHERE maintainer = :maintainer AND (day >= :start_day AND day<= :end_day) AND (start_time>= :start_time AND end_time<= :end_time)
                                    ORDER BY day ASC, start_time ASC;");
        $result = $stmt->execute($data);
        if ($result === false)
            throw new ServerErrorException();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Insert an assigment for the given activity
     *
     * @param string $activity the id of the activity.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $maintainer the id of the maintainer.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $startday the starting day.
     * It Must be in this form: 'YYYY-MM-DD';
     * @param string $endday the ending day.
     * It Must be in this form: 'YYYY-MM-DD';
     * @param string $starttime the starting hour.
     * It Must be in this form: 'HH:mm';
     * @param string $endtime the ending hour.
     * It Must be in this form: 'HH:mm';
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insert($activity, $maintainer, $day, $starttime, $endtime)
    {
        $data = [
            'activity' => $activity,
            'maintainer' => $maintainer,
            'day'=> $day,
            'start_time'=>$starttime,
            'end_time'=>$endtime
        ];
        return parent::genericInsert($data); 
    }

    /**
     * Delete an assignment
     * 
     * @param string $activity the id of the activity's assignment that must be deleted. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return boolean return true if at least one row has been deleted, false if no lines have been deleted.
     * 
     * @throws WrongDataFormatException if the parameters is in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function delete($activity)
    {
        return parent::genericDelete(['activity' => $activity]);
    }

    protected function getTableName()
    {
        return "assignment";
    }

    protected function validateField($field)
    {
        return in_array($field,['activity', 'maintainer', 'day', 'start_time', 'end_time']);
    }

    protected function validate($field, $value)
    {
        switch ($field) {
            case 'activity':
                return preg_match('/^([0-9]|[a-zA-Z]|_){1,20}$/', $value);
                break;
            case 'maintainer':
                return preg_match('/^([0-9]|[a-zA-Z]|_){1,20}$/', $value);
                break;
            case 'day':
                return preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $value);
                break;
            case 'start_time':
                return preg_match('/^[0-9]{2}:[0-9]{2}$/', $value);
                break;
            case 'end_time':
                return preg_match('/^[0-9]{2}:[0-9]{2}$/', $value);
                break;
            default:
                return false;
        }
    }
}
