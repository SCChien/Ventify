<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$username = $_SESSION['username'];
$jsonData = file_get_contents('./sql/album.json');
$database = json_decode($jsonData, true, 512, JSON_UNESCAPED_UNICODE);

if (isset($_POST['album']) && isset($database[$username]['albums'][$_POST['album']])) {
    $albumName = $_POST['album'];
    $songs = $database[$username]['albums'][$albumName];
    echo json_encode(['success' => true, 'songs' => $songs]);
} else {
    echo json_encode(['success' => false, 'message' => 'Album not found']);
}
?>
