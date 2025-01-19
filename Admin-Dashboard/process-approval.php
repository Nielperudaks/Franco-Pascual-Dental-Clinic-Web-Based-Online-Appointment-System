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



if (isset($_POST['code'])) {
    $code = $_POST['code'];
    $status = 'Approved';
    $query = "UPDATE tbl_transaction SET Status=? WHERE Transaction_Code=?";
    $stmt = $conn->Prepare($query);
    $stmt->bind_param(
        "ss",
        $status,
        $code
    );
    if ($stmt->execute()) {
        $query = "SELECT * FROM tbl_transaction WHERE Transaction_Code = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        $validateUser = $result->fetch_assoc();
        $ID = $validateUser['Client_ID'];
        $date = $validateUser['AppointmentDate'];
        $time = $validateUser['AppointmentTime'];
        $service = $validateUser['Service'];
        $doctor = $validateUser['Doctor'];
        $LastName = $validateUser['LastName'];

        $query = "SELECT Email FROM tbl_clients WHERE Client_ID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $ID);
        $stmt->execute();
        $result = $stmt->get_result();
        $validateUser = $result->fetch_assoc();

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
                                    <h2 style="color: #333333; margin: 0;">Appointment Request Approved</h2>
                                    <p style="color: #333333; font-size: 16px;">
                                        Dear $LastName,
                                    </p>
                                    <p style="color: #333333; font-size: 16px;">
                                       We would like to inform you that your appointment request has been approved. Below are the details of your appointment:
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
                
                echo "Appointment has been successfully Approved";
            } catch (Exception $e) {
                echo "Message could not be sent, Mail Error: ";
                exit;
            }
        } else {
            die('email not acquired');
        }
    } else {
        die('update unsuccessful');
    }
} else {
    die('error to post method');
}