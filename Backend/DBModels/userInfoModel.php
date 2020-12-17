<?php
require_once('abstractModel.php');
require_once('dbExceptions.php');

class UserInfoModel extends AbstractModel
{

    private $alias =[
        'user_id' => 'userid', 
        'name' => null,
        'surname' => null, 
        'email' => null, 
        'phone_number' => 'phonenumber',
        'birthdate' => null
    ];

    /**
     * Get the user's info from the given id
     *
     * @param string $id the id of the user that must be searched. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return array an associative array composed by the keys: 
     * ['userid', 'name', 'surname', 'email', 'phonenumber', 'birthdate']
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function get($id)
    {
        return parent::getSingleAs($this->alias, ['user_id' => $id]);
    }

    /**
     * Insert the user's info
     *
     * @param string $id the id of the user that must be insert. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $name the name of the user. Must contains only alphanumeric chars and the lenght must be greater than 0 and less than 21;
     * @param string $surname the surname of the user. Must contains only alphanumeric chars and the lenght must be greater than 0 and less than 31;
     * @param string $email the email of the user. Must have a correct email format.
     * @param string, $phonenumber the phonenumber of the user. Must contains only numbers and the lenght must be greater than 0 and less than 11;
     * @param string $birthdate the birthdate of the user.* Must be in this form: 'YYYY-MM-DD';
     *
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insert($id, $name, $surname, $email, $phonenumber, $birthdate)
    {
        $values = [
            'user_id' => $id,
            'name' => $name,
            'surname' => $surname,
            'email' => $email,
            'phone_number' => $phonenumber,
            'birthdate' => $birthdate
        ];
        return parent::genericInsert($values);
    }

    /**
     * Update the user's info
     *
     * @param string $id the id of the user that must be updated. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $name the name of the user. Must contains only alphanumeric chars and the lenght must be greater than 0 and less than 21;
     * @param string $surname the surname of the user. Must contains only alphanumeric chars and the lenght must be greater than 0 and less than 31;
     * @param string $email the email of the user. Must have a correct email format.
     * @param string, $phonenumber the phonenumber of the user. Must contains only numbers and the lenght must be greater than 0 and less than 11;
     * @param string $birthdate the birthdate of the user.* Must be in this form: 'YYYY-MM-DD';
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */

    public function update($id, $name, $surname, $email, $phonenumber, $birthdate)
    {
        $values = [
            'name' => $name,
            'surname' => $surname,
            'email' => $email,
            'phone_number' => $phonenumber,
            'birthdate' => $birthdate
        ];

        return parent::genericUpdate($values, ['user_id' => $id]);
        
    }

    protected function getTableName()
    {
        return "user_info";
    }
    
    protected function validateField($field)
    {
        return in_array($field,['name', 'email', 'surname', 'phone_number', 'birthdate', 'user_id']);
    }

    protected function validate($field, $value)
    {
        switch ($field) {
            case 'name':
                return preg_match('/^[A-Za-z]{1,20}$/', $value);
                break;
            case 'surname':
                return preg_match('/^[A-Za-z]{1,30}$/', $value);
                break;
            case 'birthdate':
                return preg_match('/^\\d{4}-\\d{2}-\\d{2}$/', $value);
                break;
            case 'phone_number':
                return preg_match('/^[0-9]{1,15}$/', $value);
                break;
            case 'email':
                return preg_match('/^([A-Za-z]|[0-9]|\\.|_|-)+\\@([A-Za-z]|[0-9]|\.)+\.[A-Za-z]+$/', $value);
                break;
            case 'user_id':
                return preg_match('/^([0-9]|[a-zA-Z]|_){1,20}$/', $value);
                break;
            default:
                return false;
        }
    }
    
}