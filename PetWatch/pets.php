<?php
require_once('Models/PetDataSet.php');

$view = new stdClass();
$view->pageTitle = 'Browse Pets';

$petDataSet = new PetDataSet();
$view->petDataSet = $petDataSet->fetchAllPets(); // default list

if (isset($_GET['searchButton']) && !empty($_GET['searchItem'])) {
    // Remove HTML and trim spaces
    $searchQuery = htmlspecialchars(trim($_GET['searchItem']));

    $view->petDataSet = $petDataSet->searchPets($searchQuery);

    if (count($view->petDataSet) == 0) {
        $view->dbMessage = "No pets found matching";
    } else {
        $view->dbMessage = count($view->petDataSet) . " pet(s) found";
    }
} else {
    if (count($view->petDataSet) == 0) {
        $view->dbMessage = "No pets found in database.";
    } else {
        $view->dbMessage = count($view->petDataSet) . " pet(s) available.";
    }
}

require_once("views/pets.phtml");
