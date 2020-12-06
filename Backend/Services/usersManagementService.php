<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '../libs/DBConnection.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '../libs/utils.php');

class UsersManagementService
{
    private $db;

    public function __construct($host, $port)
    {
        $this->host = $host;
        $this->port = $port;
        $this->db = new Connection($this->host, $this->port, 'SEProject', 'se_user', 'team9user');
    }

    public function post($jsoninfo)
    {
        $rules = array(
            "name" => '/^[A-Za-z]{1,20}$/',
            "surname" => '/^[A-Za-z]{1,30}$/',
            "birthdate" => '/^\\d{4}-\\d{2}-\\d{2}$/',
            "phonenumber" => '/^[0-9]{1,15}$/',
            "email" => '/^([A-Za-z]|[0-9]|\\.|_|-)+\\@([A-Za-z]|[0-9]|\.)+\.[A-Za-z]+$/',
            "userid" => '/^([0-9]|[a-zA-Z]){1,20}$/',
            "password" => '/^([A-Za-z]|[0-9]|\\.|_|-|!|\\?){1,50}$/',
            "role" => '/^(ADM|PLN|MNT|DBL)$/'
        );

        $postinfo = check_json_string($jsoninfo, $rules);

        if (authorization($this->db) != 'ADM')
            status(403, "Unauthorized");

        $forsystemuser = array(
            'id' => $postinfo['userid'],
            'password' => $postinfo['password'],
            'role' => $postinfo['role']
        );

        $foruserinfo = array(
            'user_id' => $postinfo['userid'],
            'name' => $postinfo['name'],
            'surname' => $postinfo['surname'],
            'email' => $postinfo['email'],
            'phone_number' => $postinfo['phonenumber'],
            'birthdate' => $postinfo['birthdate']
        );

        if ($forsystemuser['role'] == 'MNT') {
            array_key_exists('competences', $postinfo) or status(400, "Bad request");
            $this->check_competences($postinfo['competences']) or status(400, "Bad request");
        }

        $this->db->query("BEGIN") or status(500, "error");
        $result = $this->db->insert('system_user', $forsystemuser);

        if ($forsystemuser['role'] == 'MNT') {
            $result = $result and $this->db->insert('maintainer', array("user_id" => $forsystemuser['id']));
            $result = $result and $this->insert_competences($forsystemuser['id'], $postinfo['competences']);
        }
        $result = $result and $this->db->insert('user_info', $foruserinfo);

        if ($result == FALSE) {
            $this->db->query("ROLLBACK");
            status(400, "Bad request");
        }
        $this->db->query("COMMIT") or status(500, "error");
        
        status(200, "ok");
    }


    public function delete($id)
    {
        if (authorization($this->db) != 'ADM')
            status(403, "Unauthorized");

        $this->db->select_delete('system_user', array("id" => $id)) or status(500, "The user doesn't exist.");

        status(200, "The user '" . $id . "' has been removed!");
    }


    public function put($jsoninfo, $id)
    {
        $rules = array(
            "name" => '/^[A-Za-z]{1,20}$/',
            "surname" => '/^[A-Za-z]{1,30}$/',
            "birthdate" => '/^\\d{4}-\\d{2}-\\d{2}$/',
            "phonenumber" => '/^[0-9]{1,15}$/',
            "email" => '/^([A-Za-z]|[0-9]|\\.|_|-)+\\@([A-Za-z]|[0-9]|\.)+\.[A-Za-z]+$/',
            "password" => '/^([A-Za-z]|[0-9]|\\.|_|-|!|\\?){0,50}$/'
        );

        $postinfo = check_json_string($jsoninfo, $rules);

        if (authorization($this->db) != 'ADM')
            status(403, "Unauthorized");

        $is_mantainer = ($this->db->select('maintainer', array('user_id' => $id))!=FALSE);
        
        if ($is_mantainer) {
            array_key_exists('competences', $postinfo) or status(400, "Bad request");
            $this->check_competences($postinfo['competences']) or status(400, "Bad request");
        }

        $this->db->query("BEGIN") or status(500);
        $result = true;
        if ($postinfo['password'] != "")
            $result = $this->db->select_update('system_user', array('password' => $postinfo['password']), array('id' => $id));
        if ($is_mantainer)
           $result = $result and $this->update_competences($id, $postinfo['competences']);
        $result = $result and $this->db->select_update('user_info', array(
            'name' => $postinfo['name'],
            'surname' => $postinfo['surname'],
            'email' => $postinfo['email'],
            'phone_number' => $postinfo['phonenumber'],
            'birthdate' => $postinfo['birthdate']
        ), array('user_id' => $id));

        if ($result == FALSE) {
            $this->db->query("ROLLBACK");
            status(400, "Bad request");
        }

        $this->db->query("COMMIT") or status(500, "error");
        status(200, "The user '" . $id . "' has been updated!");

        
    }

    public function get($id = null)
    {
        if (authorization($this->db) != 'ADM') {
            if ($id == null)
                status(403, "You can't do that.");
            $this->db->select('authentication', array("token" => getallheaders()["Authorization"], "id" => $id), true) or status(403, "You can't do that.");
        }

        $sql = "SELECT user_id as userid, name, surname, email, phone_number as phonenumber, birthdate, role
                FROM user_info, system_user
                WHERE (user_info.user_id = system_user.id)";
        $params = array();

        if ($id != null) {
            $sql = "SELECT user_id as userid, name, surname, email, phone_number as phonenumber, birthdate, role
                    FROM user_info, system_user 
                    WHERE (user_info.user_id = system_user.id AND user_info.user_id = $1)";

            $params['user_id'] = $id;
        }
        $result = $this->db->queryParams($sql, $params) or status(500, 'Internal Server Error');

        $result = pg_fetch_all($result);
        if ($result == false)
            $result = array();

        foreach ($result as $key => $value) {
            if ($value['role'] != 'MNT')
                continue;

            $result_query = $this->db->select('mastery', array('maintainer' => $value['userid']));
            $result[$key]['competences'] = array();
            
            if ($result_query === FALSE)
                break;
                
            foreach ($result_query as $single_result)
                $result[$key]['competences'][] = $single_result['competence'];
        }

        if ($id != null)
            echo json_encode($result[0]);
        else
            echo json_encode($result);

        status(200);
    }

    private function check_competences($competences)
    {
        foreach ($competences as $competence){
            if ($this->db->select('competence', array("name" => $competence)) == false)
                return false;
            }
        return true;
    }

    private function insert_competences($id, $competences)
    {
        foreach ($competences as $competence)
            if ($this->db->insert('mastery', array("maintainer" => $id, "competence" => $competence)) == FALSE)
                return false;
        return true;
    }

    private function update_competences($id, $competences)
    {
        $result = ($this->db->delete("mastery", array("maintainer" => $id)) != FALSE);
        return $result and $this->insert_competences($id, $competences);
    }
}
