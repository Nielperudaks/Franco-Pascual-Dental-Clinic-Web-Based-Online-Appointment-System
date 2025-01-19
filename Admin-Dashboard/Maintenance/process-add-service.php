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
   
    if(isset($_POST['name']) && isset($_POST['description']) && isset($_POST['duration'])) {

        $name =  $_POST['name'];
        $description = $_POST['description'];
        $duration = $_POST['duration'];
        $addModel = $_POST['addModel'];
        $price = $_POST['price'];
        
        $query = "INSERT INTO tbl_services (ServiceName, Description, Duration, AddModel, Price) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare(query: $query);
        $stmt->bind_param(
            "ssisd", 
            $name,
            $description,
            $duration,
            $addModel,
            $price 
        );
        if($stmt->execute()){     
            echo "Service addition success";
            }               
    } else {
       

        echo "Error: addition failed.";
    }
             $stmt->close();    
        
            $conn->close();
?>