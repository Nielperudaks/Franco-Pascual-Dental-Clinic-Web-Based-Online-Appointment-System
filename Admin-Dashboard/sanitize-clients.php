<?php
try {
    // Establish database connection
    $conn = require __DIR__ . "../../connection.php";

    // Start a transaction
    $conn->begin_transaction();

    // Reset ResetTokenHash and ResetTokenExpiration in tbl_clients
    $resetQuery = "UPDATE tbl_clients SET ResetTokenHash = NULL, ResetTokenExpiration = NULL";
    $resetStmt = $conn->prepare($resetQuery);

    if (!$resetStmt->execute()) {
        throw new Exception("Failed to reset ResetTokenHash and ResetTokenExpiration in tbl_clients.");
    }
    $resetStmt->close();

    // Remove all rows from tbl_pending_registrations
    $deleteQuery = "DELETE FROM tbl_pending_registrations";
    $deleteStmt = $conn->prepare($deleteQuery);

    if (!$deleteStmt->execute()) {
        throw new Exception("Failed to delete rows from tbl_pending_registrations.");
    }
    $deleteStmt->close();

    // Commit the transaction
    $conn->commit();

    echo "Reset and deletion operations completed successfully.";
} catch (Exception $e) {
    // Rollback the transaction in case of an error
    $conn->rollback();
    error_log($e->getMessage());
    echo "An error occurred: " . $e->getMessage();
} finally {
    $conn->close();
}
?>
