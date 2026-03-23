<?php



require_once __DIR__ . "/Models/Database.php";
require_once( __DIR__ . '/Models/PetDataSet.php');



$view = new StdClass();
$view->pageTitle = "Sightings Map";


$petDataSet = new PetDataSet();


$results = $petDataSet->fetchAllPets(9999999999999,0); //Ensuring all possible pets are shown
$jsonResults = []; // Use this to send back sd
foreach ($results as $pet) {
// Only sending data for pets that actually have a sighting, this way is easier as I can then make the cards for them.
    if ($pet->getLongitude()) {
        $jsonResults[] = [
            //Sanitising data, should already be fine but just in case
            //Creating array with table fields in order to create cards with relevant information on each pet
            "id"           => htmlspecialchars((string)$pet->getID(), ENT_QUOTES, 'UTF-8'),
            "name"         => htmlspecialchars((string)$pet->getName(), ENT_QUOTES, 'UTF-8'),
            "description"  => htmlspecialchars((string)$pet->getDescription(), ENT_QUOTES, 'UTF-8'),
            "breed"        => htmlspecialchars((string)$pet->getBreed(), ENT_QUOTES, 'UTF-8'),
            "species"      => htmlspecialchars((string)$pet->getSpecies(), ENT_QUOTES, 'UTF-8'),
            "colour"       => htmlspecialchars((string)$pet->getColour(), ENT_QUOTES, 'UTF-8'),
            "photoURL"     => htmlspecialchars("Views/images/" . $pet->getPhotoUrl(), ENT_QUOTES, 'UTF-8'),
            "status"       => htmlspecialchars((string)$pet->getStatus(), ENT_QUOTES, 'UTF-8'),
            "dateReported" => htmlspecialchars((string)$pet->getDateReported(), ENT_QUOTES, 'UTF-8'),
            "userID"       => htmlspecialchars((string)$pet->getUserID(), ENT_QUOTES, 'UTF-8'),
            "comment"      => htmlspecialchars((string)$pet->getComment(), ENT_QUOTES, 'UTF-8'),
            "longitude"    => htmlspecialchars((string)$pet->getLongitude(), ENT_QUOTES, 'UTF-8'),
            "latitude"     => htmlspecialchars((string)$pet->getLatitude(), ENT_QUOTES, 'UTF-8'),
        ];
    }
}





header('Content-Type: application/json');
echo json_encode($jsonResults);
exit;