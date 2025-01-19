<?php
session_start();
if (!isset($_SESSION["userID"])) {
    header("Location: ../Login-Registration/");
    exit();
}

$conn = require __DIR__ . "../../connection.php";
    $query = "SELECT * FROM tbl_admin WHERE Admin_ID = {$_SESSION["userID"]}";
    $result = $conn->query($query);
    $validateUser = $result->fetch_assoc();

    if (!$validateUser || $validateUser['Access_Level'] != 2) {
        header("Location: ../Login-Registration/");
        exit();
    }
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $duration = (int)$_POST['duration'];
    //echo $start_time + $end_time + $duration;

    if (empty($start_time) || empty($end_time) || $duration <   60) {
        echo 'Invalid input: Please ensure all fields are filled and duration is greater than or equal 60.';
        exit;
    }

    $start = DateTime::createFromFormat('H:i', $start_time);
    $end = DateTime::createFromFormat('H:i', $end_time);

    if ($start >= $end) {
        echo 'Invalid input: Start time must be earlier than end time.';
        exit;
    }
    $interval = $start->diff($end);
    $diffInMinutes = $interval->h * 60 + $interval->i;

    if ($diffInMinutes < $duration) {
        echo 'Invalid input: The end time must be greater than the start time by at least the given duration.';
        exit;
    }

    $Appointed = 'Approved';
    $Waiting = 'Waiting Approval';

    $checkQuery = "SELECT COUNT(*) as count FROM tbl_transaction WHERE (Status= ? OR Status = ?)";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("ss", $Appointed, $Waiting);
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        echo "The system cannot change the schedule, there are still active appointments.";
        $conn->close();
        exit;
    }

    $time_slots = [];
    $current = clone $start;

    while ($current < $end) {
        $next = clone $current;
        $next->modify("+{$duration} minutes");

        if ($next > $end) {
            break;
        }

        $time_slots[] = $current->format('H:i') . ' - ' . $next->format('H:i');
        $current = $next;
    }

    if (empty($time_slots)) {
        echo 'No valid time slots available with the given duration.';
        exit;
    }


    if ($conn->connect_error) {
        die('Connection failed: ' . $conn->connect_error);
    }
    $clear_table = "DELETE FROM tbl_working_hours";
    if (!$conn->query($clear_table)) {
        echo 'Error clearing the table: ' . $conn->error;
        $conn->close();
        exit;
    }

    foreach ($time_slots as $slot) {
        $stmt = $conn->prepare("INSERT INTO tbl_working_hours (Working_Hours) VALUES (?)");
        $stmt->bind_param("s", $slot);
        $stmt->execute();
    }

    $stmt->close();
    $conn->close();

    echo 'Time slots generated successfully.';
}