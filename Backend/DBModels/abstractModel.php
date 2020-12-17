<?php
require_once('dbExceptions.php');

abstract class AbstractModel
{
    protected $db;
    
    public function __construct($pgdb){
        $this->db = $pgdb;
    }
    
    /**
     * Get all the rows from the database, from the table name returned by the method 'getTableName()'.
     *
     * @return array an array of associative arrays 
     */
    public function getAll() {
        return $this->getAllAs("*");
    }

    protected function genericGet($conditions) {
        return $this->getAs("*", $conditions);
    }

    protected function getAllAs($select) {
        if($select!='*')
            $select = $this->buildSelect($select);
        if(!$select)
            throw new UnexpectedValueException();
        $result = $this->db->query("SELECT $select FROM ". $this->getTableName());
        if($result===false)
            throw new ServerErrorException();
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function getSingleAs($select, $conditions) {
        $result = $this->getAs($select, $conditions);
        if($result===false)
            return false;
        if(count($result)<=0)
            return false;
        return $result[0];
    }

    protected function getAs($select, $conditions) {
        if($select!='*')
            $select = $this->buildSelect($select);
        if(!$select)
            throw new UnexpectedValueException();
        if (!$this->multipleValidate($conditions))
            throw new WrongDataFormatException();
        $where = implode(' AND ', array_map(function($key){return "$key=:$key";},array_keys($conditions)));
        $stmt = $this->db->prepare("SELECT $select FROM ". $this->getTableName() ." WHERE $where");
        $result = $stmt->execute($conditions);
        if($result===false)
            throw new ServerErrorException();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function genericInsert($values){
        if(count($values)<=0)
            return false;
        if (!$this->multipleValidate($values))
            throw new WrongDataFormatException();
        $values_stmt = ':' . implode(',:',array_keys($values));
        $into_stmt = str_replace(":",'',$values_stmt);
        $stmt = $this->db->prepare("INSERT INTO " . $this->getTableName() . "($into_stmt) VALUES($values_stmt);");
        $result = $stmt->execute($values);
        if ($result == false)
            throw new ServerErrorException();
        return ($stmt->rowCount() > 0);
    }
    
    protected function genericDelete($conditions)
    {
        if (!$this->multipleValidate($conditions)){
            throw new WrongDataFormatException();
        }  
        
        $where = implode(' AND ', array_map(function($key){return "$key=:$key";},array_keys($conditions)));
        $stmt = $this->db->prepare("DELETE FROM " . $this->getTableName() . " WHERE $where");
        $result = $stmt->execute($conditions);
        if ($result == false)
            throw new ServerErrorException();
        return ($stmt->rowCount() > 0);
    }

    protected function genericUpdate($values, $conditions)
    {
        if(count($values)<=0)
            return false;
        if (!$this->multipleValidate($conditions))
            throw new WrongDataFormatException();
        if (!$this->multipleValidate($values))
            throw new WrongDataFormatException();
        
        $set_stmt = implode(',', array_map(function($key){return "$key=:$key";},array_keys($values)));
        $where_stmt = implode(' AND ', array_map(function($key){return "$key=:$key";},array_keys($conditions)));
        $stmt = $this->db->prepare("UPDATE " . $this->getTableName() . " 
                                    SET $set_stmt
                                    WHERE $where_stmt");
        $result = $stmt->execute(array_merge($conditions, $values));
        if ($result == false)
            throw new ServerErrorException();
        return ($stmt->rowCount() > 0);
    }

    protected function multipleValidate($fields){
        foreach($fields as $field=>$value){
            if(!$this->validate($field, $value))
                return false;
        }
        return true;
    }

    private function buildSelect($selectas){
        $select_stmt = '';
        if(count($selectas)===0)
            return false;
        foreach($selectas as $field=>$as){
            if(!$this->validateField($field))
                return false;
            $select_stmt = $select_stmt . $field;
            if($as===null){
                $select_stmt = $select_stmt.",";
                continue;
            }
            if(!preg_match('/[A-Za-z0-9_]+/', $as))
                return false;
            $select_stmt = $select_stmt." as $as,";
        }
        return substr($select_stmt,0,-1);
    }

    abstract protected function validateField($field);
    abstract protected function validate($field, $value);
    abstract protected function getTableName();
}
?>