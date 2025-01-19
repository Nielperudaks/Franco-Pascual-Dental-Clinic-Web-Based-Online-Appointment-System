<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: ../Login-Registration/");
    exit();
}
require __DIR__ . '/../../connection.php';

    $query = "SELECT * FROM tbl_admin WHERE Admin_ID = {$_SESSION["userID"]}";
    $result = $conn->query($query);
    $validateUser = $result->fetch_assoc();

    if (!$validateUser || $validateUser['Access_Level'] != 2) {
        header("Location: ../Login-Registration/");
        exit();
    }


$secretKey = 'sk_test_d59eYRrHhuYjY9FxeGfkPWty';

function createPaymongoLink($conn, $amount, $transactionCode, $treatmentName, $pwdNumber = null, $discountType = null, $originalAmount = null) {
    global $secretKey;
    
    // Remove any non-numeric characters and convert to centavos
    $cleanAmount = preg_replace('/[^0-9.]/', '', $amount);
    $amountInCentavos = (float)$cleanAmount * 100;
    
    // Prepare description with simple text formatting
    $description = "PAYMENT DETAILS\n\n";
    $description .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $description .= "Service: $treatmentName\n";

    if ($pwdNumber && $discountType) {
        $description .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $description .= "\n$discountType ID Number: $pwdNumber\n";
       
        $description .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $description .= " Original Amount:  PHP " . number_format($originalAmount, 2) . "\n";
        $description .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $description .= "Discount (20%):    PHP " . number_format($originalAmount - $amount, 2) . "\n";
        $description .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
        $description .= " Final Amount: PHP " . number_format($amount, 2) . "\n";
    } else {
        $description .= "\nAmount: PHP " . number_format($amount, 2) . "\n";
    }
    $description .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n"; 

    $description .= "\n Transaction Code: $transactionCode";
    
    $data = [
        'data' => [
            'attributes' => [
                'amount' => (int)$amountInCentavos,
                'description' => $description,
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
    
    // Log the response for debugging
    error_log("PayMongo Response: " . $response);
    
    if (curl_errno($ch)) {
        error_log("Curl Error: " . curl_error($ch));
    }
    
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
    
    $updateQuery = "UPDATE tbl_treatment_records SET Treatment_Cost = ? WHERE Transaction_Code = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("ds", $amount, $transactionCode); // Bind amount and transaction code
    if (!$stmt->execute()) {
        error_log("Database Error: " . $stmt->error);
    }
    $stmt->close();
    

    return json_decode($response, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $transactionCode = $_POST['transaction_code'] ?? '';
        $amount = $_POST['amount'] ?? 0;
        $treatmentName = $_POST['treatmentName'] ?? '';
        $pwdNumber = $_POST['pwdNumber'] ?? null;
        $discountType = $_POST['discountType'] ?? null;
        $originalAmount = $_POST['originalAmount'] ?? null;

        // Validate required fields
        if (empty($transactionCode) || empty($amount) || empty($treatmentName)) {
            throw new Exception('Missing required fields');
        }

        $paymentLink = createPaymongoLink(
            $conn,
            $amount, 
            $transactionCode, 
            $treatmentName, 
            $pwdNumber, 
            $discountType, 
            $originalAmount
        );

        if (isset($paymentLink['data']['attributes']['checkout_url'])) {
            echo json_encode([
                'success' => true,
                'checkout_url' => $paymentLink['data']['attributes']['checkout_url'],
                'amount' => $amount,
                'transaction_code' => $transactionCode,
                'treatmentName' => $treatmentName,
                'pwdNumber' => $pwdNumber,
                'discountType' => $discountType,
                'originalAmount' => $originalAmount
            ]);
        } else {
            error_log("PayMongo Error Response: " . json_encode($paymentLink));
            throw new Exception('Failed to create payment link: ' . 
                              ($paymentLink['errors'][0]['detail'] ?? 'Unknown error'));
        }
    } catch (Exception $e) {
        error_log("Payment Processing Error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}
?>