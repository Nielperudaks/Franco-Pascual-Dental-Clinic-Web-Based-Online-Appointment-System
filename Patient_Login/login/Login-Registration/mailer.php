<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;
    
    require __DIR__ . "/vendor/autoload.php";
    $mail = new PHPMailer(true);
    //$mail->SMTPDebug= SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->SMTPAuth = true;
    $mail->Host = "smtp-relay.brevo.com";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->Username = "bulunggoy7@gmail.com";
    $mail->Password =  "UhR8asf71nGSrAtO";
    $mail->isHTML(true);
    return $mail;

?>