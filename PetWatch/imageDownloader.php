<?php
// 1. UPDATE THESE TO MATCH YOUR DB!
$host = '127.0.0.1';
$db = 'sge969';
$port = 3305;
$user = 'sge969';
$pass = '2mOzm3m7W58CrZS';

$pdo = new PDO("mysql:host=$host;dbname=$db;port=$port;charset=utf8mb4", $user, $pass);

// Ensure the images folder exists
$imageFolder = __DIR__ . '/Views/images/';
if (!is_dir($imageFolder)) {
    mkdir($imageFolder, 0777, true);
}

// Find pets that DO NOT have a photo yet. Limit to 20 at a time to prevent crashing.
$stmt = $pdo->query("SELECT id, species FROM pets WHERE photo_url IS NULL LIMIT 20");
$petsToProcess = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($petsToProcess) === 0) {
    die("<h1 style='color:green'>All pets have images! Process complete.</h1>");
}

echo "<h1>Downloading Images (Batch of 20)...</h1>";

$updateStmt = $pdo->prepare("UPDATE pets SET photo_url = ? WHERE id = ?");

foreach ($petsToProcess as $pet) {
    $species = urlencode($pet['species']);
    $url = "https://loremflickr.com/320/240/" . $species;

    // Create a unique filename based on the pet ID
    $filename = "pet_" . $pet['id'] . "_" . time() . ".jpg";
    $filepath = $imageFolder . $filename;

    echo "Downloading image for Pet #{$pet['id']} ({$pet['species']})...<br>";

    // Download and save
    $imageData = @file_get_contents($url);
    if ($imageData !== false) {
        file_put_contents($filepath, $imageData);
        // Save the filename to the database
        $updateStmt->execute([$filename, $pet['id']]);
    } else {
        echo "<span style='color:red'>Failed to download for Pet #{$pet['id']}</span><br>";
    }
}

echo "<h2>Batch complete. Reloading next batch in 3 seconds...</h2>";
?>
<meta http-equiv="refresh" content="3">