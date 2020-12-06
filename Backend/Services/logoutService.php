<?php
require_once($_SERVER['DOCUMENT_ROOT'].'../libs/DBConnection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'../libs/utils.php');

class LogoutService{
    private $db;
    private $host = 'localhost';
    private $port = '5432';

    public function __construct()
    {
        $this->db = new Connection($this->host, $this->port, 'SEProject', 'se_user','team9user');
    }

    public function post(){
        $token = getallheaders()["Authorization"];
                
        $check = $this->db->delete('authentication', array("token"=>$token));
        
        if ($check == FALSE){
            status(500, 'Server problem (auth). Retry later.');
        }

        status(200);
    }
    
}