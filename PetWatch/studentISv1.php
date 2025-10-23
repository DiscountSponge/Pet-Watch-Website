<?php

// load required classes
require_once('Models/PetDataSet.php');

// make a view class
$view = new stdClass();
$view->pageTitle = 'Student Information System';

// create a new student dataset object that we can generate data from
$studentsDataSet = new PetDataSet();
$view->studentsDataSet = $studentsDataSet->fetchAllStudents();

// send a results count to the view to show how many results were retrieved
if (count($view->studentsDataSet) == 0)
{
    $view->dbMessage = "No results";
}
else
{
    $view->dbMessage = count($view->studentsDataSet) . " result(s)";
}

// include the view
require_once('Views/studentISv1.phtml');