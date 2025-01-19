<?php
require_once('connection.php');

// Query to get total clients
$totalClientsQuery = "SELECT COUNT(*) as total_clients FROM tbl_clients";
$totalClientsResult = $conn->query($totalClientsQuery);
$totalClients = 0;

if ($totalClientsResult && $totalClientsResult->num_rows > 0) {
    $row = $totalClientsResult->fetch_assoc();
    $totalClients = $row['total_clients'];
}

// Query to get active clients with appointments
$activeClientsQuery = "SELECT COUNT(*) as active_clients FROM tbl_clients WHERE Status IS NOT NULL";
$activeClientsResult = $conn->query($activeClientsQuery);
$activeClients = 0;

if ($activeClientsResult && $activeClientsResult->num_rows > 0) {
    $row = $activeClientsResult->fetch_assoc();
    $activeClients = $row['active_clients'];
}

$conn->close();

// Return the data as JSON
echo json_encode([
    'total_clients' => $totalClients,
    'active_clients' => $activeClients
]);
?>
