<?php
/**
 * Cron worker to send queued contact form emails.
 *
 * cPanel Cron (every minute):
 * * * * * php /home/<user>/public_html/send-contact-queue.php >/dev/null 2>&1
 *
 * Preferred: Mailgun API
 * - MAILGUN_DOMAIN
 * - MAILGUN_API_BASE (e.g. https://api.mailgun.net)
 * - MAILGUN_API_KEY
 *
 * Fallback: SMTP
 * - CONTACT_SMTP_HOST
 * - CONTACT_SMTP_PORT (optional, default 587)
 * - CONTACT_SMTP_USER
 * - CONTACT_SMTP_PASS
 * - CONTACT_SMTP_ENCRYPTION (tls|ssl|none, optional)
 *
 * Optional:
 * - CONTACT_FROM_EMAIL
 * - CONTACT_FROM_NAME
 * - CONTACT_ADMIN_EMAIL
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

// Optional local config (recommended if hosting doesn't support env vars cleanly)
@include_once __DIR__ . '/mailgun-config.php';

define('QUEUE_FILE', __DIR__ . '/contact-mail-queue.jsonl');
define('SENT_FILE', __DIR__ . '/contact-mail-queue.sent.jsonl');
define('FAILED_FILE', __DIR__ . '/contact-mail-queue.failed.jsonl');
define('LOG_FILE', __DIR__ . '/contact-mail.log');

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
    $from = FROM_NAME !== '' ? (FROM_NAME . ' <' . FROM_EMAIL . '>') : FROM_EMAIL;

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
$mailer = null;
if ($smtpConfigured) {
    if (!file_exists($phpMailerBase . 'PHPMailer.php')) {
        logLine('PHPMailer not found; cannot use SMTP');
        exit(1);
    }
    require_once $phpMailerBase . 'PHPMailer.php';
    require_once $phpMailerBase . 'SMTP.php';
    require_once $phpMailerBase . 'Exception.php';

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

$lines = @file(QUEUE_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if (!$lines) {
    exit(0);
}

@file_put_contents(QUEUE_FILE, '');

$sent = 0;
$failed = 0;

foreach ($lines as $line) {
    $job = json_decode($line, true);
    if (!is_array($job) || empty($job['to']) || empty($job['subject']) || empty($job['html'])) {
        $failed++;
        @file_put_contents(FAILED_FILE, $line . PHP_EOL, FILE_APPEND);
        continue;
    }

    try {
        $to = (string)$job['to'];
        $subject = (string)$job['subject'];
        $html = (string)$job['html'];

        // If a job is marked "admin", force to ADMIN_EMAIL (safer if template ever changes)
        if (($job['type'] ?? '') === 'admin') {
            $to = ADMIN_EMAIL;
        }

        $ok = false;
        if ($mailgunConfigured) {
            $ok = sendViaMailgun($to, $subject, $html);
        }
        if (!$ok && $smtpConfigured && $mailer) {
            $mailer->clearAddresses();
            $mailer->Subject = $subject;
            $mailer->Body = $html;
            $mailer->addAddress($to);
            $mailer->send();
            $ok = true;
        }
        if (!$ok) {
            throw new \RuntimeException('No mail transport succeeded');
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

