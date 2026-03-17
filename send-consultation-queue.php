<?php
/**
 * Cron worker to send queued consultation emails from consultation-queue.jsonl
 *
 * cPanel Cron (every minute):
 * * * * * php /home/<user>/public_html/send-consultation-queue.php >/dev/null 2>&1
 *
 * Required env vars on hosting:
 * - CONSULT_SMTP_HOST
 * - CONSULT_SMTP_PORT (optional, default 587)
 * - CONSULT_SMTP_USER
 * - CONSULT_SMTP_PASS
 * - CONSULT_SMTP_ENCRYPTION (tls|ssl|none, optional)
 *
 * Optional:
 * - CONSULT_FROM_EMAIL
 * - CONSULT_FROM_NAME
 * - CONSULT_ADMIN_EMAIL
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

define('QUEUE_FILE', __DIR__ . '/consultation-queue.jsonl');
define('SENT_FILE', __DIR__ . '/consultation-queue.sent.jsonl');
define('FAILED_FILE', __DIR__ . '/consultation-queue.failed.jsonl');
define('LOG_FILE', __DIR__ . '/consultation-mail.log');

define('SMTP_HOST', getenv('CONSULT_SMTP_HOST') ?: '');
define('SMTP_PORT', (int)(getenv('CONSULT_SMTP_PORT') ?: 587));
define('SMTP_USER', getenv('CONSULT_SMTP_USER') ?: '');
define('SMTP_PASS', getenv('CONSULT_SMTP_PASS') ?: '');
define('SMTP_ENCRYPTION', getenv('CONSULT_SMTP_ENCRYPTION') ?: 'tls');
define('SMTP_TIMEOUT', (int)(getenv('CONSULT_SMTP_TIMEOUT') ?: 10));

define('FROM_EMAIL', getenv('CONSULT_FROM_EMAIL') ?: 'noreply@loganexpresscare.com.au');
define('FROM_NAME', getenv('CONSULT_FROM_NAME') ?: 'Consultation Form');
define('ADMIN_EMAIL', getenv('CONSULT_ADMIN_EMAIL') ?: 'info@loganexpresscare.com.au');

function logLine(string $msg): void
{
    @file_put_contents(LOG_FILE, '[' . date('c') . '] ' . $msg . PHP_EOL, FILE_APPEND);
}

$smtpConfigured = (SMTP_HOST !== '' && SMTP_USER !== '' && SMTP_PASS !== '');
if (!$smtpConfigured) {
    logLine('SMTP not configured; exiting');
    exit(0);
}

if (!file_exists(QUEUE_FILE)) {
    exit(0);
}

$phpMailerBase = __DIR__ . '/PHPMailer/src/';
if (!file_exists($phpMailerBase . 'PHPMailer.php')) {
    logLine('PHPMailer not found; exiting');
    exit(1);
}

require_once $phpMailerBase . 'PHPMailer.php';
require_once $phpMailerBase . 'SMTP.php';
require_once $phpMailerBase . 'Exception.php';

$lines = @file(QUEUE_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if (!$lines) {
    exit(0);
}

// Truncate queue early to avoid duplicates on overlapping cron runs.
@file_put_contents(QUEUE_FILE, '');

$mailer = new PHPMailer\PHPMailer\PHPMailer(true);
$mailer->isSMTP();
$mailer->Host = SMTP_HOST;
$mailer->SMTPAuth = true;
$mailer->Username = SMTP_USER;
$mailer->Password = SMTP_PASS;
$mailer->Port = SMTP_PORT;
$mailer->Timeout = SMTP_TIMEOUT;
$mailer->CharSet = 'UTF-8';
$mailer->isHTML(true);
$mailer->setFrom(FROM_EMAIL, FROM_NAME);

if (SMTP_ENCRYPTION === 'ssl') {
    $mailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
} elseif (SMTP_ENCRYPTION === 'tls') {
    $mailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
} else {
    $mailer->SMTPSecure = false;
    $mailer->SMTPAutoTLS = false;
}

function buildAdminBody(array $data): string
{
    $safe = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    return "
        <h3>New Consultation Request</h3>
        <p><strong>Name:</strong> {$safe($data['fullName'] ?? '')}</p>
        <p><strong>Phone:</strong> {$safe($data['phoneNumber'] ?? '')}</p>
        <p><strong>Email:</strong> {$safe($data['emailAddress'] ?? '')}</p>
        <p><strong>Role:</strong> {$safe($data['userType'] ?? '')}</p>
        <p><strong>Service:</strong> {$safe($data['serviceInterest'] ?? '')}</p>
        <p><strong>Preferred Contact:</strong> {$safe($data['contactMethod'] ?? '')}</p>
        <p><strong>Preferred Time:</strong> {$safe($data['contactTime'] ?? '')}</p>
        <p><strong>Message:</strong><br>{$safe($data['message'] ?? '')}</p>
    ";
}

function buildUserBody(array $data): string
{
    $safe = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    $name = $safe($data['fullName'] ?? 'there');
    return "
        <h3>Thank You {$name}</h3>
        <p>We have received your consultation request.</p>
        <p>Our team will contact you soon.</p>
        <br>
        <p>Regards,<br>Logan Express Care</p>
    ";
}

$sent = 0;
$failed = 0;

foreach ($lines as $line) {
    $row = json_decode($line, true);

    // send-mail.php stores: {queued_at, reason, payload:{...}}
    $payload = is_array($row) ? ($row['payload'] ?? null) : null;
    if (!is_array($payload)) {
        $failed++;
        @file_put_contents(FAILED_FILE, $line . PHP_EOL, FILE_APPEND);
        continue;
    }

    try {
        // Admin email
        $mailer->clearAddresses();
        $mailer->Subject = 'New Consultation Request';
        $mailer->Body = buildAdminBody($payload);
        $mailer->addAddress(ADMIN_EMAIL);
        $mailer->send();

        // User confirmation email (if provided)
        $userEmail = trim((string)($payload['emailAddress'] ?? ''));
        if ($userEmail !== '' && filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            $mailer->clearAddresses();
            $mailer->Subject = 'We Received Your Request';
            $mailer->Body = buildUserBody($payload);
            $mailer->addAddress($userEmail);
            $mailer->send();
        }

        $sent++;
        @file_put_contents(SENT_FILE, $line . PHP_EOL, FILE_APPEND);
    } catch (\Throwable $e) {
        $failed++;
        logLine('Send failed: ' . $e->getMessage());
        @file_put_contents(FAILED_FILE, $line . PHP_EOL, FILE_APPEND);
    }
}

logLine("Done. sent={$sent} failed={$failed}");
exit(0);

