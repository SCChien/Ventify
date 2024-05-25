<?php
session_start();

// 检查用户是否登录，如果未登录则跳转到登录页面
if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// 获取当前登录的用户名
$username = $_SESSION['username'];

// 读取 JSON 数据库
$jsonData = file_get_contents('album.json');
$database = json_decode($jsonData, true);

// 检查用户是否存在于数据库中，如果不存在则创建新用户记录
if(!isset($database[$username])) {
    $database[$username] = array(
        "username" => $username,
        "albums" => array()
    );
}

// 处理用户的专辑操作
if(isset($_POST['create_album'])) {
    $albumName = $_POST['album_name'];
    $database[$username]['albums'][$albumName] = array();
    saveDatabase($database);
    echo "<p>Album '$albumName' created successfully!</p>";
}

if(isset($_POST['add_song'])) {
    $albumName = $_POST['album'];
    $songName = $_POST['song_name'];
    $author = $_POST['author'];
    $database[$username]['albums'][$albumName][] = "$songName, $author";
    saveDatabase($database);
    echo "<p>Song '$songName' added to album '$albumName' successfully!</p>";
}

// 显示用户的专辑和歌曲列表
echo "<h2>Albums for $username</h2>";
foreach($database[$username]['albums'] as $albumName => $playlist) {
    echo "<h3>$albumName</h3>";
    echo "<ul>";
    foreach($playlist as $song) {
        echo "<li>$song</li>";
    }
    echo "</ul>";
}

// 添加专辑的表单
echo "<h3>Create Album</h3>";
echo "<form method='post'>";
echo "<label for='album_name'>Album Name:</label>";
echo "<input type='text' id='album_name' name='album_name' required><br><br>";
echo "<input type='submit' name='create_album' value='Create Album'>";
echo "</form>";

// 添加歌曲的表单
echo "<h3>Add Song</h3>";
echo "<form method='post'>";
echo "<label for='album'>Select Album:</label>";
echo "<select id='album' name='album'>";
foreach($database[$username]['albums'] as $albumName => $playlist) {
    echo "<option value='$albumName'>$albumName</option>";
}
echo "</select><br><br>";
echo "<label for='song_name'>Song Name:</label>";
echo "<input type='text' id='song_name' name='song_name' required><br><br>";
echo "<label for='author'>Author:</label>";
echo "<input type='text' id='author' name='author' required><br><br>";
echo "<input type='submit' name='add_song' value='Add Song'>";
echo "</form>";

// 保存数据库到 JSON 文件
function saveDatabase($database) {
    $jsonData = json_encode($database, JSON_PRETTY_PRINT);
    file_put_contents('your_json_database.json', $jsonData);
}
?>
