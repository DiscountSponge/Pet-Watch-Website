<?php

// load required classes
require_once('Models/PetDataSet.php');

// make a view class
$view = new stdClass();
$view->pageTitle = 'Pet Watch';
//



// include the view
require_once('Views/page1.phtml');