<?php
session_start();
define('ACCESS_GRANTED', true);
include('../../connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize response array
    $response = array();
    
    // Validate inputs
    if (empty($_POST['firstName']) || empty($_POST['username']) || empty($_POST['password'])) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit;
    }

    $firstName = trim($_POST['firstName']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $accessLevel = 2;
    $status = 'Active'; // Add default status

    try {
        // Prepare the statement
        $stmt = $conn->prepare("INSERT INTO tbl_admin (Name, Username, Password, Access_Level, Status) VALUES (?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        // Bind parameters - Note the correct number of parameters
        $stmt->bind_param("sssis", $firstName, $username, $password, $accessLevel, $status);
        
        // Execute the statement
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Secretary added successfully."]);
        } else {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Error adding secretary: " . $e->getMessage()]);
    } finally {
        // Close statement and connection
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($conn)) {
            $conn->close();
        }
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?>