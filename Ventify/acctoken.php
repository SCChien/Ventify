<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include('./core/conn.php');

$data = json_decode(file_get_contents('php://input'), true);
$token = $data['token'];

// 验证Token并获取共享的歌曲信息
$stmt = $conn->prepare("SELECT username FROM song_tokens WHERE token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Invalid token']);
    exit;
}

$row = $result->fetch_assoc();
$shared_username = $row['username'];

$downloads_dir = 'downloads/';
$user_dir = $downloads_dir . $shared_username;

if (!is_dir($user_dir)) {
    echo json_encode(['error' => 'No songs found']);
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

echo json_encode(['success' => true, 'songs' => $songDetails]);
?>
