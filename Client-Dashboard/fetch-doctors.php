<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION['valid'] != true) {
    header("Location: ../Patient_Login/index.php");
    exit;
}
require_once('connection.php');


if(isset($_POST['serviceID'])) {
    
    $serviceID = mysqli_real_escape_string($conn, $_POST['serviceID']);
    
    $query = "SELECT DISTINCT ds.Doctor_ID, d.Doctor_Name 
              FROM tbl_doctor_services ds 
              INNER JOIN tbl_doctors d ON ds.Doctor_ID = d.Doctor_ID 
              WHERE ds.Service_ID = '$serviceID' AND d.Status = 'Active'";
    

    $result = mysqli_query($conn, $query);
    
    if($result) {
        $doctors = array(); 
        

        while($row = mysqli_fetch_assoc($result)) {
     
            $doctors[] = $row;
        }

        mysqli_free_result($result);

        echo json_encode($doctors);
    } else {
     
        echo json_encode(array('error' => 'Failed to fetch doctors'));
    }
} else {
    echo json_encode(array('error' => 'Service ID is not provided'));
}
mysqli_close($conn);
?>