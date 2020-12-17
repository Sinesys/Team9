<?php
require_once('abstractModel.php');
require_once('dbExceptions.php');

class SMPModel extends AbstractModel{

    private $alias =[
        'procedure' => null, 
        'path' => null
    ];

    /**
     * Get the SMP's info for the given procedure.
     *
     * @param string $procedureid the id of the procedure. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return array an associative array composed by the keys: 
     * ['procedure', 'path']
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function get($procedureid) {
        return parent::getSingleAs($this->alias, ['procedure'=>$procedureid]);
    }

    /**
     * Insert a SMP for the given procedure
     *
     * @param string $procedureid the id of the procedure. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $path the path of the file
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insert($procedureid, $path) 
    {
        return parent::genericInsert(['procedure' => $procedureid, 'path' => $path]);
    }

    /**
     * Delete a SMP
     * 
     * @param string $procedureid the id of the SMP's procedure that must be deleted. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return boolean return true if at least one row has been deleted, false if no lines have been deleted.
     * 
     * @throws WrongDataFormatException if the parameters is in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function delete($procedureid)
    {
        return parent::genericDelete(['procedure' => $procedureid]);
    }

    protected function validateField($field)
    {
        return in_array($field,['procedure', 'path']);
    }

    protected function getTableName()
    {
        return "smp";
    }

    protected function validate($field, $value)
    {
        switch ($field) {
            case 'procedure':
                return preg_match('/^([0-9]|[a-zA-Z]|_){1,20}$/', $value);
                break;
            case 'path':
                return ((strlen($value) <= 256) and (strlen($value)>0));
                break;
            default:
                return false;
        }
    }

}