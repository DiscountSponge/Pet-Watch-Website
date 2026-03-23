<?php



require_once __DIR__ . "/Models/Database.php";
require_once( __DIR__ . '/Models/PetDataSet.php');



$view = new StdClass();
$view->pageTitle = "Live Map";


$petDataSet = new PetDataSet();


$results = $petDataSet->fetchAllPets(9999999999999,0); //Ensuring all possible pets are shown
$jsonResults = []; // Use this to send back sd
foreach ($results as $pet) {
// Only sending data for pets that actually have a sighting, this way is easier as I can then make the cards for them.
    if ($pet->getLongitude()) {
        $jsonResults[] = [
            "id" => $pet->getID(),
            "name" => $pet->getName(),
            "description" => $pet->getDescription(),
            "breed" => $pet->getBreed(),
            "species" => $pet->getSpecies(),
            "colour" => $pet->getColour(),
            "photoURL" => "Views/images/" . $pet->getPhotoUrl(),
            "status" => $pet->getStatus(),
            "dateReported" => $pet->getDateReported(),
            "userID" => $pet->getUserID(),
            "comment" => $pet->getComment(),
            "longitude" => $pet->getLongitude(),
            "latitude" => $pet->getLatitude(),
        ];
    }
}

require_once __DIR__ . "/Views/map.phtml";
header('Content-Type: application/json');
echo json_encode($jsonResults);
exit;