<?php

class PasswordData
{

    // Protected properties corresponding to the 'pets' table columns.
    protected $_Key, $_Passwords;

    /**
     * Constructor for the PetData class.
     *
     * @param array $dbRow An associative array representing a single row from the pets database table.
     */
    public function __construct($dbRow)
    {
        $this->_Key = $dbRow['Key'];
        $this->_Passwords = $dbRow['Passwords'];

    }

}

