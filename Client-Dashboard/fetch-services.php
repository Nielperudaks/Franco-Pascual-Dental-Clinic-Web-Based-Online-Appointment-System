<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION['valid'] != true) {
    header("Location: ../Patient_Login/index.php");
    exit;
}

$conn = require __DIR__."/connection.php";


$query = "SELECT Service_ID, ServiceName FROM tbl_services";
$result = $conn->query($query);


$services = array();


while($row = $result->fetch_assoc()) {
    $services[] = $row;
}


$conn->close();


echo json_encode($services);
?>