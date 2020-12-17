<?php
require_once('abstractModel.php');
require_once('dbExceptions.php');

class AuthenticationModel extends AbstractModel
{

    
    /**
     * Get the infos related to the given activity
     *
     * @param string $token the token. 
     * The lenght must be greater than 0 and less than 51;
     * @return array an associative array composed by the keys: 
     * ['id', 'token', 'expires']
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getByToken($token)
    {
        $result = parent::genericGet(["token" => $token]);
        if($result===false)
            return false;
        if(count($result)<=0)
            return false;
        return $result[0];
    }

    /**
     * Deletes an existing token
     *
     * @param string $token the token that must be deleted.
     * The lenght must be greater than 0 and less than 51;
     * @return boolean true if the token is deleted, otherwhise false.
     *
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function deleteToken($token){
        return parent::genericDelete(["token" => $token]);
    }

    /**
     * Create a new token for the given id (overwriting the old one if it exists).
     *
     * @param string $id the user's id.
     * @param string $token the new token.
     * @return boolean true if the token is replaced, otherwhise false.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function replaceToken($id, $token){
        parent::genericDelete(["id" => $id]);
        return parent::genericInsert(["id" => $id, "token" => $token]);
    }

    protected function getTableName()
    {
        return "authentication";
    }

    protected function validateField($field)
    {
        return in_array($field,['id', 'token']);
    }

    protected function validate($field, $value)
    {
        switch($field){
            case 'id':
                return preg_match('/^([0-9a-zA-Z_]){1,20}$/', $value);
                break;
            case 'token':
                return preg_match('/^(.){1,50}$/', $value);
                break;
            default:
                return false;
        }
    }
}
?>