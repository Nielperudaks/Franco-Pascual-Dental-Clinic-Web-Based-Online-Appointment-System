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