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