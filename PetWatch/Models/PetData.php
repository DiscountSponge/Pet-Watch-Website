<?php
/**
 * Class PetData
 *
 * This class models the data for a single pet within the petWatch system.
 * It uses protected fields for data encapsulation, ensuring that the data
 * from the database is properly structured within an object.
 *
 * The constructor accepts a database row (as an associative array) and assigns
 * its values to the class's properties. This is a fundamental part of the Model
 * [cite_start]in the MVC pattern[cite: 2].
 */
class PetData {

    // Protected properties corresponding to the 'pets' table columns.
    protected $_id, $_name, $_species, $_breed, $_color, $_photo_url, $_status, $_description, $_date_reported, $_user_id;

    /**
     * Constructor for the PetData class.
     *
     * @param array $dbRow An associative array representing a single row from the pets database table.
     */
    public function __construct($dbRow) {
        $this->_id = $dbRow['id'];
        $this->_name = $dbRow['name'];
        $this->_species = $dbRow['species'];
        $this->_breed = $dbRow['breed'];
        $this->_color = $dbRow['color'];
        $this->_photo_url = $dbRow['photo_url'];

        // Sets the status to 'lost' or 'found' using the requested if/else structure.
        if ($dbRow['status'] == 'lost') {
            $this->_status = 'lost';
        } else {
            $this->_status = 'found';
        }

        $this->_description = $dbRow['description'];
        $this->_date_reported = $dbRow['date_reported'];
        $this->_user_id = $dbRow['user_id'];
    }

    /**
     * Accessor methods (getters) for all pet properties.
     * These methods are used by the View to display the pet's information.
     */
    public function getId() {
        return $this->_id;
    }

    public function getName() {
        return $this->_name;
    }

    public function getSpecies() {
        return $this->_species;
    }

    public function getBreed() {
        return $this->_breed;
    }

    public function getColor() {
        return $this->_color;
    }

    public function getPhotoUrl() {
        return $this->_photo_url;
    }

    public function getStatus() {
        return $this->_status;
    }

    public function getDescription() {
        return $this->_description;
    }

    public function getDateReported() {
        return $this->_date_reported;
    }

    public function getUserId() {
        return $this->_user_id;
    }
}