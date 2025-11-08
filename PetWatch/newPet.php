<?php

$view = new StdClass();
$view->pageTitle = "Adding Pet";
require ("Models/PetDataSet.php");

if (isset($_POST['add'])) {
    $user_id = $_POST['user_id'];
    $name = trim($_POST['petName']);
    $status = trim($_POST['status']);
    $species = trim($_POST['species']);
    $breed = trim($_POST['breed']);
    $colour = trim($_POST['colour']);
    $dateReported = trim($_POST['dateReported']);
    $description = trim($_POST['description']);

    // ✅ Fixed upload handling
    if (isset($_FILES['petPhoto']) && $_FILES['petPhoto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '\\Views\\images\\';
        $fileTmpPath = $_FILES['petPhoto']['tmp_name'];
        $fileName = basename($_FILES['petPhoto']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $photo_url="";
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
        $petData = new PetDataSet();
        $check = $petData->insertPet($name, $status, $species, $breed, $colour, $dateReported, $description, $photo_url, $user_id);
        if(!$check){
            $_SESSION["message"] = "Error updating database. Please ensure all fields are filled";
        }else{
            header("Location:pets.php");
            exit;
        }
    }

    // ✅ Update pet in DB


}
require_once("Views/newPet.phtml");
