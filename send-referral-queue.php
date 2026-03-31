<?php
/**
 * Cron worker to send queued referral emails.
 *
 * Setup (cPanel Cron Jobs):
 * * * * * php /home/<user>/public_html/send-referral-queue.php >/dev/null 2>&1
 *
 * Preferred: Mailgun API (recommended on shared hosting)
 * - MAILGUN_DOMAIN
 * - MAILGUN_API_BASE (e.g. https://api.mailgun.net)
 * - MAILGUN_API_KEY
 *
 * Fallback: SMTP via env vars
 * - REFERRAL_SMTP_HOST
 * - REFERRAL_SMTP_PORT (optional, default 587)
 * - REFERRAL_SMTP_USER
 * - REFERRAL_SMTP_PASS
 * - REFERRAL_SMTP_ENCRYPTION (tls|ssl|none, optional)
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

// Optional local config (recommended if hosting doesn't support env vars cleanly)
// Put the real values in /home/<user>/public_html/mailgun-config.php (not committed).
@include_once __DIR__ . '/mailgun-config.php';

define('FROM_EMAIL', getenv('MAILGUN_FROM_EMAIL') ?: 'noreply@loganexpresscare.com.au');
define('FROM_NAME',  getenv('MAILGUN_FROM_NAME')  ?: 'Logan Express Care');

define('MAILGUN_DOMAIN', getenv('MAILGUN_DOMAIN') ?: '');
define('MAILGUN_API_BASE', rtrim(getenv('MAILGUN_API_BASE') ?: 'https://api.mailgun.net', '/'));
define('MAILGUN_API_KEY', getenv('MAILGUN_API_KEY') ?: '');

define('REFERRAL_SMTP_HOST', getenv('REFERRAL_SMTP_HOST') ?: '');
define('REFERRAL_SMTP_PORT', (int)(getenv('REFERRAL_SMTP_PORT') ?: 587));
define('REFERRAL_SMTP_USER', getenv('REFERRAL_SMTP_USER') ?: '');
define('REFERRAL_SMTP_PASS', getenv('REFERRAL_SMTP_PASS') ?: '');
define('REFERRAL_SMTP_ENCRYPTION', getenv('REFERRAL_SMTP_ENCRYPTION') ?: 'tls');
define('REFERRAL_SMTP_TIMEOUT', (int)(getenv('REFERRAL_SMTP_TIMEOUT') ?: 10));

define('REFERRAL_MAIL_QUEUE_FILE', __DIR__ . '/referral-mail-queue.jsonl');
define('REFERRAL_MAIL_QUEUE_SENT_FILE', __DIR__ . '/referral-mail-queue.sent.jsonl');
define('REFERRAL_MAIL_QUEUE_FAILED_FILE', __DIR__ . '/referral-mail-queue.failed.jsonl');
define('REFERRAL_MAIL_LOG_FILE', __DIR__ . '/referral-mail.log');

function logLine(string $msg): void
{
    @file_put_contents(REFERRAL_MAIL_LOG_FILE, '[' . date('c') . '] ' . $msg . PHP_EOL, FILE_APPEND);
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

if (!file_exists(REFERRAL_MAIL_QUEUE_FILE)) {
    exit(0);
}

$mailgunConfigured = (MAILGUN_DOMAIN !== '' && MAILGUN_API_KEY !== '');
$smtpConfigured = (REFERRAL_SMTP_HOST !== '' && REFERRAL_SMTP_USER !== '' && REFERRAL_SMTP_PASS !== '');

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

$lines = @file(REFERRAL_MAIL_QUEUE_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if (!$lines) {
    exit(0);
}

// Truncate queue file early to avoid double-sends if cron overlaps.
@file_put_contents(REFERRAL_MAIL_QUEUE_FILE, '');

$mailer = null;
if ($smtpConfigured) {
    $mailer = new PHPMailer\PHPMailer\PHPMailer(true);
    $mailer->isSMTP();
    $mailer->Host = REFERRAL_SMTP_HOST;
    $mailer->SMTPAuth = true;
    $mailer->Username = REFERRAL_SMTP_USER;
    $mailer->Password = REFERRAL_SMTP_PASS;
    $mailer->Port = REFERRAL_SMTP_PORT;
    $mailer->Timeout = REFERRAL_SMTP_TIMEOUT;
    $mailer->CharSet = 'UTF-8';
    $mailer->isHTML(true);
    $mailer->setFrom(FROM_EMAIL, FROM_NAME);

    if (REFERRAL_SMTP_ENCRYPTION === 'ssl') {
        $mailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
    } elseif (REFERRAL_SMTP_ENCRYPTION === 'tls') {
        $mailer->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    } else {
        $mailer->SMTPSecure = false;
        $mailer->SMTPAutoTLS = false;
    }
}

$sent = 0;
$failed = 0;

foreach ($lines as $line) {
    $job = json_decode($line, true);
    if (!is_array($job) || empty($job['to']) || empty($job['subject']) || empty($job['html'])) {
        $failed++;
        @file_put_contents(REFERRAL_MAIL_QUEUE_FAILED_FILE, $line . PHP_EOL, FILE_APPEND);
        continue;
    }

    try {
        $to = (string)$job['to'];
        $subject = (string)$job['subject'];
        $html = (string)$job['html'];

        $sentOk = false;
        if ($mailgunConfigured) {
            $sentOk = sendViaMailgun($to, $subject, $html);
        }

        if (!$sentOk && $smtpConfigured && $mailer) {
            $mailer->clearAddresses();
            $mailer->Subject = $subject;
            $mailer->Body = $html;
            $mailer->addAddress($to);
            $mailer->send();
            $sentOk = true;
        }

        if (!$sentOk) {
            throw new \RuntimeException('No mail transport succeeded');
        }

        $sent++;
        @file_put_contents(REFERRAL_MAIL_QUEUE_SENT_FILE, $line . PHP_EOL, FILE_APPEND);
    } catch (\Throwable $e) {
        $failed++;
        logLine('Send failed to ' . $job['to'] . ': ' . $e->getMessage());
        @file_put_contents(REFERRAL_MAIL_QUEUE_FAILED_FILE, $line . PHP_EOL, FILE_APPEND);
    }
}

logLine("Done. sent={$sent} failed={$failed}");
exit(0);

