<?php
session_start();
define('ACCESS_GRANTED', true);
include('../../connection.php');

if (isset($_POST['admin_id'])) {
    $admin_id = $_POST['admin_id'];
    $status = $_POST['status'];
    
    // First check if trying to deactivate
    if ($status == 'Inactive') {
        // Check active records count
        $check_query = "SELECT COUNT(*) as count FROM tbl_admin WHERE Status = 'Active' AND Access_Level = 2";
        if ($stmt = $conn->prepare($check_query)) {
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            // If only one active record exists
            if ($row['count'] == 1) {
                echo 'Invalid';
                exit;
            }
        }
    }
    
    // If check passes or status is being set to Active, proceed with update
    $query = "UPDATE tbl_admin SET Status = ? WHERE Admin_ID = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("si", $status, $admin_id);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'error';
    }
}
?>