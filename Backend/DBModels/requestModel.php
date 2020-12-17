<?php
require_once('abstractModel.php');
require_once('dbExceptions.php');

class RequestModel extends AbstractModel
{
    /**
     * Get the competences of the given procedure
     *
     * @param string $procedure the id of the procedure. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return array an array of competences' id.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function get($procedure){
        $results = parent::getAs(['competence'=>null], ['procedure'=>$procedure]);
        if(!$results)
            return [];
        $competences = [];
        foreach($results as $result)
            $competences[] = $result['competence'];
        return $competences;
    }

    /**
     * Insert a competence for to the given procedure
     *
     * @param string $procedure the id of the procedure:
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $competence the id of the competence.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insert($procedure, $competence){
        return parent::genericInsert(['procedure' => $procedure, 'competence' => $competence]);
    }

    /**
     * Insert a set of competences for to the given procedure
     *
     * @param string $procedure the id of the procedure:
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param array $competences the array of competences.
     * Each competence must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insertAll($procedure, $competences)
    {
        foreach($competences as $competence)
            $this->insert($procedure, $competence);
    }


    /**
     * Update the set of competences for the given procedure
     *
     * @param string $procedure the id of the procedure:
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param array $competences the array of competences.
     * Each competence must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * 
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function update($procedure, $competences){
        parent::genericDelete(['procedure' => $procedure]);
        $this->insertAll($procedure, $competences);
    }

    protected function validateField($field)
    {
        return in_array($field,['procedure', 'competence']);
    }

    protected function validate($field, $value){
        switch ($field) {
            case 'procedure':
                return preg_match('/^([0-9]|[a-zA-Z]|_){1,20}$/', $value);
                break;
            case 'competence':
                return preg_match('/^([0-9]|[a-zA-Z]|_){1,20}$/', $value);
                break;
            default:
                return false;
        }
    }

    protected function getTableName()
    {
        return "request";
    }

}