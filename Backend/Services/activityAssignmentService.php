<?php
require_once($_SERVER['DOCUMENT_ROOT'].'../libs/DBConnection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'../libs/utils.php');

class ActivityAssignementService{
    private $db;
    private $host = 'localhost';
    private $port = '5432';

    public function __construct()
    {
        $this->db = new Connection($this->host, $this->port, 'SEProject', 'se_user','team9user');
    }
    
    public function post($jsoninfo, $id){
        $rules = array(
            "userid" => '/^([0-9]|[a-zA-Z]|_){1,20}$/',
        );
        
        $postinfo = check_json_string($jsoninfo, $rules);
        if (authorization($this->db)!='PLN')
            status(401, "Non sei autenticato per registrare un utente");

        $result = $this->db->insert('assignment', array("activity"=>$id, "maintainer"=>$postinfo["userid"]));
        if ($result!=true) {
            status(403, "Non riesco ad assegnare l'attività");
        }

        status(200, "Attività assegnata");
    }

    public function delete($id){
        if (authorization($this->db)!='PLN')
            status(401, "Non sei autorizzato per cancellare un assegnamento.");

        $result = $this->db->select_delete('assignment', array("activity"=>$id));
        if ($result===false)
            status(500, "Errore del server nel cancellare l'assegnazione.");

        status(200, "L'attività '" . $id . "' non è più assegnata!");
    }



}