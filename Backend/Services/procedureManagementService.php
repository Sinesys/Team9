<?php
require_once(__DIR__ . '/../DBModels/procedureModel.php');
require_once(__DIR__ . '/../DBModels/competenceModel.php');
require_once(__DIR__ . '/../DBModels/requestModel.php');
require_once(__DIR__ . '/../DBModels/SMPModel.php');
require_once('servicesExceptions.php');

class ProcedureManagementService
{
    private $db;
    private $procedureModel;
    private $smppath;

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
    public function __construct($host, $port, $dbname, $username, $password, $smppath = './SMPFiles')
    {
        $this->db = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $this->smppath = $smppath;
        if (!is_dir($this->smppath))
            mkdir($this->smppath);
        $this->procedureModel = new ProcedureModel($this->db);
    }

    /**
     * Returns all the procedures' info
     *
     * @return array an array of associative arrays.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getProcedures()
    {
        $procedures = $this->procedureModel->getAll();
        $requestModel = new RequestModel($this->db);
        $smpModel = new SMPModel($this->db);
        foreach ($procedures as $index => $procedure) {
            $procedures[$index]['competencesrequired'] = $requestModel->get($procedure['procedureid']);
            $smpfile = $smpModel->get($procedure['procedureid']);
            $procedures[$index]['smpfile'] = !$smpfile ? false : true;
        }

        return $procedures;
    }

    /**
     * Returns all the procedures' info, with additional details.
     *
     * @return array an array of associative arrays.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getProceduresVerbose()
    {
        $procedures = $this->procedureModel->getAll();
        $requestModel = new RequestModel($this->db);
        $competenceModel = new CompetenceModel($this->db);
        $smpModel = new SMPModel($this->db);

        foreach ($procedures as $index => $procedure){
            $procedures[$index]['competencesrequired'] = $competenceModel->getMultiple($requestModel->get($procedure['procedureid']));
            $smpfile = $smpModel->get($procedure['procedureid']);
            $procedures[$index]['smpfile'] = !$smpfile ? false : true;
        }

        return $procedures;
    }

    /**
     * Returns all the procedure's info
     *
     * @param string $procedureid the procedure's id
     * @return array an associative arrays.
     * 
     * @throws WrongDataException if the procedure doesn't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getProcedure($procedureid)
    {
        $smpModel = new SMPModel($this->db);
        $procedure = $this->procedureModel->get($procedureid);
        if (!$procedure)
            throw new WrongDataException();

        $requestModel = new RequestModel($this->db);
        $competences = $requestModel->get($procedure['procedureid']);

        $smpModel = new SMPModel($this->db);
        $smpfile = $smpModel->get($procedure['procedureid']);
        $procedure['smpfile'] = !$smpfile ? false : true;

        $procedure['competencesrequired'] = $competences;
        return $procedure;
    }

    /**
     * Inserts a procedure
     * @param string $procedureid the procedure's id.
     * @param array $procedureinfo an associative array composed by the following keys:
     * ['description', 'competencesrequired']
     * @return void
     * 
     * @throws WrongDataException if the procedure is already present, or if the given competences doesn't exist.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insertProcedure($procedureid, $procedureinfo)
    {
        $competenceModel = new CompetenceModel($this->db);
        $requestModel = new RequestModel($this->db);

        if ($this->procedureModel->get($procedureid) != false)
            throw new WrongDataException();

        $this->db->beginTransaction();
        try {
            $this->procedureModel->insert($procedureid, $procedureinfo['description']);
            if (!$competenceModel->existAll($procedureinfo['competencesrequired'])) {
                $this->db->rollBack();
                throw new WrongDataException();
            }
            $requestModel->insertAll($procedureid, $procedureinfo['competencesrequired']);
        } catch (ServerErrorException | WrongDataFormatException $e) {
            $this->db->rollBack();
            throw $e;
        }

        $this->db->commit();
    }

    /**
     * Deletes a procedure
     *
     * @param string $procedureid the procedure's id.
     * @throws WrongDataException if the procedure doesn't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function deleteProcedure($procedureid)
    {
        try{
            $this->deleteSMPFile($procedureid);
        }catch(WrongDataException $e){}

        if (!$this->procedureModel->delete($procedureid))
            throw new WrongDataException();
    }

    /**
     * Edits an procedure
     *
     * @param string $procedureid the procedure's id.
     * @param array $activityinfo an associative array composed by the following keys:
     * ['description', 'competencesrequired']
     * @throws WrongDataException if the procedure doesn't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function editProcedure($procedureid, $procedureinfo)
    {
        $competenceModel = new CompetenceModel($this->db);
        $requestModel = new RequestModel($this->db);

        $this->db->beginTransaction();

        try {
            $result = ($this->procedureModel->update($procedureid, $procedureinfo['description']) and $competenceModel->existAll($procedureinfo['competencesrequired']));
            if (!$result) {
                $this->db->rollBack();
                throw new WrongDataException();
            }
            $requestModel->update($procedureid, $procedureinfo['competencesrequired']);
        } catch (ServerErrorException | WrongDataFormatException $e) {
            $this->db->rollBack();
            throw $e;
        }

        $this->db->commit();
    }

    /**
     * Inserts a SMP file, associated with the given procedure
     *
     * @param array $procedureid the procedure's id.
     * @param string $filename the name of the file to be created. It must contains also the exstension.
     * @param bytes $filecontent the file's content to be saved.
     * @return void
     * 
     * @throws WrongDataException if the procedure doesn't or the file is already present.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insertSMPFile($procedureid, $filename, $filecontent)
    {
        $smpModel = new SMPModel($this->db);

        if ($smpModel->get($procedureid) != false)
            throw new WrongDataException();

        if (!$this->procedureModel->get($procedureid))
            throw new WrongDataException();

        try {
            $new_file = fopen($this->smppath . '/' . $filename, "wb");
            fwrite($new_file,  $filecontent);
            $smpModel->insert($procedureid, realpath($this->smppath . '/' . $filename));
        } catch (Exception $e) {
            throw new ServerErrorException();
        }
    }

    /**
     * Deletes a SMP file
     *
     * @param array $procedureid the procedure's id.
     * @return void
     * 
     * @throws WrongDataException if the file doesn't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function deleteSMPFile($procedureid)
    {
        $smpModel = new SMPModel($this->db);
        $info = $smpModel->get($procedureid);

        if (!$info)
            throw new WrongDataException();

        unlink($info['path']);
        $smpModel->delete($procedureid);
    }

    /**
     * Returns the SMP file path
     *
     * @param array $procedureid the procedure's id.
     * @return string the SMP file's path.
     * 
     * @throws WrongDataException if the file doesn't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getSMPFile($procedureid)
    {
        $smpModel = new SMPModel($this->db);
        $result = $smpModel->get($procedureid);
        if(!$result)
            throw new WrongDataException();
        return $result['path'];
    }

}
