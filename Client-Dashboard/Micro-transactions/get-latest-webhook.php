<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION['valid'] != true) {
    header("Location: ../../Patient_Login/index.php");
    exit;
}

$logFile = __DIR__ . '../cache/paymongo_webhook_log.txt';
$debugLogFile = __DIR__ . '../cache/webhook_debug.log';

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