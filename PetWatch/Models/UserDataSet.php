<?php
session_start();
require_once ("Database.php");
require_once ("User.php");

class UserDataSet {
    protected $_dbHandle, $_dbInstance;

    public function __construct() {

        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();

    }
    public function createUsers(): array
    {
        $sqlQuery = 'SELECT * FROM users;';

        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->execute(); // execute the PDO statement

        $dataSet = [];
        // loop through and read the results of the query and cast
        // them into a matching object
        while ($row = $statement->fetch()) {
            $dataSet[] = new User($row);
        }
        return $dataSet;
    }
    public function checkUser($username, $password): bool
    {
        $query = 'SELECT * FROM users WHERE id = ?;';
        $statement = $this->_dbHandle->prepare($query);
        $statement->execute([$username]);
        $row = $statement->fetch();
        if($row) {
            if ($password == $row['password_hash']) {
                $_SESSION['username'] = $row['username'];
                $_SESSION['userType'] = $row['role'];
                return true;
            }
        }
        return false;
    }

}


