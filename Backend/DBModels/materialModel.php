<?php
require_once('abstractModel.php');
require_once('dbExceptions.php');

class MaterialModel extends AbstractModel
{
    private $alias =[
        'material_id' => 'materialid', 
        'name' => null
    ];


    /**
     * Get all the materials
     *
     * @return array an array of associative arrays composed by the keys:
     * ['materialid', 'name']
     * 
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getAll() {
        return parent::getAllAs($this->alias);
    }


    /**
     * Get the info of the given material
     *
     * @param string $materialid the id of material. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return array an associative array composed by the keys: 
     * ['materialid', 'name']
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function get($materialid) {
        return parent::getSingleAs($this->alias, ['material_id'=>$materialid]);
    }


    /**
     * Get the info of the given materials. 
     * 
     * If more materials must be retrieved, this method is preferred over multiple calls to 'get($material)'
     *
     * @param array $materials an array of materials. 
     * Each material must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return array an array of associative arrays composed by the keys: 
     * ['materialid', 'name']
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getMultiple($materials) {
        if(count($materials)==0)
            return [];
        foreach($materials as $material)
            if(!$this->validate('material_id', $material))
                throw new WrongDataFormatException();

        $in_statement = str_repeat('?,', count($materials) - 1) . '?';

        $stmt = $this->db->prepare("SELECT material_id as materialid, name FROM ". $this->getTableName() ." WHERE material_id IN ($in_statement)");
        $result = $stmt->execute($materials);
        if($result===false)
            throw new ServerErrorException();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    /**
     * Check if the material exists
     * 
     * @param array $material the material. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return boolean true if exists, otherwhise false.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */

    public function exists($materialid){
        if(!$this->validate('material_id', $materialid))
            throw new WrongDataFormatException();
        $stmt = $this->db->prepare("SELECT *
                                    FROM ". $this->getTableName() ."
                                    WHERE material_id = ?;");
        $result = $stmt->execute([$materialid]);
        if($result===false)
            throw new ServerErrorException();
        return ($stmt->rowCount()==1);
    }

    /**
     * Check if the all the given materials exist
     * 
     * @param array $materials an array of material. 
     * Each material must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return boolean true if all the materials exist, otherwhise false.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */

    public function existAll($materials){
        foreach($materials as $material){
            if(!$this->exists($material))
                return false;
        }
        return true;
    }


    /**
     * Insert a material
     *
     * @param string $materialid the id of the material.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $name the name of the material.
     * The lenght must be greater than 0 and less than 51.
     * 
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insert($materialid, $name){
        return parent::genericInsert(['material_id' => $materialid, 'name' => $name]);
       
    }
    
    /**
     * Delete a material
     * 
     * @param string $materialid the id of the material.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * 
     * @return boolean return true if at least one row has been deleted, false if no lines have been deleted.
     * 
     * @throws WrongDataFormatException if the parameters is in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function delete($materialid)
    {
        return parent::genericDelete(['material_id' => $materialid]);
        
    }


    /**
     * Update a material
     *
     * @param string $materialid the id of the material that must be updated.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $name the name of the material.
     * The lenght must be greater than 0 and less than 51.
     * 
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function update($materialid, $name)
    {
        return parent::genericUpdate(['name' => $name], ['material_id' => $materialid]);   
    }

    protected function validate($field, $value){
        switch ($field) {
            case 'material_id':
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
        return in_array($field,['material_id', 'name']);
    }


    protected function getTableName()
    {
        return "material";
    }

}