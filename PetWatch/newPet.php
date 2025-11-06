<?php
$view = new StdClass();
$view->pageTitle = "Adding Pet";
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
    // ✅ Fixed upload handling
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

    // ✅ Update pet in DB
    $petData = new PetDataSet();
    $petData->insertPet($name, $status, $species, $breed, $colour, $dateReported, $description, $photo_url, $user_id);


}
require_once("Views/newPet.phtml");
