<?php


require_once(__DIR__ . '/Models/PetDataSet.php');

$view = new stdClass();
$view->pageTitle = 'Pet Watch';

require_once(__DIR__ . '/Views/page1.phtml');