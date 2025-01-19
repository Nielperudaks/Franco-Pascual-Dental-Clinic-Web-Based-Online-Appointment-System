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
$pending = "Pending";

$stmt = $conn->prepare("
    INSERT INTO tbl_treatment_records (
        Client_ID, Treatment_Date, Treatment_Name, Treatment_Cost,
        Dentist, Payment_Status, Selected_Tooth, Transaction_Code
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$stmt) {
    echo "Preparation failed: " . $conn->error;
    exit;
}

// Bind parameters
$stmt->bind_param(
    "issdssss",  // Updated with correct types (assuming Treatment_Cost is a decimal)
    $_POST['clientID'],
    $_POST['date'],
    $_POST['procedure'],
    $_POST['AmtCharged'],
    $_POST['dentist'],
    $pending,
    $_POST['selectedTeeth'],
    $_POST['transactionCode']
);

if ($stmt->execute()) {
    echo 'Transaction Saved';
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>