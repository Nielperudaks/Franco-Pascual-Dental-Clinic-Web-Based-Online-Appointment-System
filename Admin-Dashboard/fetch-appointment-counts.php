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