<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION['valid'] != true) {
    header("Location: ../Patient_Login/index.php");
    exit;
}
require __DIR__ . '/connection.php';

// Fetch all time slots from tbl_working_hours
$query = "SELECT Working_Hours FROM tbl_working_hours";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    // Generate buttons for each time slot
    while ($row = $result->fetch_assoc()) {
        $timeSlot = $row['Working_Hours'];
        $output = preg_replace('/[:\s]+/', '', $timeSlot); // Clean string
        echo '<button class="btn btn-success fs-3 m-3 open-modal-btn" id="openModalBtn' . htmlspecialchars($output) . '">' . htmlspecialchars($timeSlot) . '</button>';
    }
} else {
    echo 'No time slots available.';
}

// Close the database connection
$conn->close();
?>