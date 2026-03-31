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

define('MAILGUN_DOMAIN',   getenv('MAILGUN_DOMAIN')   ?: '');
define('MAILGUN_API_BASE', rtrim(getenv('MAILGUN_API_BASE') ?: 'https://api.mailgun.net', '/'));
define('MAILGUN_API_KEY',  getenv('MAILGUN_API_KEY')  ?: '');

define('REFERRAL_SMTP_HOST',       getenv('MAILGUN_SMTP_HOST') ?: '');
define('REFERRAL_SMTP_PORT',       (int)(getenv('MAILGUN_SMTP_PORT') ?: 465));
define('REFERRAL_SMTP_USER',       getenv('MAILGUN_SMTP_USER') ?: '');
define('REFERRAL_SMTP_PASS',       getenv('MAILGUN_SMTP_PASS') ?: '');
define('REFERRAL_SMTP_ENCRYPTION', 'ssl');
define('REFERRAL_SMTP_TIMEOUT',    10);

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

if (MAILGUN_DOMAIN === '' || MAILGUN_API_KEY === '') {
    logLine('Mailgun API not configured; exiting');
    exit(0);
}

$lines = @file(REFERRAL_MAIL_QUEUE_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if (!$lines) {
    exit(0);
}

@file_put_contents(REFERRAL_MAIL_QUEUE_FILE, '');

$sent = 0;
$failed = 0;

foreach ($lines as $line) {
    $job = json_decode($line, true);
    if (!is_array($job) || empty($job['to']) || empty($job['subject']) || empty($job['html'])) {
        $failed++;
        @file_put_contents(REFERRAL_MAIL_QUEUE_FAILED_FILE, $line . PHP_EOL, FILE_APPEND);
        continue;
    }

    $sentOk = sendViaMailgun((string)$job['to'], (string)$job['subject'], (string)$job['html']);

    if ($sentOk) {
        $sent++;
        @file_put_contents(REFERRAL_MAIL_QUEUE_SENT_FILE, $line . PHP_EOL, FILE_APPEND);
    } else {
        $failed++;
        @file_put_contents(REFERRAL_MAIL_QUEUE_FAILED_FILE, $line . PHP_EOL, FILE_APPEND);
    }
}

logLine("Done. sent={$sent} failed={$failed}");
exit(0);

