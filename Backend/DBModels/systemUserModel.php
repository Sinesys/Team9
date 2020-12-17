<?php
require_once('abstractModel.php');
require_once('dbExceptions.php');

class SystemUserModel extends AbstractModel
{

     /**
     * Get the infos related to the given user
     *
     * @param string $id the id of the user. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return array an associative array composed by the keys: 
     * ['id', 'password', 'role']
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function get($id){
        $result = parent::genericGet(['id' => $id]);
        if($result===false)
            return false;
        if(count($result)<=0)
            return false;
        return $result[0];
    }

    /**
     * Inserts a user
     * 
     * @param string $id the id of the user. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string password the password of the user. The length must be >0 and <51;     
     * @param string role the role of the user. It must be one of 'ADM', 'PLN', 'MNT', 'DBL';
     * @return boolean true if the user is inserted, otherwhise false.
     *
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insert($id, $password, $role){
        return parent::genericInsert(['id' => $id, 'password' => $password, 'role' => $role]);
    }

    /**
     * Updates a user
     * 
     * @param string $id the id of the user. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string password the password of the user. The length must be >0 and <51;     
     * 
     * @return boolean true if the user is uptaded, otherwhise false.
     *
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function update($id, $password){
        return parent::genericUpdate(['password' => $password], ['id' => $id]);
    }

    /**
     * Deletes a user
     * 
     * @param string $id the id of the user. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return boolean true if the user is deleted, otherwhise false.
     *
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function delete($id){
        return parent::genericDelete(['id' => $id]);
    }

    protected function validateField($field)
    {
        return in_array($field,['id', 'password', 'role']);
    }

    protected function validate($field, $value){
        switch($field){
            case 'id':
                return preg_match('/^([0-9]|[a-zA-Z]|_){1,20}$/', $value);
                break;   
            case 'password':
                return preg_match('/^([A-Za-z]|[0-9]|\\.|_|-|!|\\?){1,50}$/', $value);
                break;         
            case 'role':
                return in_array($value, array('ADM', 'PLN', 'MNT', 'DBL'));
                break;  
            default:
                return false;
        }
    }

    protected function getTableName()
    {
        return "system_user";
    }
    
}
