<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// 获取请求中的专辑名称
if (isset($_POST['album'])) {
    $albumName = $_POST['album'];
    
    // 读取 JSON 数据库
    $jsonData = file_get_contents('album.json');
    $database = json_decode($jsonData, true);
    
    // 检查专辑是否存在于用户的专辑列表中
    if (isset($database[$username]['albums'][$albumName])) {
        $songs = $database[$username]['albums'][$albumName];
        
        // 将歌曲数据格式化为JSON
        $response = array(
            "songs" => array_map(function($song) {
                $songDetails = explode(", ", $song);
                return array("title" => $songDetails[0], "author" => $songDetails[1]);
            }, $songs)
        );
        
        // 设置响应头为JSON
        header('Content-Type: application/json');
        echo json_encode($response);
    } else {
        // 专辑不存在时返回空的歌曲列表
        echo json_encode(array("songs" => array()));
    }
} else {
    echo json_encode(array("songs" => array()));
}
?>
