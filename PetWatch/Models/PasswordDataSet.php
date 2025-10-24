<?php
require("Database.php");
require("PasswordData.php");
class PasswordDataSet{
    protected $_dbHandle, $_dbInstance;

    public function __construct() {
        session_start();
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();

    }
    public function createUsers(): array
    {
        $sqlQuery = 'SELECT * FROM PasswordTesting;';

        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->execute(); // execute the PDO statement

        $dataSet = [];
        // loop through and read the results of the query and cast
        // them into a matching object
        while ($row = $statement->fetch()) {
            $dataSet[] = new PasswordData($row);
        }
        return $dataSet;
    }
    public function checkUser($key, $password): bool
    {
        $query = 'SELECT * FROM PasswordTesting WHERE Key = ?;';
        $statement = $this->_dbHandle->prepare($query);
        $statement->execute([$key]);
        $row = $statement->fetch();
        if($row) {
            if ($password == $row['Passwords']) {

                return true;
            }
        }
        return false;
    }
    public function fetchAllpets() {
        $sqlQuery = 'SELECT * FROM PasswordTesting;';

        $statement = $this->_dbHandle->prepare($sqlQuery); // prepare a PDO statement
        $statement->execute(); // execute the PDO statement

        $dataSet = [];
        // loop through and read the results of the query and cast
        // them into a matching object
        while ($row = $statement->fetch()) {
            $dataSet[] = new PetData($row);
        }
        return $dataSet;
    }

    public function hashPassword(){
        // 1. SELECT all users who need their passwords hashed
        $sqlSelect = 'SELECT Key, Passwords FROM PasswordTesting;';

        $statement = $this->_dbHandle->prepare($sqlSelect);
        $statement->execute(); // Execute the SELECT statement

        // Prepare the UPDATE statement once outside the loop
        // This is more efficient than preparing it N times inside the loop
        $sqlUpdate = 'UPDATE PasswordTesting SET Passwords = ? WHERE Key = ?;';
        $statementUpdate = $this->_dbHandle->prepare($sqlUpdate);

        while($row = $statement->fetch(PDO::FETCH_ASSOC)){ // Use FETCH_ASSOC for clarity
            $currentKey = $row['Key'];
            $currentPassword = $row['Passwords'];

            // 2. CRITICAL FIX: Assign the result of password_hash() to a variable
            $hashedPassword = password_hash($currentPassword, PASSWORD_DEFAULT);

            // Check if hashing was successful (optional but good practice)
            if ($hashedPassword === false) {
                // Log an error or skip this entry
                continue;
            }

            // 3. CRITICAL FIX: Use the HASHED password in the execute array
            $statementUpdate->execute([$hashedPassword, $currentKey]);
        }
    }
}