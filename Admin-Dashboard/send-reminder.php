
<?php
// Prevent timeout for long-running processes
set_time_limit(0);

// Add error logging
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/home/username/public_html/Admin-Dashboard/logs/cron_error.log');

// Start time logging
$start_time = microtime(true);
error_log("Cron job started at: " . date('Y-m-d H:i:s'));

try {
    // Connect to the database
    $conn = require __DIR__ . "../../connection.php";

    // Set the timezone to Manila for accurate time comparisons
    date_default_timezone_set('Asia/Manila');

    // Query to retrieve relevant transaction information for processing
    $query = "SELECT AppointmentTime, AppointmentDate, Transaction_Code, Status, EmailReminderSent FROM tbl_transaction 
              WHERE AppointmentDate >= CURDATE() 
              AND AppointmentDate <= DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
    $stmt = $conn->prepare($query);

    // Execute the query and log any errors
    if (!$stmt->execute()) {
        throw new Exception("Query execution failed: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $updateQueries = [];
    $emailsSent = 0;
    $statusUpdates = 0;

    while ($row = $result->fetch_assoc()) {
        // Extract start and end times
        if (preg_match('/(\d{1,2}:\d{2}) - (\d{1,2}:\d{2})/', $row['AppointmentTime'], $time)) {
            $startTime = ltrim($time[1], '0 -');
            $endTime = ltrim($time[2], '0 -');
        } else {
            error_log("Invalid time format for transaction: " . $row['Transaction_Code']);
            continue;
        }

        // Set up date and time objects
        $appointmentDate = $row['AppointmentDate'];
        $code = $row['Transaction_Code'];
        $startDateTime = new DateTime("$appointmentDate $startTime");
        
        // Calculate grace period (15 minutes after start)
        $graceDateTime = (clone $startDateTime)->modify('+15 minutes');
        $currentDateTime = new DateTime();

        // Status update logic
        if ($currentDateTime >= $graceDateTime) {
            $newStatus = ($row['Status'] === 'Approved') ? 'Done' : 'No Response';
            $updateQueries[] = ['transaction_status' => $newStatus, 'transaction_id' => $code];
            $statusUpdates++;
        }

        // Email reminder logic
        if ($row['EmailReminderSent'] == 0 && $row['Status'] === 'Approved') {
            // Get client email
            $clientStmt = $conn->prepare("SELECT c.Email, c.Client_ID 
                                        FROM tbl_transaction t 
                                        JOIN tbl_clients c ON t.Client_ID = c.Client_ID 
                                        WHERE t.Transaction_Code = ?");
            $clientStmt->bind_param("s", $code);
            
            if (!$clientStmt->execute()) {
                error_log("Failed to get client info for transaction: $code");
                continue;
            }
            
            $clientResult = $clientStmt->get_result();
            $clientData = $clientResult->fetch_assoc();
            //echo $clientData['Email'];
            if ($clientData && $clientData['Email']) {
                $notificationStartTime = (clone $startDateTime)->modify('-1 hour');
                $notificationEndTime = $startDateTime;

                if ($currentDateTime >= $notificationStartTime && $currentDateTime <= $notificationEndTime) {
                    try {
                        require __DIR__ . "../../vendor/autoload.php";
                        $resend = Resend::client('re_4je3SZHw_9HE6pmCGJYnMwMd87nVKSBnF');

                        $resend->emails->send([
                            'from' => 'Acme <FrancoPacual@fpdentalclinic.com>',
                            'to' => [$clientData['Email']],
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
                                                <h2 style="color: #333333; margin: 0;">Upcoming Appointment Reminder</h2>
                                                <p style="color: #333333; font-size: 16px;">
                                                    Dear Client,
                                                </p>
                                                <p style="color: #333333; font-size: 16px;">
                                                   This is a reminder that you have an appointment scheduled at Franco Pascual Dental Clinic.
                                                </p>
                                                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 20px 0;">
                                                    <tr>
                                                        <td width="30%" style="padding: 10px 0; color: #333333; font-weight: bold;">Date:</td>
                                                        <td width="70%" style="padding: 10px 0; color: #333333;">$appointmentDate</td>
                                                    </tr>
                                                    <tr>
                                                        <td width="30%" style="padding: 10px 0; color: #333333; font-weight: bold;">Time:</td>
                                                        <td width="70%" style="padding: 10px 0; color: #333333;">$startTime - $endTime</td>
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

                        // Update EmailReminderSent flag
                        $updateEmailStmt = $conn->prepare("UPDATE tbl_transaction SET EmailReminderSent = 1 WHERE Transaction_Code = ?");
                        $updateEmailStmt->bind_param("s", $code);
                        $updateEmailStmt->execute();
                        $emailsSent++;
                        
                        error_log("Reminder email sent successfully for transaction: $code");
                    } catch (Exception $e) {
                        error_log("Failed to send email for transaction $code: " . $e->getMessage());
                    }
                }
            }
        }
    }

    // Batch process status updates
    foreach ($updateQueries as $update) {
        $updateStmt = $conn->prepare("UPDATE tbl_transaction SET Status = ? WHERE Transaction_Code = ?");
        $updateStmt->bind_param("ss", $update['transaction_status'], $update['transaction_id']);
        if (!$updateStmt->execute()) {
            error_log("Failed to update status for transaction: " . $update['transaction_id']);
        }
    }

    // Check and update client statuses if needed
    $checkStmt = $conn->prepare("SELECT COUNT(*) AS count FROM tbl_transaction WHERE Status IN ('Approved', 'Waiting Approval')");
    if ($checkStmt->execute()) {
        $countResult = $checkStmt->get_result()->fetch_assoc();
        if ($countResult['count'] == 0) {
            $conn->prepare("UPDATE tbl_clients SET Status = NULL")->execute();
        }
    }

    // Log completion statistics
    $end_time = microtime(true);
    $execution_time = ($end_time - $start_time);
    error_log("Cron job completed at: " . date('Y-m-d H:i:s'));
    error_log("Execution time: " . number_format($execution_time, 2) . " seconds");
    error_log("Emails sent: $emailsSent");
    error_log("Status updates: $statusUpdates");

} catch (Exception $e) {
    error_log("Critical error in cron job: " . $e->getMessage());
} finally {
    // Close database connections
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>