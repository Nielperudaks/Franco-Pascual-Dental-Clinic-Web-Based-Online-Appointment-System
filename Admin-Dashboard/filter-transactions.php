<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: Login-Registration/");
    exit();
}

$conn = require __DIR__ . "/../connection.php";
    $query = "SELECT * FROM tbl_admin WHERE Admin_ID = {$_SESSION["userID"]}";
    $result = $conn->query($query);
    $validateUser = $result->fetch_assoc();

    if (!$validateUser || ($validateUser['Access_Level'] != 1 && $validateUser['Access_Level'] != 2)) {
        header("Location: Login-Registration/");
        exit();
    }



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$doctor = $_POST['doctor'] ?? 'All';
$status = $_POST['status'] ?? 'All';

$query = "SELECT * FROM tbl_transaction";
$params = [];
$conditions = [];

if ($doctor !== "All") {
    $conditions[] = "Doctor = ?";
    $params[] = $doctor;
}

if ($status !== "All") {
    $conditions[] = "Status = ?";
    $params[] = $status;
}

if (count($conditions) > 0) {
    $query .= " WHERE " . implode(" AND ", $conditions);
}

$stmt = $conn->prepare($query);

if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

if (!empty($params)) {
    $types = str_repeat("s", count($params));
    $stmt->bind_param($types, ...$params);
}

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