<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

if (isset($_POST['album']) && isset($_POST['index'])) {
    $albumName = $_POST['album'];
    $songIndex = (int) $_POST['index'];
    
    // Read JSON database
    $jsonData = file_get_contents('album.json');
    $database = json_decode($jsonData, true);
    
    // Check if album exists in the user's albums
    if (isset($database[$username]['albums'][$albumName])) {
        // Remove the song from the album
        array_splice($database[$username]['albums'][$albumName], $songIndex, 1);
        
        // Save the updated database back to the JSON file
        file_put_contents('album.json', json_encode($database, JSON_PRETTY_PRINT));
        
        // Respond with success
        echo json_encode(['success' => true]);
    } else {
        // Album doesn't exist
        echo json_encode(['success' => false, 'message' => 'Album not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters']);
}
?>
