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
// Define encryption key
$encryptionKey = '09292222';

// Function to encrypt data
function encryptData($data, $key) {
    $iv = substr(md5($key), 0, 16);  // Use a fixed IV for debugging
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return urlencode(base64_encode($encrypted));  // Encode for URL-safe passing
}

// Get values from the URL parameters
$clientID = $_GET['clientID'] ?? '';
$transactionID = $_GET['transactionID'] ?? '';
$firstName = $_GET['firstName'] ?? '';

// Encrypt each value
$encryptedClientID = encryptData($clientID, $encryptionKey);
$encryptedTransactionID = encryptData($transactionID, $encryptionKey);
$encryptedFirstName = encryptData($firstName, $encryptionKey);

// Redirect to Record-Appointment.php with encrypted parameters
header("Location: Record-Appointment.php?cid={$encryptedClientID}&tid={$encryptedTransactionID}&name={$encryptedFirstName}");
exit();
?>