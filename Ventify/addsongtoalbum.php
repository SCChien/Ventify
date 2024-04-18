<?php
session_start();

include('conn.php');

if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $id_query = "SELECT id FROM users WHERE username = '$username'";
    $id_result = $conn->query($id_query);

    if ($id_result->num_rows == 1) {
        $row = $id_result->fetch_assoc();
        $user_id = $row['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Load existing album data if exists
            $existingAlbumData = [];
            if(file_exists('./js/album.json')) {
                $existingAlbumData = json_decode(file_get_contents('./js/album.json'), true);
            }

            // Find index of user's album data if exists
            $userIndex = -1;
            foreach ($existingAlbumData as $index => $album) {
                if ($album['username'] === $username) {
                    $userIndex = $index;
                    break;
                }
            }

            // Save album data to JSON file
            $albumData = json_decode(file_get_contents('php://input'), true);

            if ($userIndex !== -1) {
                // If user's album data exists, append new songs to it
                foreach ($albumData['songs'] as $newSong) {
                    $exists = false;
                    foreach ($existingAlbumData[$userIndex]['songs'] as $song) {
                        if ($song['songName'] === $newSong['songName'] && $song['artistName'] === $newSong['artistName']) {
                            $exists = true;
                            break;
                        }
                    }
                    if (!$exists) {
                        $existingAlbumData[$userIndex]['songs'][] = $newSong;
                    }
                }
            } else {
                // If user's album data doesn't exist, add it to existing data
                $albumData['username'] = $username; // Add username to album data
                $existingAlbumData[] = $albumData;
            }

            $jsonAlbumData = json_encode($existingAlbumData, JSON_PRETTY_PRINT);
            file_put_contents('./js/album.json', $jsonAlbumData);

            echo "Album data saved successfully.";
            exit();
        }
    } else {
        echo "Error: User not found.";
        exit();
    }
} else {
    echo "Error: User session not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="image/logo.ico" type="image/x-icon">
    <title>Album</title>
</head>
<body>
    <h1>My Album</h1>
    <div id="album"></div>

    <h2>Add Song</h2>
    <input type="text" id="songName" placeholder="Song Name">
    <input type="text" id="artistName" placeholder="Artist Name">
    <button onclick="addSong()">Add Song</button>

    <script>
        let albumData = [];

        function addSong() {
            const songName = document.getElementById('songName').value;
            const artistName = document.getElementById('artistName').value;

            if (songName && artistName) {
                const newSong = { "songName": songName, "artistName": artistName };
                if (!isDuplicate(newSong)) {
                    albumData.push(newSong);
                    displayAlbum();
                    saveAlbum();
                } else {
                    alert("This song already exists in the album.");
                }
            } else {
                alert("Please enter both song name and artist name.");
            }
        }

        function isDuplicate(newSong) {
            for (let i = 0; i < albumData.length; i++) {
                if (albumData[i].songName === newSong.songName && albumData[i].artistName === newSong.artistName) {
                    return true;
                }
            }
            return false;
        }

        function displayAlbum() {
            const albumContainer = document.getElementById('album');
            albumContainer.innerHTML = '';

            albumData.forEach(song => {
                const songElement = document.createElement('div');
                songElement.innerHTML = `<strong>${song.songName}</strong> by ${song.artistName}`;
                albumContainer.appendChild(songElement);
            });
        }

        function saveAlbum() {
            const xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log(xhr.responseText); // You can handle response if needed
                }
            };
            xhr.open("POST", "<?php echo $_SERVER['PHP_SELF']; ?>", true);
            xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
            xhr.send(JSON.stringify({ "songs": albumData }));
        }
    </script>
    <p><a href="index.php">Back to home</a><p>
</body>
</html>
