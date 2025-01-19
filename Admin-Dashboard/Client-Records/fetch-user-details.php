<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: ../Login-Registration/");
    exit();
}

$conn = require __DIR__ . "/../connection.php";
    $query = "SELECT * FROM tbl_admin WHERE Admin_ID = {$_SESSION["userID"]}";
    $result = $conn->query($query);
    $validateUser = $result->fetch_assoc();

    if (!$validateUser || $validateUser['Access_Level'] != 2) {
        header("Location: ../Login-Registration/");
        exit();
    }

// Prepare SQL statement to fetch services
if (isset($_POST['clientId'])) {
    $ID = $_POST['clientId'];
    $query = "SELECT * FROM tbl_clients WHERE Client_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "i", 
        $ID,
       
    );
    if($stmt->execute()){
        $result=$stmt->get_result();
        $validateUser=$result->fetch_assoc();
        $conn->close();
    
       
        echo json_encode($validateUser);
    }
   
} 