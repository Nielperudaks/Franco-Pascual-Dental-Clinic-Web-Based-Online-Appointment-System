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

$code =  $_POST["code"];
$status = "Approved";
$time = $_POST["time"];
$service = $_POST["service"];
$doctor =  $_POST["doctor"];
$doctorID = $_POST["doctorID"];
$serviceID = $_POST["serviceID"];



// Prepare INSERT statement
$query = "UPDATE tbl_transaction SET AppointmentTime = ?, Service = ?, Doctor = ?, Status = ?, Doctor_ID = ?, Service_ID = ? WHERE Transaction_Code = ?";

$stmt = $conn->prepare($query);

// Bind parameters
$stmt->bind_param("sssssis",  $time, $service,  $doctor, $status, $doctorID, $serviceID, $code);

// Execute statement
if (!$stmt->execute()) {
    die("Error: " . $stmt->error);
} else {
   


    // Prepare the SQL statement to fetch Client_ID from tbl_transactions
    $stmt1 = $conn->prepare("SELECT Client_ID FROM tbl_transaction WHERE Transaction_Code = ?");
    $stmt1->bind_param("s", $code);
    $stmt1->execute();
    $result1 = $stmt1->get_result();

    if ($result1->num_rows > 0) {
        $row1 = $result1->fetch_assoc();
        $client_id = $row1['Client_ID'];

        $query = "UPDATE tbl_clients SET Status = ? WHERE Client_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $status, $client_id);
        $stmt->execute();

        $stmt2 = $conn->prepare("SELECT Email FROM tbl_clients WHERE Client_ID = ?");
        $stmt2->bind_param("i", $client_id);
        $stmt2->execute();
        $result2 = $stmt2->get_result();


        $row2 = $result2->fetch_assoc();

        $stmt2->close();
    } else {

        echo json_encode(array('error' => 'No client found for the given transaction code.'));
    }

    if (isset($row2['Email'])) {
        try {
            require __DIR__ . "../../vendor/autoload.php";
            $resend = Resend::client('re_4je3SZHw_9HE6pmCGJYnMwMd87nVKSBnF');

            $resend->emails->send([
            'from' => 'Acme <FrancoPacual@fpdentalclinic.com>',
            'to' => [$row2['Email']],
            'subject' => 'Appointment',
            'html' => <<<END
    
                    <body style="font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 0;">
                        <table align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; background-color: #ffffff; margin-top: 50px;">
                            <tr>
                                <td align="center" bgcolor="#4CAF50" style="padding: 20px 0;">
                                    <h1 style="color: #ffffff; margin: 0;">Franco Pascual Dental Clinic</h1>
                                </td>
                            </tr>
                            <tr>
                                <td align="left" style="padding: 20px;">
                                    <h2 style="color: #333333; margin: 0;">Appointment Changed</h2>
                                    <p style="color: #333333; font-size: 16px;">
                                        We would like to inform you that your appointment at Franco Pascual Dental Clinic has been changed. Below are the details of your appointment:
                                    </p>
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0;">
                                    
                                        <tr>
                                            <td width="30%" style="padding: 10px 0; color: #333333; font-weight: bold;">Time:</td>
                                            <td width="70%" style="padding: 10px 0; color: #333333;">$time</td>
                                        </tr>
                                        <tr>
                                            <td width="30%" style="padding: 10px 0; color: #333333; font-weight: bold;">Doctor:</td>
                                            <td width="70%" style="padding: 10px 0; color: #333333;">$doctor</td>
                                        </tr>
                                        <tr>
                                            <td width="30%" style="padding: 10px 0; color: #333333; font-weight: bold;">Service:</td>
                                            <td width="70%" style="padding: 10px 0; color: #333333;">$service</td>
                                        </tr>
                                        <tr>
                                            <td width="30%" style="padding: 10px 0; color: #333333; font-weight: bold;">Code:</td>
                                            <td width="70%" style="padding: 10px 0; color: #333333;">$code</td>
                                        </tr>
                                    </table>
                                   
                                    <p style="color: #333333; font-size: 16px;">
                                        We look forward to seeing you soon.
                                    </p>
                                    <p style="color: #333333; font-size: 16px;">
                                        Best regards,
                                        <br>
                                        Franco Pascual Dental Clinic Team
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" bgcolor="#4CAF50" style="padding: 10px;">
                                    <p style="color: #ffffff; font-size: 14px; margin: 0;">&copy; 2024 Franco Pascual Dental Clinic. All rights reserved.</p>
                                </td>
                            </tr>
                        </table>
                    </body>               
                    END,
                    ]);
            
            echo "Appointment updated successfully!, An Email has been sent";
        } catch (Exception $e) {
            echo "Message could not be sent, Mail Error: ";
            exit;
        }
    } else {
        echo 'email has not been set';
    }
}


// Close statement and connection
$stmt->close();
$conn->close();