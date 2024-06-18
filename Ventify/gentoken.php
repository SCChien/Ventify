<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

include('./core/conn.php');

$username = $_SESSION['username'];
$token = bin2hex(random_bytes(16)); // 生成一个随机Token

// 将Token保存到数据库
$stmt = $conn->prepare("INSERT INTO song_tokens (username, token) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $token);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'token' => $token]);
} else {
    echo json_encode(['error' => 'Database error']);
}
?>