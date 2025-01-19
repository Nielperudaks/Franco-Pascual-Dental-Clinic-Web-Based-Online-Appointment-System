<?php
session_start();
define('ACCESS_GRANTED', true);
include('../../connection.php');

$response = array();

if (isset($_POST['username'])) {
    $username = $_POST['username'];
    
    $query = "SELECT COUNT(*) as count FROM tbl_admin WHERE Username = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        $response['exists'] = ($row['count'] > 0);
        
    } else {
        $response['exists'] = false;
        $response['error'] = true;
    }
} else {
    $response['exists'] = false;
    $response['error'] = true;
}

echo json_encode($response);
?>