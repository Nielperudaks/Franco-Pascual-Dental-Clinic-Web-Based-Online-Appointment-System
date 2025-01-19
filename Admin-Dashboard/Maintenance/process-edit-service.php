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
   
    if(isset($_POST['name']) && isset($_POST['description']) && isset($_POST['serviceID'])) {

        $name =  $_POST['name'];
        $description = $_POST['description'];
        $ID = $_POST['serviceID'];
        $price = $_POST['price'];
        $query = "UPDATE tbl_services SET ServiceName = ?, Description = ?, Price = ? WHERE Service_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "ssdi", 
            $name,
            $description,
            $price,
            $ID,

        );
        if($stmt->execute()){     
            echo "Service edit success";
            }               
    } else {
       

        echo "Error: addition failed.";
    }
             $stmt->close();    
        
            $conn->close();
?>