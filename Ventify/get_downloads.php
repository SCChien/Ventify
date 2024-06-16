<?php
session_start();
header('Content-Type: application/json'); // 确保返回的是JSON格式

if (!isset($_SESSION['username'])) {
    http_response_code(401); // 未授权
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include('./core/conn.php');

$username = $_SESSION['username'];
$downloads_dir = 'downloads/';
$user_dir = $downloads_dir . $username;

if (!is_dir($user_dir)) {
    echo json_encode([]);
    exit;
}

$songs = array_diff(scandir($user_dir), array('..', '.'));
$songFiles = array_filter($songs, function($song) {
    return pathinfo($song, PATHINFO_EXTENSION) == 'mp3'; // 只返回mp3文件
});

$thumbnail_extensions = ['jpg', 'jpeg', 'webp', 'png'];

$songDetails = [];
foreach ($songFiles as $song) {
    $thumbnail = '';
    $song_name = pathinfo($song, PATHINFO_FILENAME);

    foreach ($thumbnail_extensions as $ext) {
        $thumbnail_path = $user_dir . '/' . $song_name . '.' . $ext;
        if (file_exists($thumbnail_path)) {
            $thumbnail = $thumbnail_path;
            break;
        }
    }

    $songDetails[] = [
        'path' => $user_dir . '/' . $song,
        'title' => $song_name,
        'thumbnail' => $thumbnail
    ];
}

echo json_encode($songDetails);
?>
