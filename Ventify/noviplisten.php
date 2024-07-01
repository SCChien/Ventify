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

$id_query = "SELECT role FROM users WHERE username = '$username'";
$id_result = $conn->query($id_query);
$userRole = 'NORMAL USER'; // Default role, 使用大写

if ($id_result->num_rows == 1) {
    $row = $id_result->fetch_assoc();
    $userRole = $row['role'];
}

echo json_encode(['userRole' => $userRole]);
?>
