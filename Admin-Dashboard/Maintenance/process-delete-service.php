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
if (isset($_POST['serviceID'])) {
    $ID =  $_POST['serviceID'];
    $checkQuery = "SELECT COUNT(*) as count FROM tbl_transaction WHERE Service_ID = ? AND (Status = 'Approved' OR Status = 'Waiting Approval')";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("i", $ID);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        echo "There are appointments made with this service, cannot remove.";
        $conn->close();
        exit;
    }

    $query = "DELETE FROM tbl_services WHERE Service_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "i",
        $ID,

    );
    if ($stmt->execute()) {
        echo "Service deletion success";
    }
} else {


    echo "Error: deletion failed.";
}
$stmt->close();

$conn->close();