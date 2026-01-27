<?php
$view = new stdClass();
$view->pageTitle = 'Edit Pet Report';
require_once( __DIR__ . "/Models/PetDataSet.php");

//for echoing
if (isset($_GET['pet_id'])) {
    $view->pet_id= (int)($_GET['pet_id']);
    $view->name = $_GET['name'];
    $view->status = $_GET['status'];
    $view->species = $_GET['species'];
    $view->breed = $_GET['breed'];
    $view->colour = $_GET['colour'];
    $view->date_reported = $_GET['date_reported'];
    $view->description = $_GET['description'];
    $view->photo_url = $_GET['photo_url'];


}

//actually sending back to db
if (isset($_POST['edit'])) {
    $pet_id = $_POST['pet_id'];
    $name = trim($_POST['petName']);
    $status = trim($_POST['status']);
    $species = trim($_POST['species']);
    $breed = trim($_POST['breed']);
    $colour = trim($_POST['colour']);
    $dateReported = trim($_POST['dateReported']);
    $description = trim($_POST['description']);


    $photo_url =trim($_POST['existing_photo']);

    if (isset($_FILES['petPhoto']) && $_FILES['petPhoto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '\\Views\\images\\';
        $fileTmpPath = $_FILES['petPhoto']['tmp_name'];
        $fileName = basename($_FILES['petPhoto']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedExtensions)) {
            $newFileName = uniqid('pet_', true) . '.' . $fileExtension;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destination)) {
                $photo_url = $fileName;
            } else {
                $view->message = "Error moving uploaded file.";
            }
        } else {
            $view->message = "Invalid file type.";
        }
    }


    $petData = new PetDataSet();
    if (!empty($name) && strlen($name) <50 &&
        !empty($status) && !empty($species)  && !empty($dateReported) && !empty($photo_url)) {
        $check =$petData->updatePet($name, $status, $species, $breed, $colour, $dateReported, $description, $photo_url, $pet_id);
        if(!$check){
            $view->message = "Error updating database. Please ensure all fields are filled";

        }else{
            $view->message = "Pet updated successfully";

        }
    }else{
        $view->message = "Error updating database. Please ensure all fields are filled";

    }





}


require_once(__DIR__ . '/Views/editPet.phtml');