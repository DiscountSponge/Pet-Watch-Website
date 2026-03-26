<?php
session_start();
require_once('Models/PetDataSet.php');

//Security token
$token = "";
if (isset($_SESSION["csrf_token"])) {
    $token = $_SESSION["csrf_token"];
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
$results = $petDataSet->searchPets($query); // Reuse existing search code


header('Content-Type: application/json');
echo json_encode($results);
exit;