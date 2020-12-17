<?php
require_once('abstractModel.php');
require_once('dbExceptions.php');

class AccessLogModel extends AbstractModel
{
    /**
     * Get all the accesses.
     *
     * @return array an array of associative array composed by the keys: 
     * ['userid', 'accesstime']
     * 
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getAll()
    {
        return parent::getAllAs(['user_id'=>'userid','access_time'=>'accesstime']);
    }

    protected function getTableName()
    {
        return "access_log";
    }
    
    protected function validate($field, $value)
    {
        return true;
    }

    protected function validateField($field)
    {
        return in_array($field,['user_id', 'access_time']);
    }
}
