<?php
// Include your database connection logic
$conn = require __DIR__ . "../../../connection.php";
$pending = "Pending";
$stmt = $conn->prepare("
    INSERT INTO tbl_treatment_records(
    Client_ID, Treatment_Date, Treatment_Name, Treatment_Cost,
    Dentist, Payment_Status, Selected_Tooth, Transaction_Code
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        
");

// Bind parameters (all as strings `s` for simplicity, adjust if needed)
$stmt->bind_param(
    "isssssss",
    $_POST['clientID'],
    $_POST['date'],
    $_POST['procedure'],
    $_POST['AmtCharged'],
    $_POST['dentist'],
    $pending,
    $_POST['selectedTeeth'],
    $_POST['transactionCode']
    
);

if($stmt->execute()){
    echo 'Transaction Saved';
}
// Execute the statement


// Clean up
$stmt->close();
$conn->close();
?>
