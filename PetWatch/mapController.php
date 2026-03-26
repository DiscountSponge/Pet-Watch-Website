<?php
session_start();

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
require_once __DIR__ . "/Models/Database.php";
require_once( __DIR__ . '/Models/PetDataSet.php');



$view = new StdClass();
$view->pageTitle = "Sightings Map";


$petDataSet = new PetDataSet(); //creating object of class to call functions

//Make sure all possible pets show up
$results = $petDataSet->fetchAllPets(9999999999999,0); //Ensuring all possible pets are shown



header('Content-Type: application/json');
echo json_encode($results);
exit;