<?php
require_once('connection.php');

// Query to get total appointments where status is 'Done'
$totalDoneAppointmentsQuery = "SELECT COUNT(*) as total_done_appointments FROM tbl_transaction WHERE Status = 'Done'";
$totalDoneAppointmentsResult = $conn->query($totalDoneAppointmentsQuery);
$totalDoneAppointments = 0;

if ($totalDoneAppointmentsResult && $totalDoneAppointmentsResult->num_rows > 0) {
    $row = $totalDoneAppointmentsResult->fetch_assoc();
    $totalDoneAppointments = $row['total_done_appointments'];
}

// Get the current year
$currentYear = date('Y');

// Query to get appointments where status is 'Done' for the current year
$yearDoneAppointmentsQuery = "SELECT COUNT(*) as year_done_appointments FROM tbl_transaction WHERE Status = 'Done' AND YEAR(AppointmentDate) = $currentYear";
$yearDoneAppointmentsResult = $conn->query($yearDoneAppointmentsQuery);
$yearDoneAppointments = 0;

if ($yearDoneAppointmentsResult && $yearDoneAppointmentsResult->num_rows > 0) {
    $row = $yearDoneAppointmentsResult->fetch_assoc();
    $yearDoneAppointments = $row['year_done_appointments'];
}

$conn->close();

// Return the data as JSON
echo json_encode([
    'total_done_appointments' => $totalDoneAppointments,
    'year_done_appointments' => $yearDoneAppointments
]);
?>
