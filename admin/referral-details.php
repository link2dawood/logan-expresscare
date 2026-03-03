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

function friendly_label(string $value): string
{
    return ucwords(str_replace('_', ' ', $value));
}

function format_value(string $key, $value): string
{
    if ($value === null || $value === '') {
        return 'N/A';
    }

    $text = trim((string)$value);

    if ($key === 'submission_date') {
        $timestamp = strtotime($text);
        if ($timestamp !== false) {
            return date('d M Y, h:i A', $timestamp);
        }
    }

    if (in_array($key, ['consent_privacy', 'consent_contact', 'has_guardian', 'require_interpreter'], true)) {
        return strtolower($text) === 'yes' ? 'Yes' : 'No';
    }

    if (in_array($key, ['referral_source', 'gender', 'identify_aboriginal', 'primary_service', 'secondary_service', 'additional_service', 'status'], true)) {
        return friendly_label(strtolower($text));
    }

    return $text;
}

$sections = [
    'Participant Details' => [
        'first_name' => 'First Name',
        'last_name' => 'Last Name',
        'dob' => 'Date of Birth',
        'gender' => 'Gender',
        'home_address' => 'Home Address',
        'phone_number' => 'Phone',
        'email_address' => 'Email',
        'ndis_number' => 'NDIS Number',
        'has_guardian' => 'Has Guardian',
    ],
    'Cultural Information' => [
        'country_of_birth' => 'Country of Birth',
        'require_interpreter' => 'Requires Interpreter',
        'relevant_culture' => 'Cultural Considerations',
        'identify_aboriginal' => 'Aboriginal/Torres Strait Islander',
    ],
    'Service Requirements' => [
        'primary_service' => 'Primary Service',
        'hours_required' => 'Hours Required',
        'secondary_service' => 'Secondary Service',
        'additional_service' => 'Additional Service',
        'participant_conditions' => 'Conditions/Disability',
        'extra_information' => 'Extra Information',
        'special_assessments' => 'Special Assessments',
        'practitioner_notes' => 'Practitioner Notes',
    ],
    'Referrer Information' => [
        'referral_source' => 'Completed By',
        'referrer_name' => 'Referrer Name',
        'referrer_relationship' => 'Referrer Relationship',
        'referrer_phone' => 'Referrer Phone',
        'referrer_email' => 'Referrer Email',
    ],
    'Consent & Meta' => [
        'consent_privacy' => 'Privacy Consent',
        'consent_contact' => 'Contact Consent',
        'submission_date' => 'Submission Date',
        'ip_address' => 'IP Address',
        'status' => 'Status',
    ],
];
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
<body class="py-4" style="background:#f4f6fb;">
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h2 class="mb-1">Referral #<?php echo (int)$referral['id']; ?></h2>
            <div class="text-muted">Detailed referral summary</div>
        </div>
        <a href="referrals.php" class="btn btn-outline-secondary">&larr; Back to referrals</a>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Participant</div>
                    <div class="fw-semibold fs-5"><?php echo htmlspecialchars(trim(($referral['first_name'] ?? '') . ' ' . ($referral['last_name'] ?? '')) ?: 'N/A'); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Status</div>
                    <span class="badge rounded-pill text-bg-<?php echo (($referral['status'] ?? '') === 'approved') ? 'success' : 'secondary'; ?> px-3 py-2">
                        <?php echo htmlspecialchars(format_value('status', $referral['status'] ?? '')); ?>
                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">Submitted</div>
                    <div class="fw-semibold"><?php echo htmlspecialchars(format_value('submission_date', $referral['submission_date'] ?? '')); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <?php foreach ($sections as $sectionTitle => $fields): ?>
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h5 class="mb-0"><?php echo htmlspecialchars($sectionTitle); ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php foreach ($fields as $key => $label):
                                $formattedValue = format_value($key, $referral[$key] ?? '');
                            ?>
                                <div class="col-md-6">
                                    <div class="text-muted small mb-1"><?php echo htmlspecialchars($label); ?></div>
                                    <div class="fw-semibold"><?php echo nl2br(htmlspecialchars($formattedValue)); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
