<?php
header('Content-Type: application/json'); // Add this line
$conn = require __DIR__ . "../../connection.php";

$query = "SELECT Service, COUNT(*) as count 
          FROM tbl_transaction 
          WHERE Status = 'Done' 
          GROUP BY Service 
          ORDER BY count DESC";

$result = $conn->query($query);
$data = array();

while ($row = $result->fetch_assoc()) {
    $row['count'] = intval($row['count']); // Convert count to integer
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
?>