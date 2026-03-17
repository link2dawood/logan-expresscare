<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

$DEBUG_MODE = isset($_REQUEST['debug']) && $_REQUEST['debug'] === '1';
$DEBUG_LOG_FILE = __DIR__ . '/send-mail-debug.log';
$QUEUE_FILE = __DIR__ . '/consultation-queue.jsonl';
$MAIL_LOG_FILE = __DIR__ . '/consultation-mail.log';

// SMTP configuration (recommended). Configure via environment variables on hosting.
// If not set, this script will queue only (cron worker should be used).
$SMTP_HOST = getenv('CONSULT_SMTP_HOST') ?: '';
$SMTP_PORT = (int)(getenv('CONSULT_SMTP_PORT') ?: 587);
$SMTP_USER = getenv('CONSULT_SMTP_USER') ?: '';
$SMTP_PASS = getenv('CONSULT_SMTP_PASS') ?: '';
$SMTP_ENCRYPTION = getenv('CONSULT_SMTP_ENCRYPTION') ?: 'tls'; // tls|ssl|none
$SMTP_TIMEOUT = (int)(getenv('CONSULT_SMTP_TIMEOUT') ?: 4);

$FROM_EMAIL = getenv('CONSULT_FROM_EMAIL') ?: '';
$FROM_NAME = getenv('CONSULT_FROM_NAME') ?: 'Consultation Form';
$ADMIN_EMAIL = getenv('CONSULT_ADMIN_EMAIL') ?: '';

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

function sendConsultationEmails($mailer, $data)
{
    global $FROM_EMAIL, $FROM_NAME, $ADMIN_EMAIL;

    // Backward-compatible defaults (avoid hard dependency on env vars)
    $fromEmail = $FROM_EMAIL !== '' ? $FROM_EMAIL : 'noreply@loganexpresscare.com.au';
    $fromName = $FROM_NAME !== '' ? $FROM_NAME : 'Consultation Form';
    $adminEmail = $ADMIN_EMAIL !== '' ? $ADMIN_EMAIL : 'info@loganexpresscare.com.au';

    $adminBody = "
        <h3>New Consultation Request</h3>
        <p><strong>Name:</strong> {$data['fullName']}</p>
        <p><strong>Phone:</strong> {$data['phoneNumber']}</p>
        <p><strong>Email:</strong> {$data['emailAddress']}</p>
        <p><strong>Role:</strong> {$data['userType']}</p>
        <p><strong>Service:</strong> {$data['serviceInterest']}</p>
        <p><strong>Preferred Contact:</strong> {$data['contactMethod']}</p>
        <p><strong>Preferred Time:</strong> {$data['contactTime']}</p>
        <p><strong>Message:</strong><br>{$data['message']}</p>
    ";

    $mailer->setFrom($fromEmail, $fromName);
    $mailer->addAddress($adminEmail);
    $mailer->isHTML(true);
    $mailer->Subject = 'New Consultation Request';
    $mailer->Body = $adminBody;
    $mailer->send();
    debugLog('Admin email sent');

    if (!empty($data['emailAddress'])) {
        debugLog('Sending confirmation email to user', array('email' => $data['emailAddress']));
        $mailer->clearAddresses();
        $mailer->addAddress($data['emailAddress']);
        $mailer->Subject = 'We Received Your Request';
        $mailer->Body = "
            <h3>Thank You {$data['fullName']}</h3>
            <p>We have received your consultation request.</p>
            <p>Our team will contact you soon.</p>
            <br>
            <p>Regards,<br>Support Team</p>
        ";
        $mailer->send();
        debugLog('User confirmation email sent');
    }
}

function queueSubmission($data, $reason)
{
    global $QUEUE_FILE;

    $entry = array(
        'queued_at' => date('c'),
        'reason' => $reason,
        'payload' => $data,
    );

    $json = json_encode($entry);
    if ($json === false) {
        return false;
    }

    $result = @file_put_contents($QUEUE_FILE, $json . PHP_EOL, FILE_APPEND | LOCK_EX);
    return $result !== false;
}

function logMailLine($message)
{
    global $MAIL_LOG_FILE;
    @file_put_contents($MAIL_LOG_FILE, '[' . date('c') . '] ' . $message . PHP_EOL, FILE_APPEND);
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

$userType        = isset($_POST['userType']) ? $_POST['userType'] : '';
$serviceInterest = isset($_POST['serviceInterest']) ? $_POST['serviceInterest'] : '';
$fullName        = isset($_POST['fullName']) ? $_POST['fullName'] : '';
$phoneNumber     = isset($_POST['phoneNumber']) ? $_POST['phoneNumber'] : '';
$emailAddress    = isset($_POST['emailAddress']) ? $_POST['emailAddress'] : '';
$contactMethod   = isset($_POST['contactMethod']) ? $_POST['contactMethod'] : '';
$contactTime     = isset($_POST['contactTime']) ? $_POST['contactTime'] : '';
$message         = isset($_POST['message']) ? $_POST['message'] : '';

$payload = array(
    'userType' => $userType,
    'serviceInterest' => $serviceInterest,
    'fullName' => $fullName,
    'phoneNumber' => $phoneNumber,
    'emailAddress' => $emailAddress,
    'contactMethod' => $contactMethod,
    'contactTime' => $contactTime,
    'message' => $message,
);

try {
    // Always queue first so the browser redirect is fast (no SMTP/mail() blocking).
    if (!queueSubmission($payload, 'queued')) {
        logMailLine('Queue write failed');
    } else {
        debugLog('Submission queued', array('queue_file' => $QUEUE_FILE));
    }

    // Respond immediately (front-end redirects to thankyou.php on "success")
    respondSuccess();
} catch (Exception $e) {
    error_log('send-mail.php error: ' . $e->getMessage());
    debugLog('Exception caught', array('exception' => $e->getMessage()));
    respondError('HTTP/1.1 500 Internal Server Error', $e->getMessage());
}

// If respondSuccess() returned (shouldn't), exit.
exit;
