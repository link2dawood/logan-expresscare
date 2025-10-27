<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set response header to JSON
header('Content-Type: application/json');

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'booking_system');
define('DB_USER', 'admin-getgo');
define('DB_PASS', 'zain123@getgo');

// Email configuration
define('ADMIN_EMAIL', 'admin@loganexpresscare.com'); // Change to your email
define('FROM_EMAIL', 'noreply@loganexpresscare.com');
define('FROM_NAME', 'Logan Express Care');

// Response array
$response = array('success' => false, 'message' => '');

try {
    // Check if form is submitted
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Sanitize and validate input data
    function sanitize_input($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }

    // Required fields validation
    $required_fields = ['referral_source', 'first_name', 'last_name', 'dob', 'gender', 
                        'home_address', 'phone_number', 'has_guardian', 'primary_service'];
    
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            throw new Exception("Please fill in all required fields: " . str_replace('_', ' ', $field));
        }
    }

    // Validate consent checkboxes
    if (!isset($_POST['consent_privacy']) || $_POST['consent_privacy'] !== 'yes') {
        throw new Exception('You must consent to privacy terms');
    }
    if (!isset($_POST['consent_contact']) || $_POST['consent_contact'] !== 'yes') {
        throw new Exception('You must consent to contact authorization');
    }

    // Collect and sanitize form data
    $data = array(
        // Referral Source
        'referral_source' => sanitize_input($_POST['referral_source']),
        
        // Participant Details
        'first_name' => sanitize_input($_POST['first_name']),
        'last_name' => sanitize_input($_POST['last_name']),
        'dob' => sanitize_input($_POST['dob']),
        'gender' => sanitize_input($_POST['gender']),
        'home_address' => sanitize_input($_POST['home_address']),
        'phone_number' => sanitize_input($_POST['phone_number']),
        'email_address' => sanitize_input($_POST['email_address'] ?? ''),
        'ndis_number' => sanitize_input($_POST['ndis_number'] ?? ''),
        'has_guardian' => sanitize_input($_POST['has_guardian']),
        
        // Cultural Details
        'country_of_birth' => sanitize_input($_POST['country_of_birth'] ?? ''),
        'require_interpreter' => sanitize_input($_POST['require_interpreter'] ?? ''),
        'relevant_culture' => sanitize_input($_POST['relevant_culture'] ?? ''),
        'identify_aboriginal' => sanitize_input($_POST['identify_aboriginal'] ?? ''),
        
        // Services Request
        'primary_service' => sanitize_input($_POST['primary_service']),
        'hours_required' => sanitize_input($_POST['hours_required'] ?? ''),
        'secondary_service' => sanitize_input($_POST['secondary_service'] ?? ''),
        'additional_service' => sanitize_input($_POST['additional_service'] ?? ''),
        'participant_conditions' => sanitize_input($_POST['participant_conditions'] ?? ''),
        'extra_information' => sanitize_input($_POST['extra_information'] ?? ''),
        'special_assessments' => sanitize_input($_POST['special_assessments'] ?? ''),
        'practitioner_notes' => sanitize_input($_POST['practitioner_notes'] ?? ''),
        
        // Referrer Information
        'referrer_name' => sanitize_input($_POST['referrer_name'] ?? ''),
        'referrer_relationship' => sanitize_input($_POST['referrer_relationship'] ?? ''),
        'referrer_phone' => sanitize_input($_POST['referrer_phone'] ?? ''),
        'referrer_email' => sanitize_input($_POST['referrer_email'] ?? ''),
        
        // Consent
        'consent_privacy' => 'yes',
        'consent_contact' => 'yes',
        
        // Meta
        'submission_date' => date('Y-m-d H:i:s'),
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? ''
    );

    // Validate email format if provided
    if (!empty($data['email_address']) && !filter_var($data['email_address'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address format');
    }
    if (!empty($data['referrer_email']) && !filter_var($data['referrer_email'], FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid referrer email address format');
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

    // Insert data into database
    $sql = "INSERT INTO referrals (
        referral_source, first_name, last_name, dob, gender, home_address, 
        phone_number, email_address, ndis_number, has_guardian,
        country_of_birth, require_interpreter, relevant_culture, identify_aboriginal,
        primary_service, hours_required, secondary_service, additional_service,
        participant_conditions, extra_information, special_assessments, practitioner_notes,
        referrer_name, referrer_relationship, referrer_phone, referrer_email,
        consent_privacy, consent_contact, submission_date, ip_address, status
    ) VALUES (
        :referral_source, :first_name, :last_name, :dob, :gender, :home_address,
        :phone_number, :email_address, :ndis_number, :has_guardian,
        :country_of_birth, :require_interpreter, :relevant_culture, :identify_aboriginal,
        :primary_service, :hours_required, :secondary_service, :additional_service,
        :participant_conditions, :extra_information, :special_assessments, :practitioner_notes,
        :referrer_name, :referrer_relationship, :referrer_phone, :referrer_email,
        :consent_privacy, :consent_contact, :submission_date, :ip_address, 'pending'
    )";

    $stmt = $pdo->prepare($sql);
    
    if (!$stmt->execute($data)) {
        throw new Exception('Failed to save referral data');
    }

    $referral_id = $pdo->lastInsertId();

    // Send email notification to admin
    $email_subject = "New Referral Submission - " . $data['first_name'] . " " . $data['last_name'];
    
    $email_body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background: #2c5f4f; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .section { margin-bottom: 25px; }
            .section-title { background: #D4A574; color: white; padding: 10px; font-weight: bold; margin-bottom: 10px; }
            .field { margin-bottom: 10px; }
            .label { font-weight: bold; color: #2c5f4f; }
            .value { color: #555; }
            .footer { background: #f4f4f4; padding: 15px; text-align: center; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h2>New Referral Submission</h2>
            <p>Referral ID: #" . $referral_id . "</p>
        </div>
        
        <div class='content'>
            <div class='section'>
                <div class='section-title'>Referral Source</div>
                <div class='field'><span class='label'>Completed By:</span> <span class='value'>" . ucwords(str_replace('_', ' ', $data['referral_source'])) . "</span></div>
            </div>
            
            <div class='section'>
                <div class='section-title'>Participant Details</div>
                <div class='field'><span class='label'>Name:</span> <span class='value'>" . $data['first_name'] . " " . $data['last_name'] . "</span></div>
                <div class='field'><span class='label'>Date of Birth:</span> <span class='value'>" . $data['dob'] . "</span></div>
                <div class='field'><span class='label'>Gender:</span> <span class='value'>" . ucfirst($data['gender']) . "</span></div>
                <div class='field'><span class='label'>Home Address:</span> <span class='value'>" . $data['home_address'] . "</span></div>
                <div class='field'><span class='label'>Phone:</span> <span class='value'>" . $data['phone_number'] . "</span></div>
                <div class='field'><span class='label'>Email:</span> <span class='value'>" . ($data['email_address'] ?: 'Not provided') . "</span></div>
                <div class='field'><span class='label'>NDIS Number:</span> <span class='value'>" . ($data['ndis_number'] ?: 'Not provided') . "</span></div>
                <div class='field'><span class='label'>Has Guardian:</span> <span class='value'>" . ucfirst($data['has_guardian']) . "</span></div>
            </div>
            
            <div class='section'>
                <div class='section-title'>Cultural Details</div>
                <div class='field'><span class='label'>Country of Birth:</span> <span class='value'>" . ($data['country_of_birth'] ?: 'Not provided') . "</span></div>
                <div class='field'><span class='label'>Requires Interpreter:</span> <span class='value'>" . ($data['require_interpreter'] ?: 'Not specified') . "</span></div>
                <div class='field'><span class='label'>Cultural Considerations:</span> <span class='value'>" . ($data['relevant_culture'] ?: 'None') . "</span></div>
                <div class='field'><span class='label'>Aboriginal/Torres Strait Islander:</span> <span class='value'>" . ($data['identify_aboriginal'] ?: 'Not specified') . "</span></div>
            </div>
            
            <div class='section'>
                <div class='section-title'>Services Requested</div>
                <div class='field'><span class='label'>Primary Service:</span> <span class='value'>" . ucwords(str_replace('_', ' ', $data['primary_service'])) . "</span></div>
                <div class='field'><span class='label'>Hours Required:</span> <span class='value'>" . ($data['hours_required'] ?: 'Not specified') . "</span></div>
                <div class='field'><span class='label'>Secondary Service:</span> <span class='value'>" . ($data['secondary_service'] ? ucwords(str_replace('_', ' ', $data['secondary_service'])) : 'Not requested') . "</span></div>
                <div class='field'><span class='label'>Additional Service:</span> <span class='value'>" . ($data['additional_service'] ? ucwords(str_replace('_', ' ', $data['additional_service'])) : 'Not requested') . "</span></div>
                <div class='field'><span class='label'>Conditions/Disability:</span> <span class='value'>" . ($data['participant_conditions'] ?: 'Not provided') . "</span></div>
                <div class='field'><span class='label'>Extra Information:</span> <span class='value'>" . ($data['extra_information'] ?: 'None') . "</span></div>
                <div class='field'><span class='label'>Special Assessments:</span> <span class='value'>" . ($data['special_assessments'] ?: 'None') . "</span></div>
                <div class='field'><span class='label'>Practitioner Notes:</span> <span class='value'>" . ($data['practitioner_notes'] ?: 'None') . "</span></div>
            </div>";
    
    if (!empty($data['referrer_name'])) {
        $email_body .= "
            <div class='section'>
                <div class='section-title'>Referrer Information</div>
                <div class='field'><span class='label'>Name:</span> <span class='value'>" . $data['referrer_name'] . "</span></div>
                <div class='field'><span class='label'>Relationship:</span> <span class='value'>" . $data['referrer_relationship'] . "</span></div>
                <div class='field'><span class='label'>Phone:</span> <span class='value'>" . $data['referrer_phone'] . "</span></div>
                <div class='field'><span class='label'>Email:</span> <span class='value'>" . $data['referrer_email'] . "</span></div>
            </div>";
    }
    
    $email_body .= "
            <div class='section'>
                <div class='section-title'>Submission Details</div>
                <div class='field'><span class='label'>Submission Date:</span> <span class='value'>" . $data['submission_date'] . "</span></div>
                <div class='field'><span class='label'>IP Address:</span> <span class='value'>" . $data['ip_address'] . "</span></div>
            </div>
        </div>
        
        <div class='footer'>
            <p>This is an automated email from Logan Express Care Referral System</p>
            <p>Please do not reply to this email</p>
        </div>
    </body>
    </html>";

    // Email headers
    $headers = array(
        'MIME-Version: 1.0',
        'Content-type: text/html; charset=utf-8',
        'From: ' . FROM_NAME . ' <' . FROM_EMAIL . '>',
        'Reply-To: ' . FROM_EMAIL,
        'X-Mailer: PHP/' . phpversion()
    );

    // Send email to admin
    $mail_sent = mail(ADMIN_EMAIL, $email_subject, $email_body, implode("\r\n", $headers));

    // Send confirmation email to participant/referrer
    if (!empty($data['email_address']) || !empty($data['referrer_email'])) {
        $recipient_email = !empty($data['email_address']) ? $data['email_address'] : $data['referrer_email'];
        
        $confirmation_subject = "Referral Received - Logan Express Care";
        $confirmation_body = "
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
                <h2>Referral Received Successfully</h2>
            </div>
            
            <div class='content'>
                <p>Dear " . $data['first_name'] . " " . $data['last_name'] . ",</p>
                
                <p>Thank you for submitting a referral to Logan Express Care. We have received your information and our team will review it shortly.</p>
                
                <div class='highlight'>
                    <h3>Your Referral ID: #" . $referral_id . "</h3>
                    <p>Please keep this ID for your records</p>
                </div>
                
                <p><strong>What happens next?</strong></p>
                <ul>
                    <li>Our care team will review your referral within 24-48 hours</li>
                    <li>We will contact you to discuss your care needs and schedule an initial assessment</li>
                    <li>You can check your referral status anytime using your Referral ID</li>
                </ul>
                
                <p><strong>Contact Information:</strong></p>
                <ul>
                    <li>Phone: [Your Phone Number]</li>
                    <li>Email: " . ADMIN_EMAIL . "</li>
                    <li>Website: [Your Website]</li>
                </ul>
                
                <p>If you have any questions or need immediate assistance, please don't hesitate to contact us.</p>
                
                <p>Best regards,<br>
                <strong>Logan Express Care Team</strong></p>
            </div>
            
            <div class='footer'>
                <p>This is an automated confirmation email from Logan Express Care</p>
                <p>&copy; " . date('Y') . " Logan Express Care. All rights reserved.</p>
            </div>
        </body>
        </html>";
        
        mail($recipient_email, $confirmation_subject, $confirmation_body, implode("\r\n", $headers));
    }

    // Success response
    $response['success'] = true;
    $response['message'] = 'Thank you! Your referral has been submitted successfully. Reference ID: #' . $referral_id;
    $response['referral_id'] = $referral_id;

} catch (Exception $e) {
    // Error response
    $response['success'] = false;
    $response['message'] = $e->getMessage();
    
    // Log error (optional)
    error_log("Referral form error: " . $e->getMessage());
}

// Send JSON response
echo json_encode($response);
exit;