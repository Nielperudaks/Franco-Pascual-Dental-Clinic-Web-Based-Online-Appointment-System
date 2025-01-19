<?php
session_start();
define('ACCESS_GRANTED', true);
include('../../connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adminId = $_POST['admin_id'];

    $query = "SELECT * FROM tbl_admin WHERE Admin_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $adminId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $userData = $result->fetch_assoc();
        echo json_encode($userData);
    } else {
        echo json_encode(["error" => "No user found."]);
    }
} else {
    echo json_encode(["error" => "Invalid request method."]);
}
