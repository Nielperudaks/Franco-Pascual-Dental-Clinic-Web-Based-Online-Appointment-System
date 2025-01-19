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


// Fetch doctors
$doctorQuery = "SELECT Doctor_Name FROM tbl_doctors";
$doctorResult = $conn->query($doctorQuery);

$doctors = [];
while ($row = $doctorResult->fetch_assoc()) {
    $doctors[] = $row;
}

// Fetch unique statuses from transactions
$statusQuery = "SELECT DISTINCT Status FROM tbl_transaction";
$statusResult = $conn->query($statusQuery);

$statuses = [];
while ($row = $statusResult->fetch_assoc()) {
    $statuses[] = $row['Status'];
}

// Close connection
$conn->close();

// Return doctors and statuses as JSON
echo json_encode(['doctors' => $doctors, 'statuses' => $statuses]);
?>