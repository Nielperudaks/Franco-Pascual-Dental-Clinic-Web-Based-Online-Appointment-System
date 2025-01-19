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

if ((!$validateUser || $validateUser['Access_Level'] != 2) || (!$validateUser || $validateUser['Access_Level'] != 1)) {
    header("Location: Login-Registration/");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['transaction_code'])) {
    $transaction_code = $_POST['transaction_code'];

    // Prepare and execute the SQL query
    $query = $conn->prepare("SELECT AppointmentDate FROM tbl_transaction WHERE Transaction_Code = ?");
    $query->bind_param('s', $transaction_code);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo $row['AppointmentDate'];
    } else {
        echo 'No appointment found for the given transaction code.';
    }

    // Close the statement and connection
    $query->close();
    $conn->close();
} else {
    echo 'Invalid request.';
}
?>