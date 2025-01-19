Update Tutorial PHP Script

<?php
session_start();
$conn = require __DIR__ . "../../connection.php";
if(isset($_POST['updateTutorial']) && isset($_SESSION['userID'])) {
    $userID = $_SESSION['userID'];
    $updateQuery = "UPDATE tbl_clients SET Tutorial = 1 WHERE Client_ID = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $userID);
    
    if($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    $stmt->close();
    $conn->close();
}
?>