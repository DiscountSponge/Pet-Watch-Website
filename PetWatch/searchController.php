<?php

// Could possibly have included in pets.php?
require_once('Models/PetDataSet.php');


$view = new stdClass();

$petDataSet = new PetDataSet();

if (isset( $_POST['searchButton'])&& !empty($_POST['searchItem'])){
    $searchQuery = htmlentities($_POST['searchItem']);
    $view->petDataSet=$petDataSet->searchPets($searchQuery);
    if (count($view->petDataSet) == 0)
    {
        $view->dbMessage = "No pets found";
        header("Location: pets.php");
        exit;
    }else
    {

        $view->dbMessage = count($view->petDataSet) . " pet(s) found";
    }

    require_once("views/pets.phtml");


}else if (count($view->petDataSet) == 0)
{
    $view->dbMessage = "No pets found";
}
else
{

    $view->dbMessage = count($view->petDataSet) . " pet(s) found";
}
// include the view
require_once("views/pets.phtml");






