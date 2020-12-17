<?php
require_once('abstractModel.php');
require_once('dbExceptions.php');

class RequirementModel extends AbstractModel
{
    private $alias =['material' => null];

    /**
     * Get the materials required by the given activity
     *
     * @param string $activity the id of the activity. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return array an array of materials' id.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function get($activity){
        $results = parent::getAs($this->alias, ['activity'=>$activity]);
        if(!$results)
            return [];
        $materials = [];
        foreach($results as $result)
            $materials[] = $result['material'];
        return $materials;
    }

    /**
     * Insert a material for to the given activity
     *
     * @param string $activity the id of the activity:
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $material the id of the material.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insert($activity, $material){
        return parent::genericInsert(['activity' => $activity, 'material' => $material]);
    }

    /**
     * Insert a set of materials for to the given activity
     *
     * @param string $activity the id of the activity:
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param array $materials the array of materials.
     * Each material must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insertAll($activity, $materials)
    {
        foreach($materials as $material)
            $this->insert($activity, $material);
    }

    /**
     * Update the set of materials for the given activity
     *
     * @param string $activity the id of the activity:
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param array $materials the array of materials.
     * Each material must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * 
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function update($activity, $materials){
        parent::genericDelete(['activity' => $activity]);
        $this->insertAll($activity, $materials);
    }

    protected function validateField($field)
    {
        return in_array($field,['activity', 'material']);
    }

    protected function validate($field, $value){
        switch ($field) {
            case 'activity':
                return preg_match('/^([0-9]|[a-zA-Z]|_){1,20}$/', $value);
                break;
            case 'material':
                return preg_match('/^([0-9]|[a-zA-Z]|_){1,20}$/', $value);
                break;
            default:
                return false;
        }
    }

    protected function getTableName()
    {
        return "requirement";
    }

}
