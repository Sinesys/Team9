<?php
require_once($_SERVER['DOCUMENT_ROOT'].'../libs/DBConnection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'../libs/utils.php');

class LoginService{
    private $db;
    private $host = 'localhost';
    private $port = '5432';

    public function __construct()
    {
        $this->db = new Connection($this->host, $this->port, 'SEProject', 'se_user','team9user');
    }
    
    public function post($jsoninfo){
        $rules = array(
            "id" => '/^([0-9]|[a-zA-Z]|_){1,20}$/',
            "password" => '/^([A-Za-z]|[0-9]|\\.|_|-|!|\\?){1,50}$/',
        );

        $postinfo = check_json_string($jsoninfo, $rules);
        
        $sql = "SELECT role
                FROM system_user
                WHERE (ID=$1 AND PASSWORD=$2)";
        
        $result = $this->db->queryParams($sql, array($postinfo['id'],$postinfo['password']));
        
        if ($result == FALSE){
            status(403, 'The credentials are not correct.');
        }
        if (@pg_num_rows($result)==0){
            status(403, 'The credentials are not correct.');
        }

        $token = str_replace('"','x',uniqid("",true));
        $this->db->delete('authentication', array("id"=>$postinfo['id']));
        $check = $this->db->insert('authentication', array("id"=>$postinfo['id'], "token"=>$token));
        
        if ($check == FALSE){
            status(500, 'Server problem (auth). Retry later.');
        }

        $result = pg_fetch_assoc($result);

        echo json_encode([
            'role'=>$result['role'],
            'auth_token' => $token 
            ]);
        status(200);
    }

}