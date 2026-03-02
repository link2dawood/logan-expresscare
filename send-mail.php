<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $userType        = $_POST['userType'];
    $serviceInterest = $_POST['serviceInterest'];
    $fullName        = $_POST['fullName'];
    $phoneNumber     = $_POST['phoneNumber'];
    $emailAddress    = $_POST['emailAddress'];
    $contactMethod   = $_POST['contactMethod'];
    $contactTime     = $_POST['contactTime'];
    $message         = $_POST['message'];

    $mail = new PHPMailer(true);

    try {

        // ================= SMTP SETTINGS =================
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ateeqrehman4809@gmail.com';
        $mail->Password   = 'dnzn gvkb vawy wkwd'; // <-- Yahan app password paste karein
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('ateeqrehman4809@gmail.com', 'Consultation Form');

        // ================= ADMIN EMAIL =================
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

        // ================= USER CONFIRMATION =================
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

        echo "<script>alert('Form submitted successfully!'); window.location.href='index.html';</script>";

    } catch (Exception $e) {
        echo "Message could not be sent. Error: {$mail->ErrorInfo}";
    }
}