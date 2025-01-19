<?php
session_start();
define('ACCESS_GRANTED', true);
include('../../connection.php');

if (isset($_POST['admin_id'])) {
    $admin_id = $_POST['admin_id'];
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

    $query = "DELETE FROM tbl_admin WHERE Admin_ID = ? AND Access_Level = 2";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $admin_id);
        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
    } else {
        echo 'error';
    }
}