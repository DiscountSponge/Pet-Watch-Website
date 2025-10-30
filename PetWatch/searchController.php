<?php

// load required classes
require_once('Models/PetDataSet.php');


// make a view class
$view = new stdClass();
$view->pageTitle = 'Browse Pets'; // Changed title to be about pets

// create a new pet dataset object that we can generate data from
$petDataSet = new PetDataSet(); // Changed variable name from $studentsDataSet

if (isset($_POST['searchButton'])&& !empty($_POST['searchItem'])){ //if the button been pressed do all thus
    $searchQuery = $_POST['searchItem'];
    $view->petDataSet=$petDataSet->searchPets($searchQuery);
    if (count($view->petDataSet) == 0) // Changed variable
    {
        $view->dbMessage = "No pets found"; // Made message more specific
        header("Location: pets.php");
    }else
    {
        // Changed variable and made message more specific
        $view->dbMessage = count($view->petDataSet) . " pet(s) found";
    }
    // include the view
    require_once("views/pets.phtml");


}else if (count($view->petDataSet) == 0) // Otherwise just show pets
{
    $view->dbMessage = "No pets found"; // Made message more specific
}
else
{
    // Changed variable and made message more specific
    $view->dbMessage = count($view->petDataSet) . " pet(s) found";
}
// include the view
require_once("views/pets.phtml");






