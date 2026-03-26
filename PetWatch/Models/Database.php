<?php

class Database {
    protected static $_dbInstance = null;
    protected $_dbHandle;

    public static function getInstance() {
        if(self::$_dbInstance === null) {
            self::$_dbInstance = new self();
        }
        return self::$_dbInstance;
    }

    private function __construct() {
        // Use 127.0.0.1 to force TCP (essential for SSH tunnels on many systems)
        $host = '127.0.0.1';
        $dbName = 'sge969';
        $port = 3306;
        $user = 'sge969';
        $pass = '2mOzm3m7W58CrZS';

        try {

            $this->_dbHandle = new PDO(
                "mysql:host=$host;dbname=$dbName;port=$port;charset=utf8mb4",
                $user,
                $pass,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
        catch (PDOException $e) {
            // If the connection fails here,program cannot continue
            die("Database Connection Error: " . $e->getMessage());
        }
    }

    public function getdbConnection() {
        // This will now return the property we successfully set in the constructor
        return $this->_dbHandle;
    }

    public function __destruct() {
        $this->_dbHandle = null;
    }
}