<?php
    $conn = require __DIR__."/connection.php";
    if(isset($_POST['doctorID'])) {
        $ID =  $_POST['doctorID'];
        $Status = 'Inactive';
        $query = "UPDATE tbl_doctors SET Status = ? WHERE Doctor_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "ss",
            $Status, 
            $ID          
        );
        if($stmt->execute()){     
            echo "Doctor deactivation success";
            }               
    } else {
       

        echo "Error: update failed.";
    }
             $stmt->close();    
        
            $conn->close();
?>