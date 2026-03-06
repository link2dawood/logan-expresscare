<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

$DEBUG_MODE = isset($_REQUEST['debug']) && $_REQUEST['debug'] === '1';
$DEBUG_LOG_FILE = __DIR__ . '/send-mail-debug.log';

function isAjaxRequest()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function debugLog($message, $context = null)
{
    global $DEBUG_MODE, $DEBUG_LOG_FILE;

    if (!$DEBUG_MODE) {
        return;
    }

    $entry = '[' . date('Y-m-d H:i:s') . '] ' . $message;
    if ($context !== null) {
        $json = json_encode($context);
        if ($json !== false) {
            $entry .= ' | ' . $json;
        }
    }
    $entry .= PHP_EOL;

    @file_put_contents($DEBUG_LOG_FILE, $entry, FILE_APPEND);
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

function respondError($statusLine, $debugMessage = '')
{
    global $DEBUG_MODE;

    if (isAjaxRequest()) {
        header($statusLine);
        if ($DEBUG_MODE && $debugMessage !== '') {
            echo 'error:' . $debugMessage;
        } else {
            echo 'error';
        }
        exit;
    }

    header('Location: single-page.php?status=error');
    exit;
}

register_shutdown_function(function () {
    $error = error_get_last();
    if (!$error) {
        return;
    }

    $fatalTypes = array(E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR);
    if (in_array($error['type'], $fatalTypes, true)) {
        debugLog('Fatal shutdown error', $error);
    }
});

debugLog('Request started', array(
    'method' => isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '',
    'ajax' => isAjaxRequest(),
    'php_version' => PHP_VERSION,
    'post_keys' => array_keys($_POST),
));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondError('HTTP/1.1 405 Method Not Allowed', 'invalid_method');
}

$phpMailerFiles = array(
    'PHPMailer.php' => __DIR__ . '/PHPMailer/src/PHPMailer.php',
    'SMTP.php' => __DIR__ . '/PHPMailer/src/SMTP.php',
    'Exception.php' => __DIR__ . '/PHPMailer/src/Exception.php',
);

foreach ($phpMailerFiles as $name => $path) {
    if (!file_exists($path)) {
        debugLog('Missing PHPMailer file', array('file' => $name, 'path' => $path));
        respondError('HTTP/1.1 500 Internal Server Error', 'missing_phpmailer_file_' . $name);
    }
    require_once $path;
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
    debugLog('Configuring SMTP transport');

    if ($DEBUG_MODE) {
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function ($str, $level) {
            debugLog('SMTP debug', array('level' => $level, 'message' => $str));
        };
    }

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
    debugLog('Admin email sent');

    // Optional user confirmation
    if (!empty($emailAddress)) {
        debugLog('Sending confirmation email to user', array('email' => $emailAddress));
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
        debugLog('User confirmation email sent');
    }

    debugLog('Request completed successfully');
    respondSuccess();
} catch (Exception $e) {
    $mailError = isset($mail->ErrorInfo) ? $mail->ErrorInfo : '';
    $debugMessage = $mailError !== '' ? $mailError : $e->getMessage();

    error_log('send-mail.php error: ' . $debugMessage);
    debugLog('Exception caught', array(
        'exception' => $e->getMessage(),
        'mail_error' => $mailError,
    ));

    respondError('HTTP/1.1 500 Internal Server Error', $debugMessage);
}
