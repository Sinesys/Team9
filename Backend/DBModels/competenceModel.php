<?php
require_once('abstractModel.php');
require_once('dbExceptions.php');

class CompetenceModel extends AbstractModel
{
    private $alias=[
        'competence_id' => 'competenceid', 
        'name' => null
    ];

    /**
     * Get all the competences
     *
     * @return array an array of associative arrays composed by the keys:
     * ['competenceid', 'name']
     * 
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getAll() {
        return parent::getAllAs($this->alias);
    }

    /**
     * Get the info of the given competence
     *
     * @param string $competenceid the id of competence. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return array an associative array composed by the keys: 
     * ['competenceid', 'name']
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function get($competenceid) {
        return parent::getSingleAs($this->alias, ['competence_id'=>$competenceid]);
    }

    /**
     * Get the info of the given competences. 
     * 
     * If more competences must be retrieved, this method is preferred over multiple calls to 'get($competence)'
     *
     * @param array $competences an array of competences. 
     * Each competence must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return array an array of associative arrays composed by the keys: 
     * ['competenceid', 'name']
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getMultiple($competences) {
        if(count($competences)==0)
            return [];
        foreach($competences as $competence)
            if(!$this->validate('competence_id', $competence))
                throw new WrongDataFormatException();
        $in_statement = str_repeat('?,', count($competences) - 1) . '?';
        $stmt = $this->db->prepare("SELECT competence_id as competenceid, name FROM ". $this->getTableName() ." WHERE competence_id IN ($in_statement)");
        $result = $stmt->execute($competences);
        if($result===false)
            throw new ServerErrorException();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if the competence exists
     * 
     * @param array $competence the competence. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return boolean true if exists, otherwhise false.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function exists($competenceid){
        if(!$this->validate('competence_id', $competenceid))
            throw new WrongDataFormatException();
        $stmt = $this->db->prepare("SELECT *
                                    FROM ". $this->getTableName() ."
                                    WHERE competence_id = ?;");
        $result = $stmt->execute([$competenceid]);
        if($result===false)
            throw new ServerErrorException();
        return ($stmt->rowCount()==1);
    }

    /**
     * Check if the all the given competences exist
     * 
     * @param array $competences an array of competences. 
     * Each competence must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return boolean true if all the competences exist, otherwhise false.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function existAll($competences){
        foreach($competences as $competence){
            if(!$this->exists($competence))
                return false;
        }
        return true;
    }

    /**
     * Insert a competence
     *
     * @param string $competenceid the id of the competence.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $name the name of the competence.
     * The lenght must be greater than 0 and less than 51.
     * 
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insert($competenceid, $name){
        return parent::genericInsert(['competence_id' => $competenceid, 'name' => $name]);
    }
    
    /**
     * Delete a competence
     * 
     * @param string $competenceid the id of the competence.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * 
     * @return boolean return true if at least one row has been deleted, false if no lines have been deleted.
     * 
     * @throws WrongDataFormatException if the parameters is in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function delete($competenceid)
    {
        return parent::genericDelete(['competence_id'=> $competenceid]);
    }

    /**
     * Update a competence
     *
     * @param string $competenceid the id of the competence that must be updated.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $name the name of the competence.
     * The lenght must be greater than 0 and less than 51.
     * 
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function update($competenceid, $name)
    {
        return parent::genericUpdate(['name' => $name], ['competence_id' => $competenceid]);
    }

    protected function validate($field, $value){
        switch ($field) {
            case 'competence_id':
                return preg_match('/^([0-9]|[a-zA-Z]|_){1,20}$/', $value);
                break;
            case 'name':
                return preg_match('/^(.){1,50}$/', $value);
                break;
            default:
                return false;
        }
    }

    protected function validateField($field)
    {
        return in_array($field,['competence_id', 'name']);
    }

    protected function getTableName()
    {
        return "competence";
    }

}