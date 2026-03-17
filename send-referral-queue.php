<?php
/**
 * Cron worker to send queued referral emails.
 *
 * Setup (cPanel Cron Jobs):
 * * * * * /usr/local/bin/php -d detect_unicode=0 /home/<user>/public_html/send-referral-queue.php >/dev/null 2>&1
 *
 * SMTP must be configured via env vars on the server:
 * - REFERRAL_SMTP_HOST
 * - REFERRAL_SMTP_PORT (optional, default 587)
 * - REFERRAL_SMTP_USER
 * - REFERRAL_SMTP_PASS
 * - REFERRAL_SMTP_ENCRYPTION (tls|ssl|none, optional)
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

define('FROM_EMAIL', getenv('REFERRAL_FROM_EMAIL') ?: 'noreply@loganexpresscare.com');
define('FROM_NAME', getenv('REFERRAL_FROM_NAME') ?: 'Logan Express Care');

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

$smtpConfigured = (REFERRAL_SMTP_HOST !== '' && REFERRAL_SMTP_USER !== '' && REFERRAL_SMTP_PASS !== '');
if (!$smtpConfigured) {
    logLine('SMTP not configured; exiting');
    exit(0);
}

if (!file_exists(REFERRAL_MAIL_QUEUE_FILE)) {
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

$lines = @file(REFERRAL_MAIL_QUEUE_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if (!$lines) {
    exit(0);
}

// Truncate queue file early to avoid double-sends if cron overlaps.
@file_put_contents(REFERRAL_MAIL_QUEUE_FILE, '');

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
        $mailer->clearAddresses();
        $mailer->Subject = (string)$job['subject'];
        $mailer->Body = (string)$job['html'];
        $mailer->addAddress((string)$job['to']);
        $mailer->send();

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

