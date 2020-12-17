<?php
require_once(__DIR__.'/../DBModels/userFacade.php');
require_once('servicesExceptions.php');

class UsersManagementService
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
        $this->userFacade = new userFacade($this->db);
    }

    /**
     * Adds an user
     *
     * @param array $userinfo an associative array composed by the following keys:
     * ['userid', 'name', 'surname', 'email', 'phonenumber', 'birthdate', 'password', 'role']
     * and also the key 'competences' if the role is 'MNT'.
     * @return void
     * 
     * @throws WrongDataException if the user is already present, or if the given materials doesn't exist.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function addUser($userinfo)
    {
        if(!$this->userFacade->insert($userinfo))
            throw new WrongDataException();
    }

    /**
     * Deletes an user
     *
     * @param string $userid the user's id.
     * @throws WrongDataException if the user doesn't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function deleteUser($id)
    {
        if (!$this->userFacade->delete($id))
            throw new WrongDataException();
    }

    /**
     * Edits an user
     *
     * @param string $userid the user's id.
     * @param array $userinfo an associative array composed by the following keys:
     * ['name', 'surname', 'email', 'phonenumber', 'birthdate', 'password'] and also the key 'competences' if the role is 'MNT'.
     * If 'password' is an empty string, it will not be updated.
     * @throws WrongDataException if the user doesn't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function editUser($id, $userinfo)
    {
        if (!$this->userFacade->update($id, $userinfo))
            throw new WrongDataException();
    }

    /**
     * Returns all the users' info
     *
     * @return array an array of associative arrays.
     * 
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getUsersInfo()
    {
        return $this->userFacade->getAll();
    }

    /**
     * Returns all the user's info
     *
     * @param string $userid the user's id
     * @return array an associative arrays.
     * 
     * @throws WrongDataException if the user doesn't exists.
     * @throws WrongDataFormatException if the parameters are in a wrong format.
     * @throws ServerErrorException if there is a particular error in the database.
     */
    public function getUserInfo($id)
    {
        $user = $this->userFacade->get($id);
        if(!$user)
            throw new WrongDataException();
        return $user;
    }
}
