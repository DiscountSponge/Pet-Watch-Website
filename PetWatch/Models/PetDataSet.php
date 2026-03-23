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
        // Added aliases (AS) so the keys match what your PetData constructor expects
        $sqlQuery = '
        SELECT 
            pets.*, 
            s.comment AS sighting_comment, 
            s.latitude AS sighting_latitude, 
            s.longitude AS sighting_longitude 
        FROM pets 
        LEFT JOIN (
            SELECT pet_id, MAX(timestamp) as latest_time
            FROM sightings
            GROUP BY pet_id
        ) latest ON pets.id = latest.pet_id
        LEFT JOIN sightings s ON s.pet_id = latest.pet_id AND s.timestamp = latest.latest_time
        ORDER BY pets.date_reported DESC
        LIMIT :limit OFFSET :offset
    ';

        $statement = $this->_dbHandle->prepare($sqlQuery);

        // Bind as Integers - required for MySQL LIMIT/OFFSET when emulation is off
        $statement->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $statement->execute();

        $dataSet = [];
        // Use FETCH_ASSOC to ensure we get a clean array for the PetData constructor
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
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
            s.comment AS sighting_comment, 
            s.latitude AS sighting_latitude, 
            s.longitude AS sighting_longitude 
        FROM pets 
        LEFT JOIN (
            SELECT pet_id, MAX(timestamp) as latest_time
            FROM sightings
            GROUP BY pet_id
        ) latest ON pets.id = latest.pet_id
        LEFT JOIN sightings s ON s.pet_id = latest.pet_id AND s.timestamp = latest.latest_time
        WHERE 
            pets.name LIKE ? 
            OR pets.breed LIKE ? 
            OR pets.species LIKE ?
            OR pets.status LIKE ?
            OR pets.description LIKE ?
        ORDER BY pets.date_reported DESC
    ";

        $statement = $this->_dbHandle->prepare($sqlQuery);

        $searchTerm = '%' . $searchQuery . '%';

        // Check all fields for search item
        $statement->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);

        $dataSet = [];
        // (Added FETCH_ASSOC here so it matches the clean array logic of fetchAllPets)
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
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


