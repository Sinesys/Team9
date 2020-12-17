<?php
require_once('systemUserModel.php');
require_once('userInfoModel.php');
require_once('maintainerModel.php');
require_once('competenceModel.php');
require_once('masteryModel.php');
require_once('dbExceptions.php');

class userFacade
{

    public function __construct($pgdb)
    {
        $this->db = $pgdb;
        $this->systemUserModel = new SystemUserModel($pgdb);
        $this->userInfoModel = new UserInfoModel($pgdb);
    }

    /**
     * Insert the user
     *
     * @param array $userinfo the user's info of the user that must be insert. 
     * It's an array composed by the keys: 
     * ['userid', 'password', 'role', 'name', 'surname', 'email', 'phonenumber', 'birthdate']
     *
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function insert($userinfo)
    {
        if ($this->systemUserModel->get($userinfo['userid']) != false)
            return false;

        $this->db->beginTransaction();

        try {
            $this->systemUserModel->insert($userinfo['userid'], $userinfo['password'], $userinfo['role']);
            $this->userInfoModel->insert(
                $userinfo['userid'],
                $userinfo['name'],
                $userinfo['surname'],
                $userinfo['email'],
                $userinfo['phonenumber'],
                $userinfo['birthdate']
            );
        } catch (ServerErrorException | WrongDataFormatException $e) {
            $this->db->rollBack();
            throw $e;
        }

        if ($userinfo['role'] === 'MNT') {
            $maintainerModel = new MaintainerModel($this->db);
            $competenceModel = new CompetenceModel($this->db);
            $masteryModel = new MasteryModel($this->db);

            try {
                $maintainerModel->insert($userinfo['userid']);
                if (!$competenceModel->existAll($userinfo['competences'])) {
                    $this->db->rollBack();
                    return false;
                }
                $masteryModel->insertAll($userinfo['userid'], $userinfo['competences']);
            } catch (ServerErrorException | WrongDataFormatException $e) {
                $this->db->rollBack();
                throw $e;
            }
        }
        $this->db->commit();
        return true;
    }

    /**
     * Get the user's info from the given id
     *
     * @param string $id the id of the user that must be searched. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * 
     * @return array an associative array composed by the keys: 
     * ['userid', 'password', 'role', 'name', 'surname', 'email', 'phonenumber', 'birthdate']
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */

    public function get($id){
        $system_user = $this->systemUserModel->get($id);
        if ($system_user === false)
            return false;
        $info = $this->userInfoModel->get($system_user['id']);
        $info['role'] = $system_user['role'];
        if ($info['role'] === 'MNT') {
            $masteryModel = new MasteryModel($this->db);
            $info['competences'] = $masteryModel->get($info['userid']);
        }
        return $info;
    }

    /**
     * Get all the user's info
     *
     * @return array an array of associative arrays composed by the keys: 
     * ['userid', 'password', 'role', 'name', 'surname', 'email', 'phonenumber', 'birthdate']
     * 
     * @throws ServerErrorException if there is a particular error in the database.
     * */
    public function getAll(){
        $system_users = $this->systemUserModel->getAll();
        $users = [];
        $masteryModel = new MasteryModel($this->db);
        foreach ($system_users as $system_user) {
            $info = $this->userInfoModel->get($system_user['id']);
            $info['role'] = $system_user['role'];
            if ($info['role'] === 'MNT')
                $info['competences'] = $masteryModel->get($info['userid']);
            $users[] = $info;
        }

        return $users;
    }


     /**
     * Delete an user
     * 
     * @param string $id the id of the user that must be deleted. 
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return boolean return true if at least one row has been deleted, false if no lines have been deleted.
     * 
     * @throws WrongDataFormatException if the parameters is in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function delete($id){
        return $this->systemUserModel->delete($id);
    }

    /**
     * Update the user
     * 
     * @param string $id the id of the user that must be updated. 
     * @param array $userinfo the user's info of the user that must be insert. 
     * It's an array composed by the keys: 
     * ['userid', 'password', 'role', 'name', 'surname', 'email', 'phonenumber', 'birthdate']
     *
     * @return boolean return true on success, false if no row is affected.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function update($id, $userinfo){
        $this->db->beginTransaction();
        if ($userinfo['password'] != "") {
            try {
                if (!$this->systemUserModel->update($id, $userinfo['password'])) {
                    $this->db->rollBack();
                    return false;
                }
            } catch (ServerErrorException | WrongDataFormatException $e) {
                $this->db->rollBack();
                throw $e;
            }
        }
        $userInfoModel = new UserInfoModel($this->db);
        try {
            $result = $userInfoModel->update(
                $id,
                $userinfo['name'],
                $userinfo['surname'],
                $userinfo['email'],
                $userinfo['phonenumber'],
                $userinfo['birthdate']
            );
        } catch (ServerErrorException | WrongDataFormatException $e) {
            $this->db->rollBack();
            throw $e;
        }

        try {
            $user = $this->systemUserModel->get($id);
            if($user['role']==='MNT'){
                $competenceModel = new CompetenceModel($this->db);
                if(!array_key_exists("competences", $userinfo)){
                    $this->db->rollBack();
                    return false;
                }
                if (!$competenceModel->existAll($userinfo['competences'])) {
                    $this->db->rollBack();
                    return false;
                }
                $masteryModel = new MasteryModel($this->db);
                $masteryModel->update($id, $userinfo['competences']);
            }     
        } catch (ServerErrorException | WrongDataFormatException $e) {
            $this->db->rollBack();
            throw $e;
        }

        $this->db->commit();
        return true;
    }

}
