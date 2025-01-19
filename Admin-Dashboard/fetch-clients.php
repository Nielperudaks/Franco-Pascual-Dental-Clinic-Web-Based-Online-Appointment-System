<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: Login-Registration/");
    exit();
}

$conn = require __DIR__ . "/../connection.php"; // Note the corrected path

// Use prepared statement
$query = "SELECT * FROM tbl_admin WHERE Admin_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION["userID"]); // "i" for integer
$stmt->execute();
$result = $stmt->get_result();
$validateUser = $result->fetch_assoc();

if (!$validateUser || ($validateUser['Access_Level'] != 1 && $validateUser['Access_Level'] != 2)) {
    header("Location: Login-Registration/");
    exit();
}

// Include your database connection file


// Prepare the SQL query to fetch clients
$query = "SELECT Client_ID, FirstName, LastName FROM tbl_clients";
$result = $conn->query($query);

// Array to store the clients
$clients = array();

// Fetch each row from the result set
while ($row = $result->fetch_assoc()) {
    $clients[] = $row;
}

// Close the database connection
$conn->close();

// Return the array of clients as JSON
echo json_encode($clients);
?>