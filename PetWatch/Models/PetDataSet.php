<?php

require_once('Database.php');
require_once('PetData.php');

class PetDataSet
{
    protected $_dbHandle, $_dbInstance;

    public function __construct()
    {
        $this->_dbInstance = Database::getInstance();
        $this->_dbHandle = $this->_dbInstance->getdbConnection();
    }

    public function fetchAllPets($limit, $offset)
    {
        $sqlQuery = 'SELECT 
            pets.*, 
            sightings.comment AS sighting_comment, 
            sightings.latitude AS sighting_latitude, 
            sightings.longitude AS sighting_longitude 
        FROM 
            pets 
        LEFT JOIN 
            sightings ON pets.id = sightings.pet_id
        ORDER BY 
            pets.date_reported DESC
        LIMIT ? OFFSET ?
    ';
        //LIMIT and OFFSET used for pagination, limits the number of items that are on each page
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute([(int)$limit, (int)$offset]);

        $dataSet = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) { //more pagination stuff
            $dataSet[] = new PetData($row);
        }

        return $dataSet;
    }
    public function searchPets($searchQuery)
    {
        // Convert search query to lowercase, will do the same with fields
        $searchQuery = strtolower(trim($searchQuery));

        $sqlQuery = "
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
            LOWER(pets.name) LIKE ? 
            OR LOWER(pets.breed) LIKE ? 
            OR LOWER(pets.species) LIKE ?
            OR LOWER(pets.status) LIKE ?
            OR LOWER(pets.description) LIKE ?
        
    ";

        $statement = $this->_dbHandle->prepare($sqlQuery);


        $searchTerm = '%' . $searchQuery . '%';

        // Check all fields for search item
        $statement->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);

        $dataSet = [];
        while ($row = $statement->fetch()) {
            $dataSet[] = new PetData($row);
        }

        return $dataSet;
    }


    public function updatePet($name, $status, $species, $breed, $colour, $dateReported, $description, $photo_url, $pet_id)
    {

        try {
            $sqlQuery = 'UPDATE pets 
                     SET name=?, status=?, species=?, breed=?, color=?, date_reported=?, description=?, photo_url=? 
                     WHERE id=?';

            $statement = $this->_dbHandle->prepare($sqlQuery);
            $statement->execute([$name, $status, $species, $breed, $colour, $dateReported, $description, $photo_url, $pet_id]);
            if ($statement->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error updating database: " . $e->getMessage());
            return false;
        }
    }

    public function insertPet($name, $status, $species, $breed, $colour, $dateReported, $description, $photo_url, $user_id)
    {
        try {
            $sqlQuery = 'INSERT INTO pets (name, species, breed, color, photo_url,status,description,date_reported,user_id)
                            VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?);';
            $statement = $this->_dbHandle->prepare($sqlQuery);
            $statement->execute([$name, $species, $breed, $colour, $photo_url, $status, $description, $dateReported, $user_id]);

            return true;

        } catch (PDOException $e) {
            error_log("Error updating database: " . $e->getMessage());
            return false;
        }
    }

    public function deletePet($pet_id){
        $sqlQuery = 'DELETE FROM pets WHERE id=?';
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute([$pet_id]);
    }

    //nhbbj
    public function countPets(): int
    {
        $sqlQuery = 'SELECT COUNT(id) AS total FROM pets';
        $statement = $this->_dbHandle->prepare($sqlQuery);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        return (int)$row['total'];
    }

}


