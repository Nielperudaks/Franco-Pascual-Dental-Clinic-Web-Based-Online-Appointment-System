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


if (isset($_POST['date']) && isset($_POST['doctor'])) {
    $date = $_POST['date'];
    $doctor = $_POST['doctor'];

    // Query to fetch all appointments for the given doctor on the selected date
    $query = "SELECT t.AppointmentTime, s.Duration 
              FROM tbl_transaction t
              JOIN tbl_services s ON t.Service_ID = s.Service_ID
              WHERE t.AppointmentDate = ? 
              AND t.Doctor = ? 
              AND (t.Status = 'Waiting Approval' OR t.Status = 'Approved')";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $date, $doctor);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $appointments = array();

        // Fetch the result and include both AppointmentTime and Duration
        while ($row = $result->fetch_assoc()) {
            $appointments[] = array(
                "time" => $row['AppointmentTime'],
                "duration" => $row['Duration'] // Include the duration from tbl_services
            );
        }

        echo json_encode($appointments); // Return the appointments with duration
    } else {
        echo json_encode(array()); // Return empty array in case of failure
    }

    $stmt->close();
    $conn->close();
}
?>