<?php
// Redirect if accessed directly via the URL
if ($_SERVER['PHP_SELF'] === '/connection.php') {
    header("Location: 404.php"); // Change to your desired page, such as a 404 or error page
    exit;
}

// Define database credentials
$servername = "localhost";
$username = "rm2bgfu7h06d";
$password = "oNQv1M!ejV0P";
$dbName = "capstone_db";

// Check if credentials are set and not empty
if (empty($servername) || empty($username) || empty($password) || empty($dbName)) {
    // Redirect to an error page if any credentials are missing
    header("Location: 404.php"); // Change this to your desired error page
    exit;
}

// Establish database connection
$conn = new mysqli(
    hostname: $servername,
    username: $username,
    password: $password,
    database: $dbName
);

// Check connection
if ($conn->connect_error) {
    // Redirect to a 404 page if connection fails
    header("Location: 404.php"); // Change this to your desired 404 page
    exit;
}

// Return the connection object
return $conn;
?>