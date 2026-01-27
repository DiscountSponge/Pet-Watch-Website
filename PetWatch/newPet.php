<?php

$view = new StdClass();
$view->pageTitle = "Adding Pet";
require (__DIR__ . "/Models/PetDataSet.php");

if (isset($_POST['add'])) {
    $user_id = $_POST['user_id'];
    $name = trim($_POST['petName']);
    $status = trim($_POST['status']);
    $species = trim($_POST['species']);
    $breed = trim($_POST['breed']);
    $colour = trim($_POST['colour']);
    $dateReported = trim($_POST['dateReported']);
    $description = trim($_POST['description']);

    if (isset($_FILES['petPhoto']) && $_FILES['petPhoto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '\\Views\\images\\';
        $fileTmpPath = $_FILES['petPhoto']['tmp_name'];
        $fileName = basename($_FILES['petPhoto']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $photo_url = "";
        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = uniqid('pet_', true) . '.' . $fileExtension;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destination)) {
                $photo_url = $newFileName;
            } else {
                $view->message = "Error moving uploaded file.";
            }
        } else {
            $view->message = "Invalid file type.";

        }
    }
    /*
     * @todo change db fields to NOT NULL
     */
    // basic validation, fields i want filled that arent NOT NULL in DB
    if (!empty($name) && strlen($name) < 50 &&
        !empty($status) && !empty($species) && !empty($dateReported) && !empty($photo_url)) {

        $petData = new PetDataSet();
        $check = $petData->insertPet($name, $status, $species, $breed, $colour, $dateReported, $description, $photo_url, $user_id);

        if ($check) {
            $view->message = "Pet added successfully!";

        } else {
            $view->message = "Error adding pet to database. Please try again.";

        }
    } else {
        $view->message = "Please ensure all required fields are filled correctly (Name must be under 50 characters etc).";

    }
}


require_once("Views/newPet.phtml");