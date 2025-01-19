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


if (isset($_POST['code']) && isset($_POST['report'])) {
    $code = $_POST['code'];
    $report = $_POST['report'];

    // Handle file upload
    // $fileUploaded = false;
    // $filePath = '';
    // if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    //     $file = $_FILES['file'];
    //     $fileName = basename($file['name']);
    //     $fileTmpName = $file['tmp_name'];
    //     $fileSize = $file['size'];
    //     $fileType = $file['type'];

    //     // Define allowed file types and size limit
    //     $allowedTypes = [
    //         'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    //         'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    //         'application/vnd.ms-powerpoint', 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    //         'application/pdf', 'image/jpeg', 'image/png'
    //     ];
    //     $sizeLimit = 10 * 1024 * 1024; // 10 MB

    //     if (in_array($fileType, $allowedTypes) && $fileSize <= $sizeLimit) {
    //         $uploadDir = __DIR__ . '/uploads/';
    //         $filePath = $uploadDir . $fileName;
    //         if (move_uploaded_file($fileTmpName, $filePath)) {
    //             $fileUploaded = true;
    //         } else {
    //             die('There was an error uploading your file.');
    //         }
    //     } else {
    //         die('File type not allowed or file size exceeds the limit of 10 MB.');
    //     }
    // }

    $query = "UPDATE tbl_transaction SET Report=? WHERE Transaction_Code=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $report, $code);
    if ($stmt->execute()) {
        $query = "SELECT Client_ID FROM tbl_transaction WHERE Transaction_Code = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();
        $validateUser = $result->fetch_assoc();
        $ID = $validateUser['Client_ID'];

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
                                    <h2 style="color: #333333; margin: 0;">Appointment Report</h2>
                                    <p style="color: #333333; font-size: 16px;">
                                    We hope this message finds you well.
                                    On behalf of the entire team at Franco-Pascual Dental Clinic,
                                    We wanted to extend our heartfelt thanks
                                    for choosing our clinic for your dental care needs.
                                    We truly appreciate the trust you have placed in us,
                                    and it was our pleasure to assist you.
                                    
                                    Here's the report of your appointment:
                                    </p>
                                    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0;">
                                        <tr>
                                            <td width="30%" style="padding: 10px 0; color: #333333; font-weight: bold;">Report:</td>
                                            <td width="70%" style="padding: 10px 0; color: #333333;">$report</td>
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
               
                

               

                // if ($fileUploaded) {
                //     unlink($filePath); // Delete the file after sending the email
                // }
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: ";
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