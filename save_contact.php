<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

@include_once __DIR__ . '/mailgun-config.php';

header('Content-Type: application/json');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'booking_system');
define('DB_USER', 'admin-getgo');
define('DB_PASS', 'zain123@getgo');

// Email configuration
define('ADMIN_EMAIL',  getenv('MAILGUN_ADMIN_EMAIL') ?: 'info@loganexpresscare.com.au');
define('FROM_EMAIL',   getenv('MAILGUN_FROM_EMAIL')  ?: 'noreply@loganexpresscare.com.au');
define('FROM_NAME',    getenv('MAILGUN_FROM_NAME')   ?: 'Logan Express Care');
define('MG_DOMAIN',    getenv('MAILGUN_DOMAIN')      ?: '');
define('MG_API_BASE',  rtrim(getenv('MAILGUN_API_BASE') ?: 'https://api.mailgun.net', '/'));
define('MG_API_KEY',   getenv('MAILGUN_API_KEY')     ?: '');

function sendViaMailgun($to, $subject, $html, $logFile = null) {
    if (!function_exists('curl_init') || MG_DOMAIN === '' || MG_API_KEY === '') {
        return false;
    }
    $url  = MG_API_BASE . '/v3/' . MG_DOMAIN . '/messages';
    $from = FROM_NAME . ' <' . FROM_EMAIL . '>';
    $ch   = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => array('from' => $from, 'to' => $to, 'subject' => $subject, 'html' => $html),
        CURLOPT_USERPWD        => 'api:' . MG_API_KEY,
        CURLOPT_TIMEOUT        => 15,
    ));
    $body   = curl_exec($ch);
    $err    = curl_error($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($err || $status < 200 || $status >= 300) {
        if ($logFile) {
            @file_put_contents($logFile, '[' . date('c') . '] Mailgun error status=' . $status . ' err=' . $err . PHP_EOL, FILE_APPEND);
        }
        return false;
    }
    return true;
}

$CONTACT_MAIL_LOG_FILE = __DIR__ . '/contact-mail.log';

$response = array(
    'success' => false,
    'message' => ''
);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    function sanitize_input($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }

    // Required fields
    $required_fields = ['fname', 'lname', 'phone', 'email', 'message'];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            throw new Exception('Please fill in all required fields.');
        }
    }

    // Collect data
    $data = array(
        'fname'      => sanitize_input($_POST['fname']),
        'lname'      => sanitize_input($_POST['lname']),
        'phone'      => sanitize_input($_POST['phone']),
        'email'      => sanitize_input($_POST['email']),
        'message'    => sanitize_input($_POST['message']),
        'created_at' => date('Y-m-d H:i:s'),
    );

    // Validate email
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address format.');
    }

    // Database connection
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            )
        );
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        throw new Exception('Database connection error. Please try again later.');
    }

    // Insert into database
    $sql = "INSERT INTO contact (`first_name`, `last_name`, email, phone, message, created_at)
            VALUES (:fname, :lname, :email, :phone, :message, :created_at)";

    $stmt = $pdo->prepare($sql);

    if (!$stmt->execute($data)) {
        throw new Exception('Failed to save contact message.');
    }

    $contact_id = $pdo->lastInsertId();

    // Admin email
    $admin_subject = "New Contact Form Submission - " . $data['fname'] . " " . $data['lname'];

    $admin_body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background: #2c5f4f; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .section-title { background: #D4A574; color: white; padding: 10px; font-weight: bold; margin-bottom: 15px; }
            .field { margin-bottom: 10px; }
            .label { font-weight: bold; color: #2c5f4f; }
            .footer { background: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h2>New Contact Form Submission</h2>
            <p>Contact ID: #" . $contact_id . "</p>
        </div>

        <div class='content'>
            <div class='section-title'>Contact Details</div>

            <div class='field'><span class='label'>First Name:</span> " . $data['fname'] . "</div>
            <div class='field'><span class='label'>Last Name:</span> " . $data['lname'] . "</div>
            <div class='field'><span class='label'>Phone:</span> " . $data['phone'] . "</div>
            <div class='field'><span class='label'>Email:</span> " . $data['email'] . "</div>
            <div class='field'><span class='label'>Message:</span><br>" . nl2br($data['message']) . "</div>
            <div class='field'><span class='label'>Submitted At:</span> " . $data['created_at'] . "</div>
        </div>

        <div class='footer'>
            <p>This is an automated email from Logan Express Care Contact Form</p>
        </div>
    </body>
    </html>";

    // User confirmation email
    $user_subject = "We Received Your Message - Logan Express Care";

    $user_body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background: #2c5f4f; color: white; padding: 20px; text-align: center; }
            .content { padding: 30px; }
            .highlight { background: #D4A574; color: white; padding: 15px; margin: 20px 0; text-align: center; }
            .footer { background: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h2>Thank You for Contacting Us</h2>
        </div>

        <div class='content'>
            <p>Dear " . $data['fname'] . " " . $data['lname'] . ",</p>

            <p>Thank you for contacting Logan Express Care. We have received your message successfully and our team will get back to you as soon as possible.</p>

            <div class='highlight'>
                <strong>Your Reference ID: #" . $contact_id . "</strong>
            </div>

            <p><strong>Your submitted message:</strong></p>
            <p>" . nl2br($data['message']) . "</p>

            <p>If your matter is urgent, please contact us directly.</p>

            <p>Best regards,<br><strong>Logan Express Care Team</strong></p>
        </div>

        <div class='footer'>
            <p>This is an automated confirmation email from Logan Express Care</p>
            <p>&copy; " . date('Y') . " Logan Express Care. All rights reserved.</p>
        </div>
    </body>
    </html>";

    // Send emails via Mailgun directly
    sendViaMailgun(ADMIN_EMAIL, $admin_subject, $admin_body, $CONTACT_MAIL_LOG_FILE);
    sendViaMailgun($data['email'], $user_subject, $user_body, $CONTACT_MAIL_LOG_FILE);

    $response['success'] = true;
    $response['message'] = 'Thank you! Your message has been submitted successfully.';
    $response['contact_id'] = $contact_id;

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    error_log("Contact form error: " . $e->getMessage());
}

echo json_encode($response);
exit;
?>