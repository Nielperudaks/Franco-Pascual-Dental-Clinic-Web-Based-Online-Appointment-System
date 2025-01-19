<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION['valid'] != true) {
    header("Location: ../Patient_Login/index.php");
    exit;
}

$conn = require __DIR__ . "../../connection.php";

    $query = "SELECT * FROM tbl_working_hours";
    $result = $conn->query($query);
    $schedules = array();

    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }

    $conn->close();
    echo json_encode(['schedules' => $schedules]);