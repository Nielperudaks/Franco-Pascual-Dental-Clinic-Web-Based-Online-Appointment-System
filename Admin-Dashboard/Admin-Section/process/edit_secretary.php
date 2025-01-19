<?php
session_start();
define('ACCESS_GRANTED', true);
include('../../connection.php');


$response = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $adminId = $_POST['admin_id'];
    $firstName = $_POST['first_name'];

    $username = $_POST['username'];
    $password = $_POST['Password'];

    $query = "UPDATE tbl_admin SET Name = ?, Password = ?, Username = ? WHERE Admin_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $firstName, $password, $username, $adminId);

    if ($stmt->execute()) {
        $response['status'] = 'success';
        $response['message'] = 'User information updated successfully!';
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Error updating user information: ' . $conn->error;
    }

    echo json_encode($response);
}
