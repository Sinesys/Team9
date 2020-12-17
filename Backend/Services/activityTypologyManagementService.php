<?php
require_once(__DIR__.'/../DBModels/activityTypologyModel.php');
require_once('servicesExceptions.php');

class ActivityTypologyManagementService{
    private $db;
    private $activityTypologyModel;

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
        $this->activityTypologyModel = new ActivityTypologyModel($this->db);
    }
    
    /**
     * Returns all the typologies' infos
     *
     * @return array an array of associative arrays.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getActivityTypologies(){
        return $this->activityTypologyModel->getAll();
    }

    /**
     * Returns all the infos of the typology
     * 
     * @param string $typologyid the typology's id.
     * @return array an associative arrays.
     * 
     * @throws WrongDataException if the typology doesn't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getActivityTypology($activityTypologyid){
        $typology = $this->activityTypologyModel->get($activityTypologyid);
        if(!$typology)
            throw new WrongDataException();
        return $typology;
    }

    /**
     * Inserts a typology
     *
     * @param string $typologyid the typology's id.
     * @param string $description the typology's description. It can be empty.
     * @return void
     * 
     * @throws WrongDataException if the typology is already present.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insertActivityTypology($typologyid, $description){
        if($this->activityTypologyModel->get($typologyid)!=false)
            throw new WrongDataException();
        $this->activityTypologyModel->insert($typologyid, $description);
    }

    /**
     * Inserts a typology
     *
     * @param string $typologyid the typology to be deleted.
     * @return void
     * 
     * @throws WrongDataException if the typology doens't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function deleteActivityTypology($typologyid){
        if (!$this->activityTypologyModel->delete($typologyid))
            throw new WrongDataException();
    }

    /**
     * Edits a typology
     *
     * @param string $typologyid the typology to edit.
     * @param string $description the typology's description. It can be empty.
     * @return void
     * 
     * @throws WrongDataException if the typology doens't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function editActivityTypology($typologyid, $description){
        if (!$this->activityTypologyModel->update($typologyid, $description))
            throw new WrongDataException();
    }

}