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

$stmt = $conn->prepare("
UPDATE tbl_treatment_records SET
Payment_Status = 'Paid'

WHERE Treatment_ID = ?
");
$stmt->bind_param(
"s",
$_POST['TreatmentID']
);

// Execute the statement
if($stmt->execute()){
    echo 'Payment Success';
}

// Clean up
$stmt->close();

$conn->close();
?>