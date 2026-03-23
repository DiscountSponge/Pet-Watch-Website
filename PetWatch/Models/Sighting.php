<?php
class Sightings {


protected $_id, $_pet_id, $_user_id, $_comment, $_latitude, $_longitude, $_timestamp;


public function __construct($dbRow) {
$this->_id = $dbRow['id'];
$this->_pet_id = $dbRow['pet_id'];
$this->_user_id = $dbRow['user_id'];
$this->_comment = $dbRow['comment'];
$this->_latitude = $dbRow['latitude'];
$this->_longitude = $dbRow['longitude'];
$this->_timestamp = $dbRow['timestamp'];
}



public function getId() { return $this->_id; }
public function getPetId() { return $this->_pet_id; }
public function getUserId() { return $this->_user_id; }
public function getComment() { return $this->_comment; }
public function getLatitude() { return $this->_latitude; }
public function getLongitude() { return $this->_longitude; }
public function getTimestamp() { return $this->_timestamp; }



public function setId($id) { $this->_id = $id; }
public function setPetId($pet_id) { $this->_pet_id = $pet_id; }
public function setUserId($user_id) { $this->_user_id = $user_id; }
public function setComment($comment) { $this->_comment = $comment; }
public function setLatitude($latitude) { $this->_latitude = $latitude; }
public function setLongitude($longitude) { $this->_longitude = $longitude; }
public function setTimestamp($timestamp) { $this->_timestamp = $timestamp; }
}