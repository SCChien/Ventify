<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo "User not logged in.";
    exit();
}

$username = $_SESSION['username'];
$albumName = $_GET['album'];

if (empty($albumName)) {
    echo "No album specified.";
    exit();
}

$jsonData = file_get_contents('./sql/album.json');
$database = json_decode($jsonData, true, 512, JSON_UNESCAPED_UNICODE);
$user_dir = "downloads/$username";
$files = scandir($user_dir);

if (isset($database[$username]['albums'][$albumName])) {
    if (empty($database[$username]['albums'][$albumName])) {
        echo "<p class='no-song'>No song</p>";
    } else {
        foreach ($database[$username]['albums'][$albumName] as $song) {
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'mp3' && $song['song'] == pathinfo($file, PATHINFO_FILENAME)) {
                    $safe_title = pathinfo($file, PATHINFO_FILENAME);
                    $thumbnail_path_array = glob("$user_dir/$safe_title.{jpg,jpeg,png,webp}", GLOB_BRACE);
                    $thumbnail_path = $thumbnail_path_array ? $thumbnail_path_array[0] : './image/main/est.jpg';
                    echo "<div class='song'>";
                    echo"<img src='" . $song['thumbnail'] . "' alt='thumbnail'>";
                    echo "<li><a href=\"#\" onclick=\"playSong('$user_dir/$file', '$safe_title', '$thumbnail_path')\">$safe_title</a></li>";
                    echo "<form method='POST' action='playlist.php'>";
                    echo "<input type='hidden' name='album' value='$albumName'>";
                    echo "<input type='hidden' name='song_to_delete' value='" . $song['song'] . "'>";
                    echo "<button class='deleteAlbumBtn' type='submit' name='delete_song'>Delete</button>";
                    echo "</form>";
                    echo "</div>";
                }
            }
        }
    }
}else{
    echo "Album does not exist.";
}

?>
