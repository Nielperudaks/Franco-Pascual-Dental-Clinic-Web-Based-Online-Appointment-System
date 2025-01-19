<?php
require __DIR__ . '../../../connection.php';

$cacheDir = __DIR__ . '../cache';
if (!file_exists($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}

$webhookData = file_get_contents('php://input');

$debugLogFile = $cacheDir . '/webhook_debug.log';
$logFile = $cacheDir . '/paymongo_webhook_log.txt';

file_put_contents($debugLogFile, date('Y-m-d H:i:s') . " Received webhook: " . $webhookData . "\n", FILE_APPEND);

$webhookArray = json_decode($webhookData, true);

if ($webhookArray) {
    $logEntry = json_encode([
        'id' => uniqid(),
        'timestamp' => date('Y-m-d H:i:s'),
        'data' => [
            'transaction_code' => $webhookArray['data']['attributes']['data']['attributes']['remarks'] ?? '',
            'amount' => ($webhookArray['data']['attributes']['data']['attributes']['amount'] ?? 0) / 100,
            'status' => $webhookArray['data']['attributes']['data']['attributes']['status'] ?? ''
        ]
    ]) . "\n";

    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    
    $transactionCode = $webhookArray['data']['attributes']['data']['attributes']['remarks'] ?? '';
    if ($transactionCode) {
        $query = "UPDATE tbl_transaction SET Payment_Status = 'Paid' WHERE Transaction_Code = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('s', $transactionCode);
        $stmt->execute();
    }

    http_response_code(200);
    echo json_encode(['status' => 'success']);
} else {
    file_put_contents($debugLogFile, date('Y-m-d H:i:s') . " Failed to parse webhook data\n", FILE_APPEND);
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid webhook data']);
}
?>
