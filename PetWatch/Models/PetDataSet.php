<?php

require_once ('Database.php');
require_once('PetData.php');

class PetDataSet {
    protected $_dbHandle, $_dbInstance;
        
    public function __construct() {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    public function fetchAllpets() {
        $sqlQuery = 'SELECT 
    pets.*, 
    sightings.comment AS sighting_comment, 
    sightings.latitude AS sighting_latitude, 
    sightings.longitude AS sighting_longitude 
    FROM 
    pets 
    LEFT JOIN 
    sightings ON pets.id = sightings.pet_id;';

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

    public function searchPets($searchQuery) {

    $searchStatement = "SELECT 
        pets.*, 
        sightings.comment AS sighting_comment, 
        sightings.latitude AS sighting_latitude, 
        sightings.longitude AS sighting_longitude 
        FROM 
        pets 
        LEFT JOIN 
        sightings ON pets.id = sightings.pet_id; WHERE pets.name LIKE '%$searchQuery%';";
        $statement = $this->_dbHandle->prepare($searchStatement);
        $statement->execute();
        $dataSet = [];
        while ($row = $statement->fetch()) {
            $dataSet[] = new PetData($row);

        }
    return $dataSet;
    }

}


