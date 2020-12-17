<?php
require_once(__DIR__.'/../DBModels/competenceModel.php');
require_once('servicesExceptions.php');

class CompetencesManagementService{
    private $db;
    private $competencesModel;

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
        $this->competencesModel = new CompetenceModel($this->db);
    }

    /**
     * Returns all the competences' infos
     *
     * @return array an array of associative arrays.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getCompetences(){
        return $this->competencesModel->getAll();
    }
    
    /**
     * Returns all the infos of the competence
     * 
     * @param string $competenceid the competence's id.
     * @return array an associative arrays.
     * 
     * @throws WrongDataException if the competence doesn't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getCompetence($competenceid){
        $competence = $this->competencesModel->get($competenceid);
        if(!$competence)
            throw new WrongDataException();
        return $competence;
    }

    /**
     * Inserts a competence
     *
     * @param string $competenceid the competence's id.
     * @param string $description the competence's description. It can be empty.
     * @return void
     * 
     * @throws WrongDataException if the competence is already present.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insertCompetence($competenceid, $name){
        if($this->competencesModel->exists($competenceid))
            throw new WrongDataException();
        $this->competencesModel->insert($competenceid, $name);
    }

    /**
     * Inserts a competence
     *
     * @param string $competenceid the competence to be deleted.
     * @return void
     * 
     * @throws WrongDataException if the competence doens't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function deleteCompetence($competenceid){
        if (!$this->competencesModel->delete($competenceid))
            throw new WrongDataException();
    }

    /**
     * Edits a competence
     *
     * @param string $competenceid the competence to edit.
     * @param string $description the competence's description. It can be empty.
     * @return void
     * 
     * @throws WrongDataException if the competence doens't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function editCompetence($competenceid, $name){
        if (!$this->competencesModel->update($competenceid, $name))
            throw new WrongDataException();
    }

}