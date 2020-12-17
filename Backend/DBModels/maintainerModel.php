<?php
require_once('abstractModel.php');
require_once('dbExceptions.php');

class MaintainerModel extends AbstractModel
{

    /**
     * Checks if the maintainer exists
     * 
     * @param string $id the maintainer's id. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return string the id of the maintainer if it exists, otherwhise false. 
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function get($id){
        $result = parent::genericGet(['user_id', $id]);
        if($result===false)
            return false;
        return $result['user_id'];
    }

    /**
     * Insert a maintainer
     *
     * @param string $id the id of the maintainer.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insert($id){
        return parent::genericInsert(['user_id' => $id]);
    }

    protected function validate($field, $value){
        switch($field){
            case 'user_id':
                return preg_match('/^([0-9]|[a-zA-Z]|_){1,20}$/', $value);
                break;
            default:
                return false;
        }
    }

    protected function validateField($field)
    {
        return in_array($field,['user_id']);
    }

    protected function getTableName()
    {
        return "maintainer";
    }

}