<?php
//Different file from mapController so one controls what it looks like
// and the other the actual meat of the operation
$view = new StdClass();
$view->pageTitle = "Sightings Map";
require_once __DIR__ . "/Views/map.phtml";