<?php
// Include your database connection logic
$conn = require __DIR__ . "../../../connection.php";

// Prepare SQL statement to fetch services
if (isset($_POST['serviceID'])) {
    $ID = $_POST['serviceID'];
    $query = "SELECT * FROM tbl_services WHERE Service_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "i", 
        $ID,
       
    );
    if($stmt->execute()){
        $result=$stmt->get_result();
        $validateUser=$result->fetch_assoc();
        $conn->close();
    
       
        echo json_encode($validateUser);
    }
   
} else {
    $query = "SELECT * FROM tbl_services";
    $result = $conn->query($query);

    // Array to store services
    $services = array();

    // Fetch services and add them to the array
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }

    // Close connection
    $conn->close();

    // Return services as JSON 
    echo json_encode(['services' => $services]);
}