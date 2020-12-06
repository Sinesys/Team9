<?php
require_once($_SERVER['DOCUMENT_ROOT'].'../libs/DBConnection.php');
require_once($_SERVER['DOCUMENT_ROOT'].'../libs/utils.php');

class MantainerManagementService{
    private $db;
    private $host = 'localhost';
    private $port = '5432';

    public function __construct()
    {
        $this->db = new Connection($this->host, $this->port, 'SEProject', 'se_user','team9user');
    }


    public function get($id=null){
        if (authorization($this->db)!='PLN')
            status(401, "You can't do that.");

        $sql = "SELECT maintainer.user_id as userid, name, surname, email, phone_number as phonenumber, birthdate
                FROM user_info, maintainer
                WHERE (user_info.user_id = maintainer.user_id)";
        $params = array();
               
        if($id!=null){
            $sql = "SELECT maintainer.user_id as userid, name, surname, email, phone_number as phonenumber, birthdate
                    FROM user_info, maintainer 
                    WHERE (user_info.user_id = maintainer.user_id AND user_info.user_id = $1)";

            $params['user_id'] = $id;
        }
        $result = $this->db->queryParams($sql, $params);  

        if ($result == FALSE){
            status(403, 'No maintainer found');
        }
        if (@pg_num_rows($result)==0){
            status(403, 'No maintainer found');
        }

        $results = pg_fetch_all($result);
        foreach($results as $key=>$result){
            $sql = "SELECT competence 
                    FROM mastery 
                    WHERE (maintainer = $1)";
            $result_query = $this->db->queryParams($sql, array('maintainer'=>$result['userid']));
            $results[$key]['competences'] = array();
            while($row=pg_fetch_row($result_query))
                $results[$key]['competences'][] = $row[0];
        }        
               
        if($id!=null){
            echo json_encode($results[0]);
        }else{
            echo json_encode($results);
        }
        status(200);
    }


}