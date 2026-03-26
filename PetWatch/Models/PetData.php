<?php

class PetData implements jsonSerializable { //

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
        $this->_sighting_comment = $dbRow['sighting_comment'] ?? null;
        $this->_sighting_longitude = $dbRow['sighting_longitude'] ?? null;
        $this->_sighting_latitude = $dbRow['sighting_latitude'] ?? null;
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

    //This makes it so the object itself knows how we want the dta formatted
    //The logic for how a pet should look is now in the object itself when sent to browser


    public function jsonSerialize(): array
    {
        return [
            // Sanitise data just in case
            "id"           => (int)$this->getId(),
            "name"         => htmlspecialchars((string)$this->getName(), ENT_QUOTES, 'UTF-8'),
            "species"      => htmlspecialchars((string)$this->getSpecies(), ENT_QUOTES, 'UTF-8'),
            "breed"        => htmlspecialchars((string)$this->getBreed(), ENT_QUOTES, 'UTF-8'),
            "colour"       => htmlspecialchars((string)$this->getColour(), ENT_QUOTES, 'UTF-8'),
            "photoURL"     => "Views/images/" . htmlspecialchars((string)$this->getPhotoUrl(), ENT_QUOTES, 'UTF-8'),
            "status"       => $this->getStatus(), //doesnt need as it has hard settings
            "description"  => htmlspecialchars((string)$this->getDescription(), ENT_QUOTES, 'UTF-8'),
            "dateReported" => $this->getDateReported(),
            "userID"       => (int)$this->getUserId(),
            "comment"      => htmlspecialchars((string)($this->getComment() ?? ""), ENT_QUOTES, 'UTF-8'),
            "longitude"    => (float)$this->getLongitude(),
            "latitude"     => (float)$this->getLatitude()
        ];
    }
}