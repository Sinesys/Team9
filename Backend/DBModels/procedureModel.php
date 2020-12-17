<?php
require_once('abstractModel.php');
require_once('dbExceptions.php');

class ProcedureModel extends AbstractModel
{
    private $alias =[
        'procedure_id' => 'procedureid', 
        'description' => null
    ];

    /**
     * Get all the procedures
     *
     * @return array an array of associative arrays composed by the keys:
     * ['procedureid', 'description']
     * 
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getAll() {
        return parent::getAllAs($this->alias);
    }

    /**
     * Get the info of the given procedure
     *
     * @param string $procedureid the id of procedure. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return array an associative array composed by the keys: 
     * ['procedureid', 'description']
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function get($procedureid) {
        return parent::getSingleAs($this->alias, ['procedure_id'=>$procedureid]);
    }

    /**
     * Insert a procedure
     *
     * @param string $procedureid the id of the procedure.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $description the description of the procedure.
     * 
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */

    public function insert($procedureid, $description){
        return parent::genericInsert(['procedure_id' => $procedureid, 'description' => $description]);
    }

    /**
     * Delete a procedure
     * 
     * @param string $procedureid the id of the procedure.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * 
     * @return boolean return true if at least one row has been deleted, false if no lines have been deleted.
     * 
     * @throws WrongDataFormatException if the parameters is in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */    
    public function delete($procedureid)
    {
        return parent::genericDelete(['procedure_id' => $procedureid]);
    }

    /**
     * Update a procedure
     *
     * @param string $procedureid the id of the procedure that must be updated.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $description the description of the procedure.
     * 
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */

    public function update($procedureid, $description)
    {
        return parent::genericUpdate(['description' => $description], ['procedure_id' => $procedureid]);
    }

    protected function validateField($field)
    {
        return in_array($field,['procedure_id', 'description']);
    }

    protected function validate($field, $value){
        switch ($field) {
            case 'procedure_id':
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
        return "procedure";
    }

}