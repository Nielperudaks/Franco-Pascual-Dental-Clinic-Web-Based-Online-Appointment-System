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
// Function to generate unique ID
function generateDoctorID() {
    $timestamp = time();
    $uniqueID = 'doctor_' . $timestamp;
    return $uniqueID;
}


// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required fields are set
    if (isset($_POST['doctorName'], $_POST['doctorStatus'], $_POST['services'], $_POST['password'])) {
        try {
            // Start transaction
            $conn->begin_transaction();

            $doctorName = $_POST['doctorName'];
            $doctorStatus = $_POST['doctorStatus'];
            $services = $_POST['services'];
            $password = $_POST['password'];
            $doctorID = generateDoctorID();
            $accessLevel = 1;

            // Insert into tbl_doctors
            $stmt_doctors = $conn->prepare("INSERT INTO tbl_doctors (Doctor_ID, Doctor_Name, Status) VALUES (?, ?, ?)");
            if (!$stmt_doctors) {
                throw new Exception("Prepare failed for doctors table: " . $conn->error);
            }
            $stmt_doctors->bind_param("sss", $doctorID, $doctorName, $doctorStatus);
            if (!$stmt_doctors->execute()) {
                throw new Exception("Execute failed for doctors table: " . $stmt_doctors->error);
            }
            $stmt_doctors->close();

            // Insert into tbl_admin
            $stmt_admin = $conn->prepare("INSERT INTO tbl_admin (Username, Name, Status, Password, Access_Level) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt_admin) {
                throw new Exception("Prepare failed for admin table: " . $conn->error);
            }
            $stmt_admin->bind_param("ssssi", $doctorID, $doctorName, $doctorStatus, $password, $accessLevel);
            if (!$stmt_admin->execute()) {
                throw new Exception("Execute failed for admin table: " . $stmt_admin->error);
            }
            $stmt_admin->close();

            // Insert doctor services into tbl_doctor_services
            $stmt_services = $conn->prepare("INSERT INTO tbl_doctor_services (Doctor_ID, Service_ID) VALUES (?, ?)");
            if (!$stmt_services) {
                throw new Exception("Prepare failed for doctor services table: " . $conn->error);
            }

            foreach ($services as $service) {
                $stmt_services->bind_param("ss", $doctorID, $service);
                if (!$stmt_services->execute()) {
                    throw new Exception("Execute failed for doctor services table: " . $stmt_services->error);
                }
            }
            $stmt_services->close();

            // Commit transaction
            $conn->commit();
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Doctor added successfully.'
            ]);

        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            
            echo json_encode([
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ]);
        } finally {
            $conn->close();
        }

    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required fields.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method.'
    ]);
}