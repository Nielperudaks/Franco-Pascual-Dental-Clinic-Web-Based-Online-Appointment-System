<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: ../Login-Registration/");
    exit();
}

$conn = require __DIR__ . "../../connection.php";
    $query = "SELECT * FROM tbl_admin WHERE Admin_ID = {$_SESSION["userID"]}";
    $result = $conn->query($query);
    $validateUser = $result->fetch_assoc();

    if (!$validateUser || $validateUser['Access_Level'] != 2) {
        header("Location: ../Login-Registration/");
        exit();
    }



// Create cache directory if it doesn't exist
// 0777 gives full read, write, and execute permissions (be cautious in production)
if (!file_exists($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}

// Read raw webhook data from PHP input stream
// This captures the raw POST data sent to the webhook endpoint
$webhookData = file_get_contents('php://input');

// POTENTIAL ISSUE: These paths look incorrect
// The '../cache' might not resolve correctly depending on script location
$debugLogFile = __DIR__ . '/webhook_debug.log';
$logFile = __DIR__ . 'paymongo_webhook_log.txt';


// Log the received webhook data
// FILE_APPEND ensures new logs are added to the end of the file
if (!is_writable(dirname($debugLogFile))) {
    error_log("Cannot write to log directory: " . dirname($debugLogFile));
}

// Log full webhook data structure for investigation
file_put_contents($debugLogFile, date('Y-m-d H:i:s') . " Webhook Data Structure: " . print_r($webhookArray, true) . "\n", FILE_APPEND);

// Decode the JSON webhook data
$webhookArray = json_decode($webhookData, true);

if ($webhookArray) {
    // Create a log entry with a unique ID, timestamp, and extracted data
    $logEntry = json_encode([
        'id' => uniqid(), // Generate a unique identifier
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => [
            // Safely extract data using null coalescing operator (??)
            'transaction_code' => $webhookArray['data']['attributes']['data']['attributes']['remarks'] ?? '',
            'amount' => ($webhookArray['data']['attributes']['data']['attributes']['amount'] ?? 0) / 100, // Convert cents to dollars
            'status' => $webhookArray['data']['attributes']['data']['attributes']['status'] ?? ''
        ]
    ]) . "\n";

    // Write log entry to file
    // LOCK_EX prevents concurrent write attempts
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    
    // Update transaction status in database
    $transactionCode = $webhookArray['data']['attributes']['data']['attributes']['remarks'] ?? '';
    if ($transactionCode) {
        $query = "UPDATE tbl_transaction SET Payment_Status = 'Paid' WHERE Transaction_Code = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $transactionCode);
        $stmt->execute();
    }

    // Respond with success
    http_response_code(200);
    echo json_encode(['status' => 'success']);
} else {
    // Log parsing failure
    file_put_contents($debugLogFile, date('Y-m-d H:i:s') . " Failed to parse webhook data\n", FILE_APPEND);
    
    // Respond with error
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid webhook data']);
}
?>