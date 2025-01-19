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


if (isset($_SESSION["userID"])) {
    $query = "SELECT * FROM tbl_clients WHERE Client_ID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_POST['clientID']);
    $stmt->execute();
    $result = $stmt->get_result();
    $validateUser = $result->fetch_assoc();

    if (isset($validateUser)) {
        // Get user details
        $FirstName = $validateUser["FirstName"];
        $LastName = $validateUser["LastName"];
        $ID = $_POST['clientID'];
        $code =  $_POST["code"];
        $date = $_POST["date"];
        $status = "Approved";
        $time = $_POST["time"];
        $service = $_POST["service"];
        $doctor =  $_POST["doctor"];
        $doctorID = $_POST["doctorID"];
        $serviceID = $_POST["serviceID"];
        $targetTooth = $_POST["targetTooth"];
        $isPriority = $_POST["priority"];

        // Prepare INSERT statement
        $query = "INSERT INTO tbl_transaction(Transaction_Code, FirstName, LastName, AppointmentDate, AppointmentTime, Service, Doctor, Status, Client_ID, Doctor_ID, Service_ID, Teeth, IsPriority)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        // Bind parameters
        $stmt->bind_param("ssssssssisiss", $code, $FirstName, $LastName, $date, $time, $service,  $doctor, $status, $ID, $doctorID, $serviceID,$targetTooth, $isPriority );

        // Execute statement
        if (!$stmt->execute()) {
            die("Error: " . $stmt->error);
        } else {
            echo "Data inserted successfully!";
            $query = "UPDATE tbl_clients SET Status = ? WHERE Client_ID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("si", $status, $ID);
            $stmt->execute();




            if (isset($validateUser['Email'])) {
                try {
                    require __DIR__ . "../../vendor/autoload.php";
                    $resend = Resend::client('re_4je3SZHw_9HE6pmCGJYnMwMd87nVKSBnF');

                    $resend->emails->send([
                    'from' => 'Acme <FrancoPacual@fpdentalclinic.com>',
                    'to' => [$validateUser['Email']],
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
                                    <h2 style="color: #333333; margin: 0;">New Appointment</h2>
                                    <p style="color: #333333; font-size: 16px;">
                                        Dear $LastName,
                                    </p>
                                    <p style="color: #333333; font-size: 16px;">
                                       You've got a new appointment at Franco Pascual Dental Clinic. Below are the details of your appointment:
                                    </p>
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0;">
                                        <tr>
                                            <td width="30%" style="padding: 10px 0; color: #333333; font-weight: bold;">Date:</td>
                                            <td width="70%" style="padding: 10px 0; color: #333333;">$date</td>
                                        </tr>
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
                                        If you have any questions or need to reschedule, please email us at <a href="mailto:FrancoPacual@fpdentalclinic.com" style="color: #4CAF50; text-decoration: none;">FrancoPacual@fpdentalclinic.com</a>.
                                    </p>
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

                } catch (Exception $e) {
                    echo "Message could not be sent, Mail Error: ";
                    exit;
                }
            } else {
                echo 'email has not been set';
            }
        }
    } else {
        echo 'Client has not been set' . $_POST['clientID'];
    }
}

// Close statement and connection
$stmt->close();
$conn->close();