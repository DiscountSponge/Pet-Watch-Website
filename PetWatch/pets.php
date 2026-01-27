<?php

require_once( __DIR__ . '/Models/PetDataSet.php');

$view = new stdClass();
$view->pageTitle = 'Browse Pets';

$petDataSet = new PetDataSet();
// default list

$resultsPerPage = 10;
$totalPets= $petDataSet->countPets();
$totalPages = ceil($totalPets/$resultsPerPage); //rounds result up

if (isset($_GET['page'])) {
    $page = (int) $_GET['page'];
}else{
    $page = 1;
}
$page = max(1,min($page,$totalPages)); // ensures user cant just go in and type things that messes up the page, fixes the minimum and maximum number of pages that can go in the url
$view->page = $page;
$view->totalPages = $totalPages;
$start = ($page - 1) * $resultsPerPage;
$view->petDataSet = $petDataSet->fetchAllPets($resultsPerPage, $start);
//First item in the database query that it picks up on
// On the 4th page it picks from 3-1 * 10 so pet/sighting combo no 30

if (isset($_GET['searchButton']) && !empty($_GET['searchItem'])) {

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

if(isset($_POST['deletePet']) && !empty($_POST['deletePet'])) {
    $pet_id = $_POST['deletePet'];
    $petDataSet->deletePet($pet_id);
    header('Location:pets.php', $view->message = "Pet deleted successfully."); //need header to refresh page, session wont work?
    //sending $view->message make the success message popup, shouldnt be possible to fail unless
    //theres a connection issue i assume

}

require_once( __DIR__ . "/Views/pets.phtml");
