<?php

// Database connection parameters
$host = "localhost";
$username = "root";
$password = "";
$database = "music";

// Create a connection to the database
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
