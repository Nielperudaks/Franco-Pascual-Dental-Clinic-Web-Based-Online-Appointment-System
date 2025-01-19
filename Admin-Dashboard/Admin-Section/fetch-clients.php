<?php
// Include your database connection file
require_once('connection.php');

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
