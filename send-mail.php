<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

function isAjaxRequest()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function respondSuccess()
{
    if (isAjaxRequest()) {
        echo 'success';
        exit;
    }

    header('Location: thankyou.php');
    exit;
}

function respondError($statusLine)
{
    if (isAjaxRequest()) {
        header($statusLine);
        echo 'error';
        exit;
    }

    header('Location: single-page.php?status=error');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('HTTP/1.1 405 Method Not Allowed');
}

$userType        = isset($_POST['userType']) ? $_POST['userType'] : '';
$serviceInterest = isset($_POST['serviceInterest']) ? $_POST['serviceInterest'] : '';
$fullName        = isset($_POST['fullName']) ? $_POST['fullName'] : '';
$phoneNumber     = isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : '';
$emailAddress    = isset($_POST['emailAddress']) ? $_POST['emailAddress'] : '';
$contactMethod   = isset($_POST['contactMethod']) ? $_POST['contactMethod'] : '';
$contactTime     = isset($_POST['contactTime']) ? $_POST['contactTime'] : '';
$message         = isset($_POST['message']) ? $_POST['message'] : '';

$mail = new PHPMailer(true);

try {
    // SMTP settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ateeqrehman4809@gmail.com';
    $mail->Password   = 'dnzn gvkb vawy wkwd';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('ateeqrehman4809@gmail.com', 'Consultation Form');
    $mail->addAddress('ateeqrehman4809@gmail.com');
    $mail->isHTML(true);
    $mail->Subject = 'New Consultation Request';
    $mail->Body = "
        <h3>New Consultation Request</h3>
        <p><strong>Name:</strong> $fullName</p>
        <p><strong>Phone:</strong> $phoneNumber</p>
        <p><strong>Email:</strong> $emailAddress</p>
        <p><strong>Role:</strong> $userType</p>
        <p><strong>Service:</strong> $serviceInterest</p>
        <p><strong>Preferred Contact:</strong> $contactMethod</p>
        <p><strong>Preferred Time:</strong> $contactTime</p>
        <p><strong>Message:</strong><br>$message</p>
    ";

    $mail->send();

    // Optional user confirmation
    if (!empty($emailAddress)) {
        $mail->clearAddresses();
        $mail->addAddress($emailAddress);
        $mail->Subject = 'We Received Your Request';
        $mail->Body = "
            <h3>Thank You $fullName</h3>
            <p>We have received your consultation request.</p>
            <p>Our team will contact you soon.</p>
            <br>
            <p>Regards,<br>Support Team</p>
        ";
        $mail->send();
    }

    respondSuccess();
} catch (Exception $e) {
    error_log('send-mail.php error: ' . $mail->ErrorInfo);
    respondError('HTTP/1.1 500 Internal Server Error');
}
