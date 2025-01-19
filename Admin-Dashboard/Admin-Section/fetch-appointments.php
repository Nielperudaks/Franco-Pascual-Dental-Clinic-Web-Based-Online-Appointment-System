<?php
require_once('connection.php');

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$serviceCounts = [];

for ($month = 1; $month <= 12; $month++) {
    $query = $conn->prepare("SELECT COUNT(*) AS count FROM tbl_transaction WHERE Status = 'Done' AND YEAR(AppointmentDate) = ? AND MONTH(AppointmentDate) = ?");
    $query->bind_param('ii', $year, $month);
    $query->execute();
    $result = $query->get_result();
    $row = $result->fetch_assoc();
    $serviceCounts[] = $row['count'];
}

echo json_encode($serviceCounts);

$conn->close();
?>
