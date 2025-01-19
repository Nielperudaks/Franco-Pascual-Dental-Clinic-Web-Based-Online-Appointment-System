<?php
session_start();

// Check if session user ID is not set
if (!isset($_SESSION["userID"])) {
    header("Location: ../Login-Registration/");
    exit();
}

$conn = require __DIR__ . "../../../connection.php";

$query = "SELECT * FROM tbl_admin WHERE Admin_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION["userID"]);
$stmt->execute();
$result = $stmt->get_result();
$validateUser = $result->fetch_assoc();

// Check if user is not found or doesn't have correct access level
if (!$validateUser || $validateUser['Access_Level'] != 1) {
    header("Location: ../Login-Registration/");
    exit();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$status = $_POST['status'] ?? 'All';
$doctorID = $_POST['doctorID'];

$query = "SELECT * FROM tbl_transaction WHERE Doctor_ID = ?";
$params = [$doctorID];

if ($status !== "All") {
    $query .= " AND Status = ?";
    $params[] = $status;
}

$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$types = str_repeat("s", count($params));
$stmt->bind_param($types, ...$params);

if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();

$transactions = [];
while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
}

// Close statement and connection
$stmt->close();
$conn->close();

echo json_encode($transactions);
?>