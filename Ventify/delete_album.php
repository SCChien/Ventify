<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(array('success' => false, 'message' => 'User not logged in.'));
    exit();
}

$username = $_SESSION['username'];
$albumName = $_GET['album'];

if (empty($albumName)) {
    echo json_encode(array('success' => false, 'message' => 'No album specified.'));
    exit();
}

$jsonData = file_get_contents('album.json');
$database = json_decode($jsonData, true, 512, JSON_UNESCAPED_UNICODE);

if (deleteAlbum($username, $albumName, $database)) {
    echo json_encode(array('success' => true));
} else {
    echo json_encode(array('success' => false, 'message' => 'Album could not be deleted or does not exist.'));
}

function deleteAlbum($username, $albumName, &$database)
{
    if ($albumName !== 'favourite' && isset($database[$username]['albums'][$albumName])) {
        unset($database[$username]['albums'][$albumName]);
        saveDatabase($database);
        return true;
    } else {
        return false;
    }
}

function saveDatabase($database)
{
    $jsonData = json_encode($database, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents('album.json', $jsonData);
}
?>

