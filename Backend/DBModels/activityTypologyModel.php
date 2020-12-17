<?php
require_once('abstractModel.php');
require_once('dbExceptions.php');

class ActivityTypologyModel extends AbstractModel
{
    private $alias =[
        'typology_id' => 'typologyid', 
        'description' => null
    ];

    /**
     * Get all the info of all the typologies
     *
     * @return array an array of associative arrays composed by the keys:
     * ['typologyid', 'description']
     * 
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getAll() {
        return parent::getAllAs($this->alias);
    }

    /**
     * Get the activity's typology info from the given id
     *
     * @param string $typologyid the id of the activity's typology that must be searched. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return array an associative array composed by the keys: 
     * ['typologyid', 'description']
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function get($typologyid) {
        return parent::getSingleAs($this->alias, ['typology_id'=>$typologyid]);
    }

    
    /**
     * Insert an activity's typology
     *
     * @param string $typologyid the id of the activity's typology.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $description the description of the activity. Can be null;
     * 
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insert($typologyid, $description){
        return parent::genericInsert(['typology_id' => $typologyid, 'description' => $description]);
    }

    /**
     * Delete an activity's typology
     * 
     * @param string $typologyid the id of the activity's typology that must be deleted. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * 
     * @return boolean return true if at least one row has been deleted, false if no lines have been deleted.
     * 
     * @throws WrongDataFormatException if the parameters is in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function delete($typologyid)
    {
        return parent::genericDelete(['typology_id' => $typologyid]);
    }

    /**
     * Update an activity's typology
     *
     * @param string $typologyid the id of the activity's typology that must be updated. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $description the description of the activity. Can be null;
     * 
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function update($typologyid, $description)
    {
        return parent::genericUpdate(['description' => $description], ['typology_id' => $typologyid]);
    }

    protected function validateField($field)
    {
        return in_array($field,['typology_id', 'description']);
    }

    protected function validate($field, $value){
        switch ($field) {
            case 'typology_id':
                return preg_match('/^([0-9]|[a-zA-Z]|_){1,20}$/', $value);
                break;
            case 'description':
                return true;
                break;
            default:
                return false;
        }
    }

    protected function getTableName()
    {
        return "activity_typology";
    }

}




