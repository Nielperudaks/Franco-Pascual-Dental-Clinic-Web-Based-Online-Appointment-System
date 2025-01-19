<?php
session_start();
if (!isset($_SESSION["userID"]) || $_SESSION['valid'] != true) {
    header("Location: ../Patient_Login/index.php");
    exit;
}
$conn = require __DIR__."/connection.php";

if (isset($_POST['transactionCode'])) {
    $transactionCode = $_POST['transactionCode'];

     // Fetch the Client_ID where Transaction_Code matches
     $query = "SELECT Client_ID FROM tbl_transaction WHERE Transaction_Code = ?";
     $stmt = $conn->prepare($query);
     $stmt->bind_param("s", $transactionCode);
     $stmt->execute();
     $result = $stmt->get_result();
     $clientData = $result->fetch_assoc();
     $clientId = $clientData['Client_ID'];

     if ($clientData) {
         

         // Update the Status in tbl_clients where Client_ID matches
         $query = "UPDATE tbl_clients SET Status = NULL WHERE Client_ID = ?";
         $stmt = $conn->prepare($query);
         $stmt->bind_param("s", $clientId);
         $stmt->execute();
     }

    // Delete the transaction
    $query = "DELETE FROM tbl_transaction WHERE Transaction_Code = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $transactionCode);

    if ($stmt->execute()) {
        $query = "SELECT Email, LastName FROM tbl_clients WHERE Client_ID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $clientId);
            $stmt->execute();
            $result = $stmt->get_result();
            $validateUser = $result->fetch_assoc();
            $LastName = $validateUser['LastName'];

            if(isset($validateUser['Email'])){
                try{ require __DIR__ . "../../vendor/autoload.php";
                $resend = Resend::client('re_4je3SZHw_9HE6pmCGJYnMwMd87nVKSBnF');

                $resend->emails->send([
                'from' => 'Acme <FrancoPacual@fpdentalclinic.com>',
                'to' => [$validateUser['Email']],
                'subject' => 'Appointment Cancellation',
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
                                    <h2 style="color: #333333; margin: 0;">Appointment Request</h2>
                                    <p style="color: #333333; font-size: 16px;">
                                        Dear $LastName,
                                    </p>
                                    <p style="color: #333333; font-size: 20px;">
                                        Your appointment has been <strong>Cancelled</strong>, feel free to appoint again if you have any concerns.
                                    </p>
                                    
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

                echo "Cancellation Success";
            }
       

       
    } else {
        echo "Error: Failed to delete the transaction.";
    }
} else {
    echo "Error: Transaction code is required.";
}

$stmt->close();
$conn->close();
?>