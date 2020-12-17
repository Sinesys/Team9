<?php
require_once('authenticationModel.php');
require_once('systemUserModel.php');
require_once('dbExceptions.php');

class authorizationFacade
{
    public function __construct($pgdb){
        $this->db = $pgdb;
        $this->authenticationModel = new AuthenticationModel($pgdb);
        $this->systemUserModel = new SystemUserModel($pgdb);
    }

    /**
     * Check if the current token is associated with at least one of the given roles
     *
     * @param string $token the token.
     * The lenght must be greater than 0 and less than 51;
     * @param array $auth array containing all the roles to check:
     * Each role must be one of the following strings: 'ADM', 'PLN', 'MNT', 'DBL'.
     * @return boolean true if at least one of the role is matched, otherwhise false.
     */
    public function isAuthorizated($token, $auth){
        return in_array($this->getAuthorization($token), $auth);
    }

    /**
     * Check if the given token is associated with the given id
     *
     * @param string $token the token.
     * The lenght must be greater than 0 and less than 51;
     * @param string $id the user's id.
     * Must contains only alphanumeric chars, numbers and underscores and the lenght must be greater than 0 and less than 21;
     * @return boolean true if there is a match between the token and the id, otherwhise false.
     */
    public function matchTokenWithID($token, $id){
        $auth = $this->authenticationModel->getByToken($token);
        if(!$auth)
            return false;
        if($auth['expires'] < date( "Y-m-d H:i:s"))
            return false;
        return $auth['id']===$id;
    }

    /**
     * Get the role associated with the given token
     *
     * @param string $token the token.
     * The lenght must be greater than 0 and less than 51;
     * @return string the role associated with the given token
     */
    public function getAuthorization($token){
        $auth = $this->authenticationModel->getByToken($token);
        if(!$auth)
            return false;
        if($auth['expires'] < date( "Y-m-d H:i:s"))
            return false;
        $user = $this->systemUserModel->get($auth['id']);
        if(!$user)
            return false;
        return $user['role'];
    }
}
