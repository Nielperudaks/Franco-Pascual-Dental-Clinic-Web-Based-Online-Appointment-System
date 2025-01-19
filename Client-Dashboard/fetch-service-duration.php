<?php
$conn = require __DIR__ . "/../connection.php";

if (isset($_POST['service_id'])) {
    $service_id = $_POST['service_id'];

    $query = "SELECT duration FROM tbl_services WHERE Service_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $service_id);
    $stmt->execute();

    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        echo json_encode(array("duration" => $row['duration']));
    } else {
        echo json_encode(array("error" => "Service not found"));
    }

    $stmt->close();
    $conn->close();
}
?>
