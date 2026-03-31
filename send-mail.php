<?php

@include_once __DIR__ . '/mailgun-config.php';

$DEBUG_MODE = isset($_REQUEST['debug']) && $_REQUEST['debug'] === '1';
$DEBUG_LOG_FILE = __DIR__ . '/send-mail-debug.log';
$MAIL_LOG_FILE = __DIR__ . '/consultation-mail.log';

$FROM_EMAIL  = getenv('MAILGUN_FROM_EMAIL') ?: 'noreply@loganexpresscare.com.au';
$FROM_NAME   = getenv('MAILGUN_FROM_NAME') ?: 'Consultation Form';
$ADMIN_EMAIL = getenv('MAILGUN_ADMIN_EMAIL') ?: 'info@loganexpresscare.com.au';

function mailgunSend($to, $subject, $html)
{
    global $FROM_EMAIL, $FROM_NAME, $MAIL_LOG_FILE;

    $smtpHost = getenv('MAILGUN_SMTP_HOST') ?: '';
    $smtpUser = getenv('MAILGUN_SMTP_USER') ?: '';
    $smtpPass = getenv('MAILGUN_SMTP_PASS') ?: '';
    $smtpPort = (int)(getenv('MAILGUN_SMTP_PORT') ?: 587);

    if ($smtpHost === '' || $smtpUser === '' || $smtpPass === '') {
        @file_put_contents($MAIL_LOG_FILE, '[' . date('c') . '] SMTP not configured' . PHP_EOL, FILE_APPEND);
        return false;
    }

    $base = __DIR__ . '/PHPMailer/src/';
    require_once $base . 'PHPMailer.php';
    require_once $base . 'SMTP.php';
    require_once $base . 'Exception.php';

    try {
        $mailer = new PHPMailer\PHPMailer\PHPMailer(true);
        $mailer->isSMTP();
        $mailer->Host       = $smtpHost;
        $mailer->SMTPAuth   = true;
        $mailer->Username   = $smtpUser;
        $mailer->Password   = $smtpPass;
        $mailer->Port       = $smtpPort;
        $mailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mailer->CharSet    = 'UTF-8';
        $mailer->isHTML(true);
        $mailer->setFrom($FROM_EMAIL, $FROM_NAME);
        $mailer->addAddress($to);
        $mailer->Subject = $subject;
        $mailer->Body    = $html;
        $mailer->send();
        @file_put_contents($MAIL_LOG_FILE, '[' . date('c') . '] Sent to ' . $to . PHP_EOL, FILE_APPEND);
        return true;
    } catch (\Throwable $e) {
        @file_put_contents($MAIL_LOG_FILE, '[' . date('c') . '] SMTP error: ' . $e->getMessage() . PHP_EOL, FILE_APPEND);
        return false;
    }
}

function buildConsultAdminHtml($data)
{
    $safe = function($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
    return "
        <h3>New Consultation Request</h3>
        <p><strong>Name:</strong> {$safe($data['fullName'])}</p>
        <p><strong>Phone:</strong> {$safe($data['phoneNumber'])}</p>
        <p><strong>Email:</strong> {$safe($data['emailAddress'])}</p>
        <p><strong>Role:</strong> {$safe($data['userType'])}</p>
        <p><strong>Service:</strong> {$safe($data['serviceInterest'])}</p>
        <p><strong>Preferred Contact:</strong> {$safe($data['contactMethod'])}</p>
        <p><strong>Preferred Time:</strong> {$safe($data['contactTime'])}</p>
        <p><strong>Message:</strong><br>{$safe($data['message'])}</p>
    ";
}

function buildConsultUserHtml($data)
{
    $safe = function($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); };
    $name = $safe($data['fullName'] ?? 'there');
    return "
        <h3>Thank You {$name}</h3>
        <p>We have received your consultation request.</p>
        <p>Our team will contact you soon.</p>
        <br>
        <p>Regards,<br>Logan Express Care</p>
    ";
}

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
    // Send admin notification via Mailgun
    $adminSent = mailgunSend(
        $ADMIN_EMAIL,
        'New Consultation Request',
        buildConsultAdminHtml($payload)
    );
    debugLog('Admin email via Mailgun', array('sent' => $adminSent));

    // Send user confirmation if email provided
    if (!empty($payload['emailAddress'])) {
        $userSent = mailgunSend(
            $payload['emailAddress'],
            'We Received Your Request',
            buildConsultUserHtml($payload)
        );
        debugLog('User confirmation via Mailgun', array('sent' => $userSent));
    }

    respondSuccess();
} catch (Exception $e) {
    error_log('send-mail.php error: ' . $e->getMessage());
    debugLog('Exception caught', array('exception' => $e->getMessage()));
    respondError('HTTP/1.1 500 Internal Server Error', $e->getMessage());
}

// If respondSuccess() returned (shouldn't), exit.
exit;
