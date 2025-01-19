<?php
$debugLogFile = __DIR__ . '../cache/webhook_debug.log';
$webhookLogFile = __DIR__ . '../cache/paymongo_webhook_log.txt';

echo "Debug Log:\n";
if (file_exists($debugLogFile)) {
    echo file_get_contents($debugLogFile);
}

echo "\nWebhook Log:\n";
if (file_exists($webhookLogFile)) {
    echo file_get_contents($webhookLogFile);
}
?> 