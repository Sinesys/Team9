<?php 
require_once('utils.php');
class Connection{
    private $db;

    public function __construct($host, $port, $dbname, $username, $password){
        $string = "host=$host port=$port dbname=$dbname user=$username password=$password";
        $this->db = pg_connect($string);
        if ($this->db == FALSE){
            status(503, 'Database connection failed!');
            return;
        }
    }

    public function insert($table, $data) {
        return (pg_insert($this->db, $table, $data)!=FALSE);
    }

    public function query($q){
        return pg_query($this->db, $q);
    }

    public function queryParams($query, $params, $return_false = false){
        $result = pg_query_params($this->db, $query, $params);
        if ($result == FALSE)
            return false;
        if($return_false and (pg_num_rows($result)==0))
            return false;
        return $result;
    }
    
    public function select($table, $condition, $return_false = false){
        $result = pg_select($this->db, $table, $condition);
        return $result;
    }

    public function update($table, $data, $condition){
        return pg_update($this->db, $table, $data, $condition);
    }

    public function select_update($table, $data, $condition){
        $result = pg_select($this->db, $table, $condition);
        if ($result!=false)
            return (pg_update($this->db, $table, $data, $condition)!=FALSE) ;
        return false;
    }

    public function delete($table, $condition){
        return pg_delete($this->db, $table, $condition);
    }
    
    public function select_delete($table, $condition){
        $result = pg_select($this->db, $table, $condition);
        if ($result!=false)
            return pg_delete($this->db, $table, $condition);

        return false;
    }
    
    
}
?>