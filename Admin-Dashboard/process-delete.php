<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: Login-Registration/");
    exit();
}

$conn = require __DIR__ . "/../connection.php"; // Note the corrected path

// Use prepared statement
$query = "SELECT * FROM tbl_admin WHERE Admin_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION["userID"]); // "i" for integer
$stmt->execute();
$result = $stmt->get_result();
$validateUser = $result->fetch_assoc();

if (!$validateUser || ($validateUser['Access_Level'] != 1 && $validateUser['Access_Level'] != 2)) {
    header("Location: Login-Registration/");
    exit();
}

   
    if(isset($_POST['code'])) {

        $transactionCode =  $_POST['code'];
        $query = "DELETE FROM tbl_transaction WHERE Transaction_Code = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $transactionCode);
        if($stmt->execute()){
            $query = "UPDATE tbl_clients SET Status = null";
            $stmt = $conn->prepare($query);
            $stmt->execute();
        
            
        
            echo "Success";
            }               
    } else {
       

        echo "Error: Transaction code is required.";
    }
             $stmt->close();    
        
            $conn->close();
?>