<?php
// creates session token if they dont exist
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once(__DIR__ . '/Models/PetDataSet.php');




require_once(__DIR__ . '/Models/PetDataSet.php');

$petDataSet = new PetDataSet();
$view = new stdClass();
$view->pageTitle = 'Browse Pets';

//  AJAX ENDPOINTS (Returns JSON, then EXITS)


// Javascrupt to fetch pets
if (isset($_GET['ajax_fetch_all'])) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');

    // Security Check: Verifies the token sent by JS
    $token = $_SESSION["csrf_token"] ?? '';
    if (!isset($_GET["token"]) || $_GET["token"] !== $token) {
        http_response_code(403);
        echo json_encode(["error" => "No data for you sir"]);
        exit;
    }

    $resultsPerPage = 80;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $start = ($page - 1) * $resultsPerPage;

    $query = $_GET['q'] ?? '';
    if (!empty($query)) {
        $results = $petDataSet->searchPets(htmlspecialchars(trim($query)));
    } else {
        $results = $petDataSet->fetchAllPets($resultsPerPage, $start);
    }

    echo json_encode($results);
    exit();
}

if (isset($_GET['ajax_pet_id'])) {
    // Kill any accidental whitespace/HTML leaked so far
    while (ob_get_level()) ob_end_clean();

    $petId = (int)$_GET['ajax_pet_id'];
    $pet = $petDataSet->fetchPetById($petId);

    // 2. Set header
    header('Content-Type: application/json');

    // sends JSON results to relevant js file
    echo json_encode($pet);
    exit;
}


// Handle Delete Modal Submission
if(isset($_POST['deletePet']) && !empty($_POST['deletePet'])) {

    // Security check for standard HTML form
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['csrf_token']) {
        die("Security token mismatch. Action aborted.");
    }

    $petToDelete = (int)$_POST['deletePet'];
    $petDataSet->deletePet($petToDelete);

    $_SESSION['flash_message'] = "Pet deleted successfully.";
    header('Location: pets.php');
    exit();
}

// Handle Edit Modal Submission
if (isset($_POST['edit'])) {

    // Security check for standard HTML form
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['csrf_token']) {
        die("Security token mismatch.");
    }

    $pet_id = (int)$_POST['pet_id'];
    $name = $_POST['petName'];
    $status = $_POST['status'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $colour = $_POST['colour'];
    $dateReported = $_POST['dateReported'];
    $description = $_POST['description'];
    $photoURL = $_POST['existing_photo'];
    //collects data from modal and uses it to update database
    if (isset($_FILES['petPhoto']) && $_FILES['petPhoto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
        $fileName = basename($_FILES['petPhoto']['name']);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $newFileName = uniqid('pet_', true) . '.' . $fileExtension;
            if (move_uploaded_file($_FILES['petPhoto']['tmp_name'], $uploadDir . $newFileName)) {
                $photoURL = $newFileName;
            }
        }
    }

    //Sends data to table
    $petDataSet->updatePet($name, $status, $species, $breed, $colour, $dateReported, $description, $photoURL, $pet_id);
    $_SESSION['flash_message'] = "Pet successfully updated!";
    header('Location: pets.php');
    exit();
}


// Flash Messages
if (isset($_SESSION['flash_message'])) {
    $view->message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}


require_once(__DIR__ . "/Views/pets.phtml");