<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'booking_system');
define('DB_USER', 'admin-getgo');
define('DB_PASS', 'zain123@getgo');

// Email configuration
define('ADMIN_EMAIL', 'admin@loganexpresscare.com');
define('FROM_EMAIL', 'noreply@loganexpresscare.com');
define('FROM_NAME', 'Logan Express Care');

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

    $headers = array(
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=utf-8',
        'From: ' . FROM_NAME . ' <' . FROM_EMAIL . '>',
        'Reply-To: ' . ADMIN_EMAIL,
        'X-Mailer: PHP/' . phpversion()
    );

    // Send admin email
    $admin_mail_sent = mail(ADMIN_EMAIL, $admin_subject, $admin_body, implode("\r\n", $headers));

    // Send user email
    $user_mail_sent = mail($data['email'], $user_subject, $user_body, implode("\r\n", $headers));

    // You can choose whether email failure should block success or not
    // Here: form submission is considered successful if DB insert worked
    if (!$admin_mail_sent) {
        error_log("Admin email failed for contact ID #" . $contact_id);
    }

    if (!$user_mail_sent) {
        error_log("User confirmation email failed for contact ID #" . $contact_id);
    }

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