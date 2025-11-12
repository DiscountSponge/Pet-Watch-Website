<?php

class PetData {

    // Protected properties corresponding to the 'pets' table columns.
    protected $_id, $_name, $_species, $_breed, $_color, $_photo_url, $_status, $_description, $_date_reported, $_user_id,$_sighting_comment, $_sighting_longitude, $_sighting_latitude;

    /**
     * Constructor for the PetData class.
     *
     * @param array $dbRow //Modifies from students version we were given
     */
    public function __construct($dbRow) {
        $this->_id = $dbRow['id'];
        $this->_name = $dbRow['name'];
        $this->_species = $dbRow['species'];
        $this->_breed = $dbRow['breed'];
        $this->_color = $dbRow['color'];
        $this->_photo_url = $dbRow['photo_url'];



        if ($dbRow['status'] == 'lost') {
            $this->_status = 'lost';
        } else {
            $this->_status = 'found';
        }

        $this->_description = $dbRow['description'];
        $this->_date_reported = $dbRow['date_reported'];
        $this->_user_id = $dbRow['user_id'];
        $this->_sighting_comment = $dbRow['sighting_comment'];
        $this->_sighting_longitude = $dbRow['sighting_longitude'];
        $this->_sighting_latitude = $dbRow['sighting_latitude'];
    }

 
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

    public function getColour() {
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

    public function getComment(){
        return $this->_sighting_comment;
    }
    public function getLongitude(){
        return $this->_sighting_longitude;
    }
    public function getLatitude(){
        return $this->_sighting_latitude;
    }
}