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

$logFile = __DIR__ . 'paymongo_webhook_log.txt';
$debugLogFile = __DIR__ . 'webhook_debug.log';

if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    $entries = array_filter(explode("\n", $content));
    
    if (!empty($entries)) {
        $lastEntry = end($entries);
        $webhookData = json_decode($lastEntry, true);
        
        file_put_contents($debugLogFile, date('Y-m-d H:i:s') . " Reading webhook: " . print_r($webhookData, true) . "\n", FILE_APPEND);
        
        if ($webhookData && isset($webhookData['id'])) {
            $lastShownId = $_SESSION['last_shown_webhook_id'] ?? '';
            
            if (!isset($_SESSION['last_shown_webhook_id']) || $webhookData['id'] !== $lastShownId) {
                $_SESSION['last_shown_webhook_id'] = $webhookData['id'];
                
                echo json_encode([
                    'isNew' => true,
                    'message' => "Transaction: {$webhookData['data']['transaction_code']}<br>" .
                                "Amount: ₱{$webhookData['data']['amount']}<br>" .
                                "Status: {$webhookData['data']['status']}"
                ]);
                exit;
            }
        }
    }
}

echo json_encode(['status' => 'no_updates']);
?>