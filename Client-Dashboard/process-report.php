<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION['valid'] != true) {
    header("Location: ../Patient_Login/index.php");
    exit;
}

$conn = require __DIR__ . "../../connection.php";

$response = array();

if (isset($_POST['code'])) {
    $code = $_POST['code'];
    $query = "SELECT Report FROM tbl_transaction WHERE Transaction_Code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $code);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $validateUser = $result->fetch_assoc();
        if ($validateUser) {
            if($validateUser['Report']===null || $validateUser['Report']===''){
                $response['report'] = 'No report given yet';
            }else{
                $response['report'] = $validateUser['Report'];
            }
            
        } else {
            $response['error'] = 'No report found for the given code.';
        }
    } else {
        $response['error'] = 'Query execution failed.';
    }
} else {
    $response['error'] = 'Code not provided.';
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($response);