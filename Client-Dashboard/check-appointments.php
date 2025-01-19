<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION['valid'] != true) {
    header("Location: ../Patient_Login/index.php");
    exit;
}

// Check if date and doctor are provided
$conn = require __DIR__ . "../../connection.php";

if (isset($_POST['date']) && isset($_POST['doctor'])) {
    $date = $_POST['date'];
    $doctor = $_POST['doctor'];
    $Status = "Waiting Approval";
    $Status2 = "Approved";

    $query = "SELECT AppointmentTime FROM tbl_transaction WHERE AppointmentDate = ? AND Doctor = ? AND (Status=? OR Status=?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $date, $doctor, $Status, $Status2);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        $appointments = array();
        while ($row = $result->fetch_assoc()) {
            $appointments[] = $row['AppointmentTime']; // Only fetch the AppointmentTime
        }

        $stmt->close();
        $conn->close();

        echo json_encode($appointments);
    } else {
        // Handle SQL execution error
        echo json_encode(array()); // Return empty array as response
    }
}