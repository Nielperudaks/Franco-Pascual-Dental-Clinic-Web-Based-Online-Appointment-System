<?php
session_start();

// Check if session user ID is not set
if (!isset($_SESSION["userID"])) {
    header("Location: ../Login-Registration/");
    exit();
}

$conn = require __DIR__ . "../../../connection.php";

$query = "SELECT * FROM tbl_admin WHERE Admin_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION["userID"]);
$stmt->execute();
$result = $stmt->get_result();
$validateUser = $result->fetch_assoc();

// Check if user is not found or doesn't have correct access level
if (!$validateUser || $validateUser['Access_Level'] != 1) {
    header("Location: ../Login-Registration/");
    exit();
}

// Set timezone to Philippines
date_default_timezone_set('Asia/Manila');
$date =  date('Y-m-d'); // Output the server's current date


// Prepare SQL statement to fetch today's appointments
if (isset($_POST['doctorID'])) {
    $ID = $_POST['doctorID'];
    
    $query = "
        SELECT FirstName, Service, Transaction_Code, AppointmentTime, Client_ID
        FROM tbl_transaction 
        WHERE Doctor_ID = ? 
        AND Status = 'Approved'
        AND AppointmentDate = '".$date."'   
    ";

    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $ID);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $appointments = array();

        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }

        echo json_encode($appointments);
    } else {
        echo json_encode(array("error" => "Failed to fetch data"));
    }

    $stmt->close();
    $conn->close();
}
?>