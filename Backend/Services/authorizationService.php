<?php
require_once(__DIR__.'/../DBModels/authorizationFacade.php');
require_once('servicesExceptions.php');

class AuthorizationService{
    private $db;

    /**
     * Create an istance of the service.
     * 
     * The service create a connection with a database, so all the info related to this connection must be provided.
     *
     * @param string $host the host of the database management system.
     * @param string $port the port of the database management system.
     * @param string $dbname the database name.
     * @param string $username the username for accessing to the database.
     * @param string $password  the username for accessing to the database.
     */
    public function __construct($host, $port, $dbname, $username, $password)
    {
        $this->db = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        $this->authorization = new authorizationFacade($this->db);
    }

    /**
     * Check if the account associated with the given token has one of the given roles
     *
     * @param string $token the token.
     * @param array $roles an array containing all the roles for the comparision.
     * @return boolean true if the account associated with the given token has one of the given roles, otherwhise false.
     *
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function isAuthorizated($token, $roles){
        if(!$this->authorization->isAuthorizated($token, $roles))
            return false;
        return true;
    }

    /**
     * Check if the account associated with the given token has the same id as the one given
     *
     * @param string $token the token.
     * @param string $id the user's id.
     *
     * @return boolean the account associated with the given token has the same id as the one given, otherwhise false.
     *
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function matchTokenWithID($token, $id){
        if(!$this->authorization->matchTokenWithID($token, $id))
            return false;
        return true;
    }

}
?>