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


if (isset($_POST['transactionCode']) && isset($_POST['reason'])) {
    
    $transactionCode = $_POST['transactionCode'];
    $reason = $_POST['reason'];
    $status = "Cancelled";
    
    // Update transaction status
    $query = "UPDATE tbl_transaction SET Status = ? WHERE Transaction_Code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $status, $transactionCode);
    if ($stmt->execute()) {
        // Reset client status
        $query = "UPDATE tbl_clients SET Status = NULL WHERE Client_ID = (SELECT Client_ID FROM tbl_transaction WHERE Transaction_Code = ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $transactionCode);
        $stmt->execute();
        
        // Get client ID from transaction
        $query = "SELECT Client_ID FROM tbl_transaction WHERE Transaction_Code = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $transactionCode);
        $stmt->execute();
        $result = $stmt->get_result();
        $validateUser = $result->fetch_assoc();
        $ID = $validateUser['Client_ID'];
        
        // Get client email
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
                                    <h2 style="color: #333333; margin: 0;">Appointment Cancellation</h2>
                                    <p style="color: #333333; font-size: 16px;">
                                       We would like to inform you that your appointment has been cancelled. Below are the details of the cancellation:
                                    </p>
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0;">
                                        <tr>
                                            <td width="30%" style="padding: 10px 0; color: #333333; font-weight: bold;">Report:</td>
                                            <td width="70%" style="padding: 10px 0; color: #333333;">$reason</td>
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
        }

        echo "Cancellation Successful";
    } else {
        die('Update unsuccessful');
    }
} else {
    die('Update unsuccessful');
}

$stmt->close();
$conn->close();
?>