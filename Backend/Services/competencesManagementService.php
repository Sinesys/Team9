<?php
require_once($_SERVER['DOCUMENT_ROOT'].'../libs/DBConnection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'../libs/utils.php');

class CompetencesManagementService{
    private $db;
  
    public function __construct($host, $port){
        $this->host = $host;
        $this->port = $port;
        $this->db = new Connection($this->host, $this->port, 'SEProject', 'se_user','team9user');
    }
    
    public function get(){
        if (authorization($this->db)!='ADM')
            status(401, "Unauthorized");

        $result = $this->db->query("SELECT name FROM competence");
        if($result == FALSE)
            status(500, 'Internal Server Error');  

        $competences = array();
        while($row=pg_fetch_row($result))
                $competences[] = $row[0];
        
        echo json_encode($competences); 
        status(200);
    }

}