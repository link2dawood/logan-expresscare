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
 *
 * Preferred alternative (recommended on shared hosting):
 * - MAILGUN_DOMAIN
 * - MAILGUN_API_BASE (e.g. https://api.mailgun.net)
 * - MAILGUN_API_KEY
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

// Optional local config (recommended if hosting doesn't support env vars cleanly)
@include_once __DIR__ . '/mailgun-config.php';

define('QUEUE_FILE', __DIR__ . '/consultation-queue.jsonl');
define('SENT_FILE', __DIR__ . '/consultation-queue.sent.jsonl');
define('FAILED_FILE', __DIR__ . '/consultation-queue.failed.jsonl');
define('LOG_FILE', __DIR__ . '/consultation-mail.log');

define('MAILGUN_DOMAIN', getenv('MAILGUN_DOMAIN') ?: '');
define('MAILGUN_API_BASE', rtrim(getenv('MAILGUN_API_BASE') ?: 'https://api.mailgun.net', '/'));
define('MAILGUN_API_KEY', getenv('MAILGUN_API_KEY') ?: '');

define('SMTP_HOST',       getenv('MAILGUN_SMTP_HOST') ?: '');
define('SMTP_PORT',       (int)(getenv('MAILGUN_SMTP_PORT') ?: 465));
define('SMTP_USER',       getenv('MAILGUN_SMTP_USER') ?: '');
define('SMTP_PASS',       getenv('MAILGUN_SMTP_PASS') ?: '');
define('SMTP_ENCRYPTION', 'ssl');
define('SMTP_TIMEOUT',    10);

define('FROM_EMAIL',  getenv('MAILGUN_FROM_EMAIL')  ?: 'noreply@loganexpresscare.com.au');
define('FROM_NAME',   getenv('MAILGUN_FROM_NAME')   ?: 'Logan Express Care');
define('ADMIN_EMAIL', getenv('MAILGUN_ADMIN_EMAIL') ?: 'info@loganexpresscare.com.au');

function logLine(string $msg): void
{
    @file_put_contents(LOG_FILE, '[' . date('c') . '] ' . $msg . PHP_EOL, FILE_APPEND);
}

function sendViaMailgun(string $to, string $subject, string $html): bool
{
    if (!function_exists('curl_init')) {
        logLine('cURL not available for Mailgun');
        return false;
    }

    if (MAILGUN_DOMAIN === '' || MAILGUN_API_KEY === '') {
        return false;
    }

    $url = MAILGUN_API_BASE . '/v3/' . MAILGUN_DOMAIN . '/messages';

    $from = FROM_NAME !== ''
        ? (FROM_NAME . ' <' . FROM_EMAIL . '>')
        : FROM_EMAIL;

    $postFields = [
        'from' => $from,
        'to' => $to,
        'subject' => $subject,
        'html' => $html,
    ];

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postFields,
        CURLOPT_USERPWD => 'api:' . MAILGUN_API_KEY,
        CURLOPT_TIMEOUT => 15,
    ]);

    $body = curl_exec($ch);
    $err = curl_error($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($err) {
        logLine('Mailgun curl error: ' . $err);
        return false;
    }

    if ($status < 200 || $status >= 300) {
        logLine('Mailgun HTTP ' . $status . ' body=' . (string)$body);
        return false;
    }

    return true;
}

if (!file_exists(QUEUE_FILE)) {
    exit(0);
}

$mailgunConfigured = (MAILGUN_DOMAIN !== '' && MAILGUN_API_KEY !== '');
$smtpConfigured = (SMTP_HOST !== '' && SMTP_USER !== '' && SMTP_PASS !== '');

if (!$mailgunConfigured && !$smtpConfigured) {
    logLine('Mailgun and SMTP not configured; exiting');
    exit(0);
}

$phpMailerBase = __DIR__ . '/PHPMailer/src/';
if ($smtpConfigured) {
    if (!file_exists($phpMailerBase . 'PHPMailer.php')) {
        logLine('PHPMailer not found; cannot use SMTP');
        exit(1);
    }
    require_once $phpMailerBase . 'PHPMailer.php';
    require_once $phpMailerBase . 'SMTP.php';
    require_once $phpMailerBase . 'Exception.php';
}

$lines = @file(QUEUE_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if (!$lines) {
    exit(0);
}

// Truncate queue early to avoid duplicates on overlapping cron runs.
@file_put_contents(QUEUE_FILE, '');

$mailer = null;
if ($smtpConfigured) {
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
        $adminSubject = 'New Consultation Request';
        $adminHtml = buildAdminBody($payload);

        $adminSent = false;
        if ($mailgunConfigured) {
            $adminSent = sendViaMailgun(ADMIN_EMAIL, $adminSubject, $adminHtml);
        }
        if (!$adminSent && $smtpConfigured && $mailer) {
            $mailer->clearAddresses();
            $mailer->Subject = $adminSubject;
            $mailer->Body = $adminHtml;
            $mailer->addAddress(ADMIN_EMAIL);
            $mailer->send();
            $adminSent = true;
        }
        if (!$adminSent) {
            throw new \RuntimeException('Admin email transport failed');
        }

        // User confirmation email (if provided)
        $userEmail = trim((string)($payload['emailAddress'] ?? ''));
        if ($userEmail !== '' && filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            $userSubject = 'We Received Your Request';
            $userHtml = buildUserBody($payload);
            $userSent = false;

            if ($mailgunConfigured) {
                $userSent = sendViaMailgun($userEmail, $userSubject, $userHtml);
            }
            if (!$userSent && $smtpConfigured && $mailer) {
                $mailer->clearAddresses();
                $mailer->Subject = $userSubject;
                $mailer->Body = $userHtml;
                $mailer->addAddress($userEmail);
                $mailer->send();
                $userSent = true;
            }
            if (!$userSent) {
                throw new \RuntimeException('User email transport failed');
            }
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

