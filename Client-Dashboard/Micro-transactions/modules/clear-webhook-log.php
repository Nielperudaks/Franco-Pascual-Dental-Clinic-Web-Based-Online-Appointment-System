<?php
$cacheDir = __DIR__ . '/../../cache';
if (!file_exists($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}

$logFile = $cacheDir . '/paymongo_webhook_log.txt';

if (file_put_contents($logFile, '', LOCK_EX) !== false) {
    echo "Webhook log cleared successfully.";
} else {
    echo "Failed to clear webhook log.";
}
?> 