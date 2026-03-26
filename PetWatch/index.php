<?php
//should be initialising the security token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once(__DIR__ . '/Models/PetDataSet.php');

$view = new stdClass();
$view->pageTitle = 'Pet Watch';

require_once(__DIR__ . '/Views/page1.phtml');