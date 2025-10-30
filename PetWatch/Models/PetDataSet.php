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
        $sql = "
        SELECT 
            pets.*, 
            sightings.comment AS sighting_comment, 
            sightings.latitude AS sighting_latitude, 
            sightings.longitude AS sighting_longitude 
        FROM 
            pets 
        LEFT JOIN 
            sightings ON pets.id = sightings.pet_id
        WHERE 
            pets.name LIKE :searchQuery
        OR 
            pets.breed LIKE :searchQuery
        OR 
            pets.species LIKE :searchQuery
    ";

        $statement = $this->_dbHandle->prepare($sql);
        $statement->bindValue(':searchQuery', '%' . $searchQuery . '%', PDO::PARAM_STR);
        $statement->execute();

        $dataSet = [];
        while ($row = $statement->fetch()) {
            $dataSet[] = new PetData($row);
        }

        return $dataSet;
    }


}


