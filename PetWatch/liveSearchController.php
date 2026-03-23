<?php
session_start();
require_once('Models/PetDataSet.php');

$token = "";
if (isset($_SESSION["ajaxToken"])) {
    $token = $_SESSION["ajaxToken"];
}

if (!isset($_GET["token"]) || $_GET["token"] !== $token) {
    header('Content-Type: application/json');
    http_response_code(403); // Kill script with Forbidden status

    $data = new stdClass();
    $data->error = "No data for you sir";
    echo json_encode($data);

    exit; // Stops script executing anymore so no request is made to db
}
$petDataSet = new PetDataSet();

$query = $_GET['q'] ?? '';
$results = $petDataSet->searchPets($query); // Reuse your existing model logic!

$jsonResults = [];
foreach ($results as $pet) {
// "Pack" the data into an array for JSON
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

header('Content-Type: application/json');
echo json_encode($jsonResults);
exit;