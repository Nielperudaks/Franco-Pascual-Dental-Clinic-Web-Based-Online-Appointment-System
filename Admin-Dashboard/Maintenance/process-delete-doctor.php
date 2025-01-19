<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: ../Login-Registration/");
    exit();
}

$conn = require __DIR__ . "../../connection.php";

// Validate session and user access
$query = "SELECT * FROM tbl_admin WHERE Admin_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION["userID"]);
$stmt->execute();
$result = $stmt->get_result();
$validateUser = $result->fetch_assoc();
$stmt->close();

if (!$validateUser || $validateUser['Access_Level'] != 2) {
    header("Location: ../Login-Registration/");
    exit();
}

if (isset($_POST['id'])) {
    $ID = $_POST['id'];

    // Check if the doctor has appointments with status 'Approved' or 'Waiting Approval'
    $checkQuery = "SELECT COUNT(*) as count FROM tbl_transaction WHERE Doctor_ID = ? AND (Status = 'Approved' OR Status = 'Waiting Approval')";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("s", $ID);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        echo "The doctor has appointments and cannot be deleted.";
        $conn->close();
        exit;
    }

    // Proceed with deletion if no appointments are found
    $deleteDoctorQuery = "DELETE FROM tbl_doctors WHERE Doctor_ID = ?";
    $deleteDoctorStmt = $conn->prepare($deleteDoctorQuery);
    $deleteDoctorStmt->bind_param("s", $ID);
    $doctorDeleted = $deleteDoctorStmt->execute();
    $deleteDoctorStmt->close();

    if ($doctorDeleted) {
        // Delete associated row in tbl_admin where Username matches Doctor_ID
        $deleteAdminQuery = "DELETE FROM tbl_admin WHERE Username = ?";
        $deleteAdminStmt = $conn->prepare($deleteAdminQuery);
        $deleteAdminStmt->bind_param("s", $ID);
        $adminDeleted = $deleteAdminStmt->execute();
        $deleteAdminStmt->close();

        if ($adminDeleted) {
            echo "Doctor and associated admin account deletion successful.";
        } else {
            echo "Doctor deleted, but failed to delete associated admin account.";
        }
    } else {
        echo "Error: Doctor deletion failed.";
    }
} else {
    echo "Error: Missing Doctor ID.";
}

$conn->close();
?>