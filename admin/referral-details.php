<?php
session_start();

require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: referrals.php');
    exit;
}

$database = new Database();
$db = $database->getConnection();

$stmt = $db->prepare('SELECT * FROM referrals WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$referral = $stmt->fetch();

if (!$referral) {
    header('Location: referrals.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Referral #<?php echo (int)$referral['id']; ?> - Logan Express Care</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/custom.css" rel="stylesheet">
</head>
<body class="p-4" style="background:#f8f9fa;">
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Referral #<?php echo (int)$referral['id']; ?></h2>
        <a href="referrals.php" class="btn btn-secondary">Back to referrals</a>
    </div>
    <div class="card"><div class="card-body">
        <div class="row g-3">
            <?php
            $labels = [
                'referral_source' => 'Completed By', 'first_name' => 'First Name', 'last_name' => 'Last Name', 'dob' => 'Date of Birth',
                'gender' => 'Gender', 'home_address' => 'Home Address', 'phone_number' => 'Phone', 'email_address' => 'Email',
                'ndis_number' => 'NDIS Number', 'has_guardian' => 'Has Guardian', 'country_of_birth' => 'Country of Birth',
                'require_interpreter' => 'Requires Interpreter', 'relevant_culture' => 'Cultural Considerations',
                'identify_aboriginal' => 'Aboriginal/Torres Strait Islander', 'primary_service' => 'Primary Service',
                'hours_required' => 'Hours Required', 'secondary_service' => 'Secondary Service', 'additional_service' => 'Additional Service',
                'participant_conditions' => 'Conditions/Disability', 'extra_information' => 'Extra Information',
                'special_assessments' => 'Special Assessments', 'practitioner_notes' => 'Practitioner Notes',
                'referrer_name' => 'Referrer Name', 'referrer_relationship' => 'Referrer Relationship', 'referrer_phone' => 'Referrer Phone',
                'referrer_email' => 'Referrer Email', 'consent_privacy' => 'Privacy Consent', 'consent_contact' => 'Contact Consent',
                'submission_date' => 'Submission Date', 'ip_address' => 'IP Address', 'status' => 'Status'
            ];
            foreach ($labels as $key => $label):
                $value = isset($referral[$key]) && $referral[$key] !== '' ? $referral[$key] : 'N/A';
            ?>
                <div class="col-md-6">
                    <div><strong><?php echo htmlspecialchars($label); ?>:</strong></div>
                    <div><?php echo nl2br(htmlspecialchars((string)$value)); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div></div>
</div>
</body>
</html>
