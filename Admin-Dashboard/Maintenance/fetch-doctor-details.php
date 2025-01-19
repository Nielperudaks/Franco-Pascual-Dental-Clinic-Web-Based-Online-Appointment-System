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


    $doctorId = $_GET['doctorId'];

    // Fetch doctor details
    $doctorQuery = "SELECT Doctor_Name, Status FROM tbl_doctors WHERE Doctor_ID = ?";
    $stmt = $conn->prepare($doctorQuery);
    $stmt->bind_param("s", $doctorId);
    $stmt->execute();
    $doctorResult = $stmt->get_result();
    $doctor = $doctorResult->fetch_assoc();
    
    // Fetch doctor services
    $servicesQuery = "SELECT Service_ID FROM tbl_doctor_services WHERE Doctor_ID = ?";
    $stmt = $conn->prepare($servicesQuery);
    $stmt->bind_param("s", $doctorId);
    $stmt->execute();
    $servicesResult = $stmt->get_result();
    $services = [];
    while ($row = $servicesResult->fetch_assoc()) {
        $services[] = $row;
    }
    
    $response = [
        'doctor' => $doctor,
        'services' => $services
    ];
    
    echo json_encode($response);
    
    $conn->close(); 

?>