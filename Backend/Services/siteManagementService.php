<?php
require_once(__DIR__.'/../DBModels/siteModel.php');
require_once('servicesExceptions.php');

class SiteManagementService{
    private $db;
    private $siteModel;

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
        $this->siteModel = new SiteModel($this->db);
    }

    /**
     * Returns all the sites' infos
     *
     * @return array an array of associative arrays.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    
    public function getSites(){
        return $this->siteModel->getAll();
    }

    
    /**
     * Returns all the infos of the site
     * 
     * @param string $siteid the site's id.
     * @return array an associative arrays.
     * 
     * @throws WrongDataException if the site doesn't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getSite($siteid){
        $site = $this->siteModel->get($siteid);
        if(!$site)
            throw new WrongDataException();
        return $site;
    }

    /**
     * Inserts a site
     *
     * @param string $siteid the site's id.
     * @param string $description the site's description. It can be empty.
     * @return void
     * 
     * @throws WrongDataException if the site is already present.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insertSite($siteid, $area, $department){
        if($this->siteModel->get($siteid)!=false)
            throw new WrongDataException();
        $this->siteModel->insert($siteid, $area, $department);
    }

    /**
     * Inserts a site
     *
     * @param string $siteid the site to be deleted.
     * @return void
     * 
     * @throws WrongDataException if the site doens't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function deleteSite($siteid){
        if (!$this->siteModel->delete($siteid))
            throw new WrongDataException();
    }

    /**
     * Edits a site
     *
     * @param string $siteid the site to edit.
     * @param string $description the site's description. It can be empty.
     * @return void
     * 
     * @throws WrongDataException if the site doens't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function editSite($siteid, $area, $department){
        if (!$this->siteModel->update($siteid, $area, $department))
            throw new WrongDataException();
    }

}