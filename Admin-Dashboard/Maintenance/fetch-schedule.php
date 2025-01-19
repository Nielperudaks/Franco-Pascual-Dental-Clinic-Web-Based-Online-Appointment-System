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

    $query = "SELECT * FROM tbl_working_hours";
    $result = $conn->query($query);
    $schedules = array();

    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }

    $conn->close();
    echo json_encode(['schedules' => $schedules]);