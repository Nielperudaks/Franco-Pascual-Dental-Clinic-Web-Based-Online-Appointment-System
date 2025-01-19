<?php
header('Content-Type: application/json');
$conn = require __DIR__ . "../../connection.php";

// Get records for current and previous year
$query = "SELECT Treatment_Cost, Treatment_Date 
          FROM tbl_treatment_records 
          WHERE Payment_Status = 'Paid' 
          AND YEAR(Treatment_Date) >= YEAR(CURRENT_DATE) - 1
          ORDER BY Treatment_Date ASC";

$result = $conn->query($query);
$data = array();

while ($row = $result->fetch_assoc()) {
    $row['Treatment_Cost'] = floatval($row['Treatment_Cost']);
    $data[] = $row;
}

echo json_encode($data);
$conn->close();
?>