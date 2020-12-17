<?php
require_once('abstractModel.php');
require_once('dbExceptions.php');

class SiteModel extends AbstractModel
{

    private $alias =[
        'site_id' => 'siteid', 
        'area' => null,
        'department' => null
    ];

    /**
     * Get all the sites
     *
     * @return array an array of associative arrays composed by the keys:
     * ['siteid', 'area', 'department']
     * 
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getAll()
    {
        return parent::getAllAs($this->alias);
    }
    
    /**
     * Get the info of the given site
     *
     * @param string $siteid the id of site. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return array an associative array composed by the keys: 
     * ['siteid', 'area', 'department']
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function get($siteid) {
        return parent::getSingleAs($this->alias, ['site_id'=>$siteid]);
    }


    /**
     * Insert a site
     *
     * @param string $siteid the id of the site.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $area the area of the site.
     * The lenght must be greater than 0 and less than 51.
     * @param string $department the department of the site.
     * The lenght must be greater than 0 and less than 51.
     * 
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insert($siteid, $area, $department)
    {
        return parent::genericInsert(['site_id' => $siteid, 'area' => $area, 'department'=> $department]);
    }

    /**
     * Delete a site
     * 
     * @param string $siteid the id of the site.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * 
     * @return boolean return true if at least one row has been deleted, false if no lines have been deleted.
     * 
     * @throws WrongDataFormatException if the parameters is in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */

    public function delete($siteid)
    {
        return parent::genericDelete(['site_id' => $siteid]);
    }

    /**
     * Update a site
     *
     * @param string $siteid the id of the site that must be updated.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @param string $area the area of the site.
     * The lenght must be greater than 0 and less than 51.
     * @param string $department the department of the site.
     * The lenght must be greater than 0 and less than 51.
     * 
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function update($siteid, $area, $department)
    {
        return parent::genericUpdate(['area' => $area, 'department' => $department], ['site_id' => $siteid]);
    }

    protected function getTableName()
    {
        return "site";
    }

    protected function validateField($field)
    {
        return in_array($field,['site_id', 'area','department']);
    }

    protected function validate($field, $value)
    {
        switch ($field) {
            case 'site_id':
                return preg_match('/^([0-9]|[a-zA-Z]|_){1,20}$/', $value);
                break;
            case 'area':
                return preg_match('/^(.){1,50}$/', $value);
                break;
            case 'department':
                return preg_match('/^(.){1,50}$/', $value);
                break;
            default:
                return false;
        }
    }
    
}
