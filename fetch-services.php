<?php
// Redirect if accessed directly via the URL and if it's not an AJAX request


// Include the database connection
$conn = require __DIR__ . "/connection.php";

// Check if the connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch services
$sql = "SELECT * FROM tbl_services";
$result = $conn->query($sql);

// Initialize the services array
$services = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Sanitize output data (for XSS protection)
        $services[] = [
            'ServiceName' => htmlspecialchars($row['ServiceName'], ENT_QUOTES, 'UTF-8'),
            'Description' => htmlspecialchars($row['Description'], ENT_QUOTES, 'UTF-8'),
            'Price' => htmlspecialchars($row['Price'], ENT_QUOTES, 'UTF-8')
        ];
    }
}

// Output services as JSON
header('Content-Type: application/json');
echo json_encode($services);

// Close the database connection
$conn->close();
?>