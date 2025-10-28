<?php

// load required classes
require_once('Models/PetDataSet.php');

// make a view class
$view = new stdClass();
$view->pageTitle = 'Pet Watch';
//
//create a new student dataset object that we can generate data from
$petDataSet = new PetDataSet();
$view->petDataSet = $petDataSet->fetchAllPets();

// send a results count to the view to show how many results were retrieved
if (count($view->petDataSet) == 0)
{
    $view->dbMessage = "No results";
}
else
{
    $view->dbMessage = count($view->petDataSet) . " result(s)";
}

// include the view
require_once('Views/page1.phtml');