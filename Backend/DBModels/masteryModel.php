<?php
require_once('abstractModel.php');
require_once('dbExceptions.php');

class MasteryModel extends AbstractModel
{
    /**
     * Get the competences of the given maintainer
     *
     * @param string $id the id of the maintainer. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return array an array of competences' id.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function get($id){
        $results = parent::getAs(['competence'=>null], ['maintainer'=>$id]);
        if(!$results)
            return [];
        $competences = [];
        foreach($results as $result)
            $competences[] = $result;
        return $competences;
    }    

    /**
     * Insert a competence for to the given maintainer
     *
     * @param string $maintainer the id of the maintainer:
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $competence the id of the competence.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insert($maintainer, $competence)
    {
        return parent::genericInsert(['maintainer' => $maintainer, 'competence' => $competence]);
    }

    /**
     * Insert a set of competences for to the given maintainer
     *
     * @param string $maintainer the id of the maintainer:
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param array $competences the array of competences.
     * Each competence must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insertAll($maintainer, $competences)
    {
        foreach($competences as $competence)
            $this->insert($maintainer, $competence);
        return true;
    }

    /**
     * Update the set of competences for the given maintainer
     *
     * @param string $maintainer the id of the maintainer:
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param array $competences the array of competences.
     * Each competence must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * 
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function update($maintainer, $competences){
        parent::genericDelete(['maintainer' => $maintainer]);
        return $this->insertAll($maintainer, $competences);
    }
    
    protected function getTableName()
    {
        return "mastery";
    }

    protected function validateField($field)
    {
        return in_array($field,['maintainer', 'competence']);
    }

    protected function validate($field, $value)
    {
        switch($field){
            case 'maintainer':
                return preg_match('/^([0-9]|[a-zA-Z]|_){1,20}$/', $value);
                break;
            case 'competence':
                return preg_match('/^([0-9]|[a-zA-Z]|_){1,20}$/', $value);
                break;
            default:
                return false;
        }
    }

    
}
