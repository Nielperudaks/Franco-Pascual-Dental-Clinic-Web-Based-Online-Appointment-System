<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: Login-Registration/");
    exit();
}

$conn = require __DIR__ . "/../connection.php"; // Note the corrected path

// Use prepared statement
$query = "SELECT * FROM tbl_admin WHERE Admin_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION["userID"]); // "i" for integer
$stmt->execute();
$result = $stmt->get_result();
$validateUser = $result->fetch_assoc();

if (!$validateUser || ($validateUser['Access_Level'] != 1 && $validateUser['Access_Level'] != 2)) {
    header("Location: Login-Registration/");
    exit();
}
date_default_timezone_set('Asia/Manila');
$date =  date('Y-m-d'); // Output the server's current date

$query = "
    SELECT 
        Service as ServiceName, 
        COUNT(Client_ID) as ClientCount 
    FROM tbl_transaction 
    WHERE Status = 'Done'
   
    GROUP BY Service 
    ORDER BY ClientCount DESC
";

try {
    $result = $conn->query($query);
    
    if ($result === false) {
        throw new Exception("Query failed: " . $conn->error);
    }
    
    $serviceCounts = [];
    while ($row = $result->fetch_assoc()) {
        $serviceCounts[] = [
            'ServiceName' => $row['ServiceName'],
            'ClientCount' => (int)$row['ClientCount'] // Convert to integer for proper JSON encoding
        ];
    }
    
    $result->free();
    
    header('Content-Type: application/json');
    echo json_encode($serviceCounts);
    
} catch (Exception $e) {
    header('HTTP/1.1 500 Internal Server Error');
    header('Content-Type: application/json');
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    $conn->close();
}