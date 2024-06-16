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

$jsonData = file_get_contents('album.json');
$database = json_decode($jsonData, true, 512, JSON_UNESCAPED_UNICODE);

if (isset($database[$username]['albums'][$albumName])) {
    if (empty($database[$username]['albums'][$albumName])) {
        echo "<p class='no-song'>No song</p>";
    } else {
        foreach ($database[$username]['albums'][$albumName] as $song) {
            echo "<div class='song'>";
            echo "<img src='" . $song['thumbnail'] . "' alt='thumbnail'>";
            echo "<p>" . $song['song'] . "</p>";
            echo "<form method='POST' action='playlist.php'>";
            echo "<input type='hidden' name='album' value='$albumName'>";
            echo "<input type='hidden' name='song_to_delete' value='" . $song['song'] . "'>";
            echo "<button type='submit' name='delete_song'>Delete</button>";
            echo "</form>";
            echo "</div>";
        }
    }
} else {
    echo "Album does not exist.";
}
?>
