<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION['valid'] != true) {
    header("Location: ../Patient_Login/index.php");
    exit;
}
    $conn = require __DIR__."../../connection.php";
   
    if(isset($_POST['serviceId'])) {
        $serviceId = $_POST['serviceId'];
        
        // Prepare the query to prevent SQL injection
        $query = "SELECT AddModel FROM tbl_services WHERE Service_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $serviceId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if($row = $result->fetch_assoc()) {
            echo json_encode([
                'status' => 'success',
                'addModel' => $row['AddModel']
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Service not found'
            ]);
        }
        
        $stmt->close();
        $conn->close();
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No service ID provided'
        ]);
    }
?>