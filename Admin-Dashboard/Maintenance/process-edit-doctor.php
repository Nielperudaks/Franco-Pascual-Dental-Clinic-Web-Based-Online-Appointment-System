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
$doctorId = $_POST['doctorId'] ?? null;
$doctorName = $_POST['doctorName'] ?? null;
$password = $_POST['password'] ?? null;
$services = $_POST['services'] ?? [];


if (empty($doctorId)) {
    echo "Doctor ID is missing.";
    exit;
}

if (empty($doctorName)) {
    echo "Doctor name cannot be empty.";
    exit;
}

if (empty($password)) {
    echo "Password cannot be empty.";
    exit;
}


if (empty($services)) {
    echo "At least one service must be selected.";
    exit;
}

try {

    $updateDoctorQuery = "UPDATE tbl_doctors SET Doctor_Name = ? WHERE Doctor_ID = ?";
    $stmt = $conn->prepare($updateDoctorQuery);
    $stmt->bind_param("ss", $doctorName, $doctorId);
    $stmt->execute();


    $deleteServicesQuery = "DELETE FROM tbl_doctor_services WHERE Doctor_ID = ?";
    $stmt = $conn->prepare($deleteServicesQuery);
    $stmt->bind_param("s", $doctorId);
    $stmt->execute();

    $insertServiceQuery = "INSERT INTO tbl_doctor_services (Doctor_ID, Service_ID) VALUES (?, ?)";
    $stmt = $conn->prepare($insertServiceQuery);
    foreach ($services as $serviceId) {
        $stmt->bind_param("ss", $doctorId, $serviceId);
        $stmt->execute();
    }

    echo "Doctor details updated successfully";
} catch (Exception $e) {
    echo "An error occurred: " . $e->getMessage();
} finally {
    $conn->close();
}
?>