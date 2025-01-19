<?php
header('Content-Type: application/json');
$conn = require __DIR__ . "../../connection.php";

$query = "SELECT 
            Treatment_Name,
            SUM(Treatment_Cost) as total_cost
          FROM tbl_treatment_records
          WHERE Payment_Status = 'Paid'
          GROUP BY Treatment_Name
          ORDER BY total_cost DESC";

$result = $conn->query($query);
$data = array();

while ($row = $result->fetch_assoc()) {
    // Convert to proper numeric value
    $row['total_cost'] = floatval($row['total_cost']);
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
?>