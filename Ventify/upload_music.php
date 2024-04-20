<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "music";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    // Check if file was uploaded without errors
    if (isset($_FILES["music_file"]) && $_FILES["music_file"]["error"] == 0) {
        // Check if all required fields are filled
        if (!empty($_POST["music_name"]) && !empty($_POST["artist"]) && !empty($_POST["category"])) {
            $music_name = $_POST["music_name"];
            $artist = $_POST["artist"];
            $category = $_POST["category"];

            $allowed_extensions = array("mp3");
            $temp = explode(".", $_FILES["music_file"]["name"]);
            $extension = end($temp);

            // Validate file extension
            if (in_array($extension, $allowed_extensions)) {
                // Move the uploaded file to the desired location
                $upload_dir = "upload_music/";
                $new_filename = uniqid() . '.' . $extension;
                $upload_path = $upload_dir . $new_filename;

                if (move_uploaded_file($_FILES["music_file"]["tmp_name"], $upload_path)) {
                    // Insert data into the database
                    $conn = new mysqli($servername, $username, $password, $dbname);
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    $sql = "INSERT INTO music_table (music_name, artist, category, file_path) VALUES ('$music_name', '$artist', '$category', '$upload_path')";

                    if ($conn->query($sql) === TRUE) {
                        echo "File uploaded successfully.";
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                    }

                    $conn->close();
                } else {
                    echo "Error uploading file.";
                }
            } else {
                echo "Invalid file format. Only MP3 files are allowed.";
            }
        } else {
            echo "Please fill all required fields.";
        }
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "Invalid request.";
}
?>
