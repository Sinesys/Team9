<?php
require_once($_SERVER['DOCUMENT_ROOT'].'../libs/DBConnection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'../libs/utils.php');

class AccessLogService{
    private $db;

    public function __construct($host, $port){
        $this->host = $host;
        $this->port = $port;
        $this->db = new Connection($this->host, $this->port, 'SEProject', 'se_user','team9user');
    }

    public function get(){
        if (authorization($this->db)!='ADM')
            status(403, "Unauthorized");
                      
        $result = $this->db->query("SELECT user_id as userid, access_time as accesstime FROM access_log ORDER BY access_time DESC") or status(500, 'Internal Server Error');  

        $result = pg_fetch_all($result);
        if ($result==false)
            $result = array();
        
        echo json_encode($result);
        status(200);
    }

}