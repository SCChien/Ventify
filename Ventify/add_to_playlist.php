<?php
session_start();
include('./core/conn.php');

if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    $id_query = "SELECT id FROM users WHERE username = ?";
    $stmt = $conn->prepare($id_query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $id_result = $stmt->get_result();

    if ($id_result->num_rows == 1) {
        $row = $id_result->fetch_assoc();
        $user_id = $row['id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve song name and singer from POST request
            $currentSongName = $_POST['songName'];
            $currentSinger = $_POST['singer'];

            // Load existing playlist data
            $playlistFile = './sql/playlist.json';
            $existingData = file_get_contents($playlistFile);
            $playlistData = json_decode($existingData, true);

            // Check if the song already exists in the playlist
            $isDuplicate = false;
            foreach ($playlistData as $entry) {
                if ($entry['username'] === $username) {
                    foreach ($entry['songs'] as $song) {
                        if ($song['songName'] === $currentSongName && $song['artistName'] === $currentSinger) {
                            $isDuplicate = true;
                            break 2; // Exit both loops if duplicate is found
                        }
                    }
                }
            }

            // If the song is not a duplicate, add it to the playlist
            if (!$isDuplicate) {
                // Find the index of the user's playlist data if it exists
                $userIndex = -1;
                foreach ($playlistData as $index => $entry) {
                    if ($entry['username'] === $username) {
                        $userIndex = $index;
                        break;
                    }
                }

                // Add the new song entry to the user's playlist data
                if ($userIndex !== -1) {
                    $songEntry = array(
                        'songName' => $currentSongName,
                        'artistName' => $currentSinger
                    );
                    // Append the new song entry to the existing list of songs
                    $playlistData[$userIndex]['songs'][] = $songEntry;
                } else {
                    // If user's playlist data doesn't exist, create new entry
                    $playlistData[] = array(
                        'songs' => array(
                            array(
                                'songName' => $currentSongName,
                                'artistName' => $currentSinger
                            )
                        ),
                        'username' => $username
                    );
                }

                // Encode the updated playlist data as JSON
                $jsonPlaylistData = json_encode($playlistData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

                // Write the updated playlist data back to the file
                file_put_contents($playlistFile, $jsonPlaylistData);

                echo "Song added to playlist successfully.";
            } else {
                echo "Error: Song already exists in the playlist.";
            }
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
