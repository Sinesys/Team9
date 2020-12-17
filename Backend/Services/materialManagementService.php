<?php
require_once(__DIR__.'/../DBModels/materialModel.php');
require_once('servicesExceptions.php');

class MaterialManagementService{
    private $db;
    private $materialModel;

    /**
     * Create an istance of the service.
     * 
     * The service create a connection with a database, so all the info related to this connection must be provided.
     *
     * @param string $host the host of the database management system.
     * @param string $port the port of the database management system.
     * @param string $dbname the database name.
     * @param string $username the username for accessing to the database.
     * @param string $password  the username for accessing to the database.
     */
    public function __construct($host, $port, $dbname, $username, $password)
    {
        $this->db = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $this->materialModel = new MaterialModel($this->db);
    }
    /**
     * Returns all the materials' infos
     *
     * @return array an array of associative arrays.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    
    public function getMaterials(){
        return $this->materialModel->getAll();
    }

    /**
     * Returns all the infos of the material
     * 
     * @param string $materialid the material's id.
     * @return array an associative arrays.
     * 
     * @throws WrongDataException if the material doesn't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getMaterial($materialid){
        $material = $this->materialModel->get($materialid);
        if(!$material)
            throw new WrongDataException();
        return $material;
    }

    /**
     * Inserts a material
     *
     * @param string $materialid the material's id.
     * @param string $description the material's description. It can be empty.
     * @return void
     * 
     * @throws WrongDataException if the material is already present.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insertMaterial($materialid, $name){
        if($this->materialModel->exists($materialid))
            throw new WrongDataException();
        $this->materialModel->insert($materialid, $name);
    }

    /**
     * Inserts a material
     *
     * @param string $materialid the material to be deleted.
     * @return void
     * 
     * @throws WrongDataException if the material doens't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function deleteMaterial($materialid){
        if (!$this->materialModel->delete($materialid))
            throw new WrongDataException();
    }

    /**
     * Edits a material
     *
     * @param string $materialid the material to edit.
     * @param string $description the material's description. It can be empty.
     * @return void
     * 
     * @throws WrongDataException if the material doens't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */

    public function editMaterial($materialid, $name){
        if (!$this->materialModel->update($materialid, $name))
            throw new WrongDataException();
    }

}