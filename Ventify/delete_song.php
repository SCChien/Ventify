<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$username = $_SESSION['username'];
$jsonData = file_get_contents('./sql/album.json');
$database = json_decode($jsonData, true, 512, JSON_UNESCAPED_UNICODE);

if (isset($_POST['album'], $_POST['index']) && isset($database[$username]['albums'][$_POST['album']])) {
    $albumName = $_POST['album'];
    $songIndex = intval($_POST['index']);
    if (isset($database[$username]['albums'][$albumName][$songIndex])) {
        $song = $database[$username]['albums'][$albumName][$songIndex];
        unset($database[$username]['albums'][$albumName][$songIndex]);
        
        $songFilePath = "downloads/$username/{$song['song']}.mp3";
        if (file_exists($songFilePath)) {
            unlink($songFilePath);
        }

        $thumbnailExtensions = ['jpg', 'jpeg', 'png', 'webp'];
        foreach ($thumbnailExtensions as $ext) {
            $thumbnailPath = "downloads/$username/{$song['song']}.$ext";
            if (file_exists($thumbnailPath)) {
                unlink($thumbnailPath);
            }
        }

        if (empty($database[$username]['albums'][$albumName])) {
            unset($database[$username]['albums'][$albumName]);
        }

        file_put_contents('./sql/album.json', json_encode($database, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Song not found']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid album or song index']);
}
?>
