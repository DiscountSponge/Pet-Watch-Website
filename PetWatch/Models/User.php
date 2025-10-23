<?php

class User
{

    // Protected properties that match the user table columns.
    protected $_id, $_username, $_email, $_password_hash, $_role;

    /**
     * The constructor accepts a database row and assigns its values to the object's properties.
     *
     * @param array $dbRow An associative array representing one user record.
     */
    public function __construct(array $dbRow)
    {
        $this->_id = $dbRow['id'];
        $this->_username = $dbRow['username'];
        $this->_email = $dbRow['email'];
        $this->_password_hash = $dbRow['password_hash'];
        $this->_role = $dbRow['role'];
    }

    /**
     * Accessor methods (getters) to allow other parts of the application
     * to retrieve user data in a controlled manner.
     */
    public function getId()
    {
        return $this->_id;
    }

    public function getUsername()
    {
        return $this->_username;
    }

    public function getEmail()
    {
        return $this->_email;
    }

    public function getPasswordHash()
    {
        return $this->_password_hash;
    }

    public function getRole()
    {
        return $this->_role;
    }
}