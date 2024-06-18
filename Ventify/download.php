<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$songPath = $data['songPath'];
$songTitle = $data['songTitle'];
$thumbnailPath = $data['thumbnailPath'];

$downloads_dir = 'downloads/';
$user_dir = $downloads_dir . $_SESSION['username'];

if (!is_dir($user_dir)) {
    mkdir($user_dir, 0777, true);
}

$song_dest = $user_dir . '/' . basename($songPath);
if (!copy($songPath, $song_dest)) {
    echo json_encode(['error' => 'Failed to copy song']);
    exit;
}

if ($thumbnailPath) {
    $thumbnail_dest = $user_dir . '/' . basename($thumbnailPath);
    if (!copy($thumbnailPath, $thumbnail_dest)) {
        echo json_encode(['error' => 'Failed to copy thumbnail']);
        exit;
    }
}

echo json_encode(['success' => true]);
?>
