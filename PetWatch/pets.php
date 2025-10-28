<?php

// load required classes
require_once('Models/PetDataSet.php');

// make a view class
$view = new stdClass();
$view->pageTitle = 'Browse Pets'; // Changed title to be about pets

// create a new pet dataset object that we can generate data from
$petDataSet = new PetDataSet(); // Changed variable name from $studentsDataSet

// Assumes your PetDataSet class has a method called fetchAllPets()
// Changed view variable from $view->studentsDataSet
$view->petDataSet = $petDataSet->fetchAllPets();

// send a results count to the view to show how many results were retrieved
if (count($view->petDataSet) == 0) // Changed variable
{
    $view->dbMessage = "No pets found"; // Made message more specific
}
else
{
    // Changed variable and made message more specific
    $view->dbMessage = count($view->petDataSet) . " pet(s) found";
}

// include the view
require_once('Views/pets.phtml'); // Changed from studentISv1.phtml
