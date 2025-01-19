<?php
require __DIR__ . '../../../connection.php';

$secretKey = 'sk_test_Rv211wBmLpq98pnC9wx6GXwE';

function createPaymongoLink($amount, $transactionCode) {
    global $secretKey;
    
    // Remove any non-numeric characters and convert to centavos
    $cleanAmount = preg_replace('/[^0-9.]/', '', $amount);
    $amountInCentavos = (float)$cleanAmount * 100;
    
    $data = [
        'data' => [
            'attributes' => [
                'amount' => (int)$amountInCentavos,
                'description' => 'Payment for Transaction: ' . $transactionCode,
                'remarks' => $transactionCode,
                'currency' => 'PHP',
            ]
        ]
    ];

    // Create PayMongo link
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.paymongo.com/v1/links');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Basic ' . base64_encode($secretKey . ':')
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    // Simulate webhook immediately for testing
    $webhookData = json_encode([
        'data' => [
            'id' => uniqid(),
            'attributes' => [
                'type' => 'payment.paid',
                'data' => [
                    'attributes' => [
                        'amount' => (int)$amountInCentavos,
                        'remarks' => $transactionCode,
                        'status' => 'paid'
                    ]
                ]
            ]
        ]
    ]);

    // Send webhook data to webhook_listener.php
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'webhook_listener.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $webhookData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $webhookResponse = curl_exec($ch);
    error_log("Webhook Response: " . $webhookResponse);
    curl_close($ch);

    return json_decode($response, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $transactionCode = $_POST['transaction_code'];
    $amount = $_POST['amount'];

    $paymentLink = createPaymongoLink($amount, $transactionCode);

    if (isset($paymentLink['data']['attributes']['checkout_url'])) {
        echo json_encode([
            'success' => true,
            'checkout_url' => $paymentLink['data']['attributes']['checkout_url'],
            'amount' => $amount,
            'transaction_code' => $transactionCode
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create payment link',
            'error' => $paymentLink['errors'] ?? 'Unknown error'
        ]);
    }
}
?> 