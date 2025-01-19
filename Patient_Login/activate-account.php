<?php
// activate-account.php - Handle account activation
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    if (!isset($_GET["token"])) {
        header("Location: index.php?error=invalid_token");
        exit();
    }

    $token = $_GET["token"];
    $hashedToken = hash("sha256", $token);
    
    $conn = require __DIR__ . "../../connection.php";

    // First, get the pending registration
    $query = "SELECT * FROM tbl_pending_registrations 
              WHERE AccountActivationHash = ? 
              AND CreatedAt > DATE_SUB(NOW(), INTERVAL 24 HOUR)";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $hashedToken);
    $stmt->execute();
    $result = $stmt->get_result();
    $pendingUser = $result->fetch_assoc();

    if ($pendingUser === null) {
        header("Location: index.php?error=invalid_or_expired");
        exit();
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert into main clients table
        $query = "INSERT INTO tbl_clients 
                  (FirstName, LastName, Email, Password) 
                  VALUES (?, ?, ?, ?)";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "ssss",
            $pendingUser["FirstName"],
            $pendingUser["LastName"],
            $pendingUser["Email"],
            $pendingUser["Password"]
        );
        
        $stmt->execute();

        // Delete from pending registrations
        $query = "DELETE FROM tbl_pending_registrations WHERE AccountActivationHash = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $hashedToken);
        $stmt->execute();

        // Commit transaction
        $conn->commit();
        
        header("Location: index.php?status=activation_success");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    header("Location: index.php?error=activation_failed");
    exit();
}
?>