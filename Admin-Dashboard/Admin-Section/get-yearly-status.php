<?php
$conn = require __DIR__ . "../../connection.php";

$currentYear = date('Y');

$query = "SELECT 
    MONTH(AppointmentDate) as month,
    Status,
    COUNT(*) as count
FROM tbl_transaction
WHERE YEAR(AppointmentDate) = ?
    AND Status IN ('Done', 'Cancelled', 'No Response')
GROUP BY MONTH(AppointmentDate), Status
ORDER BY month, Status";

$stmt = $conn->prepare($query);
$stmt->bind_param('s', $currentYear);
$stmt->execute();
$result = $stmt->get_result();

$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
$stmt->close();
$conn->close();
?>