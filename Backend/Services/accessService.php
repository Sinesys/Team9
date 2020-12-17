<?php
require_once(__DIR__.'/../DBModels/systemUserModel.php');
require_once(__DIR__ .'/../DBModels/authenticationModel.php');
require_once(__DIR__ .'/../DBModels/accessLogModel.php');
require_once('servicesExceptions.php');

class AccessService
{
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
    }

    /**
     * Allow to make the login 
     *
     * @param string $id the username.
     * @param string $password the password.
     * @return array an associative array composed by the following keys:
     * ['role', 'auth_token']
     * 
     * @throws WrongCredentialsException if the credentials are wrong.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function login($id, $password)
    {
        $system_user = new SystemUserModel($this->db);     
        $user = $system_user->get($id);
        if ($user === false)
            throw new WrongCredentialsException();
        if ($password != $user['password'])
            throw new WrongCredentialsException();   

        $authenticationModel = new AuthenticationModel($this->db);
        $token = str_replace('"', 'x', uniqid("", true));
        $authenticationModel->replaceToken($id, $token);

        return ['role' => $user['role'], 'auth_token' => $token];
    }

    /**
     * Allow to make the login 
     *
     * @param string $token the token associated with account that wants to logout
     * @return void
     * 
     * @throws WrongDataException if the token doesn't exist or if it's expired.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function logout($token){
        $authenticationModel = new AuthenticationModel($this->db);
        if (!$authenticationModel->deleteToken($token))
            throw new WrongDataException();
    }

    /**
     * Get the access log of all the users
     *
     * @return array an array of associative array composed by the keys: 
     * ['userid', 'accesstime']
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getAccessLog(){
        $accessLogModel = new AccessLogModel($this->db);
        $result = $accessLogModel->getAll();
        return $result;
    }

}
