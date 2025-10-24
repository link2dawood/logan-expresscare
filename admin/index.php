<?php
// Start session before any output
session_start();

require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Check if user is logged in
$userEmail = $_SESSION['user_email'] ?? '';
$userName = $_SESSION['user_name'] ?? '';
$userSignedUp = $_SESSION['user_signed_up'] ?? false;

// If not logged in, redirect to login
if (!$userSignedUp || !$userEmail) {
    header('Location: /admin/user-login.php');
    exit();
}

// Get user data and status
$userData = null;
$userStatus = 'pending';

try {
    $query = "SELECT * FROM staff_onboarding WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $userEmail);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $userData = $stmt->fetch();
        $userStatus = $userData['status'];
    }
} catch (Exception $e) {
    error_log("Get user data error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <title>User Dashboard - Logan Express Care</title>
    <link rel="shortcut icon" type="image/x-icon" href="../images/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/all.min.css" rel="stylesheet">
    <link href="../css/custom.css" rel="stylesheet">
    <link href="../css/animate.css" rel="stylesheet">
    
    <style>
        body {
            background: var(--secondary-color);
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }
        
        .dashboard-header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .dashboard-header h1 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .dashboard-header p {
            color: var(--text-color);
            margin: 0;
        }
        
        .status-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .status-header {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .status-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        
        .status-pending .status-icon {
            background: #ffc107;
        }
        
        .status-submitted .status-icon {
            background: #17a2b8;
        }
        
        .status-approved .status-icon {
            background: #28a745;
        }
        
        .status-rejected .status-icon {
            background: #dc3545;
        }
        
        .status-info h3 {
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .status-info p {
            color: var(--text-color);
            margin: 0;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending .status-badge {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-submitted .status-badge {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-approved .status-badge {
            background: #d4edda;
            color: #155724;
        }
        
        .status-rejected .status-badge {
            background: #f8d7da;
            color: #721c24;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        
        .btn-primary {
            background: var(--accent-color);
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 58, 41, 0.3);
        }
        
        .btn-secondary {
            background: var(--secondary-color);
            border: 2px solid var(--divider-color);
            padding: 12px 30px;
            border-radius: 8px;
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: var(--divider-color);
            color: var(--primary-color);
        }
        
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background: #c82333;
            color: white;
        }
        
        .progress-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .progress-steps {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }
        
        .progress-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            flex: 1;
            position: relative;
        }
        
        .progress-step::after {
            content: '';
            position: absolute;
            top: 25px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: var(--divider-color);
            z-index: -1;
        }
        
        .progress-step:last-child::after {
            display: none;
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--secondary-color);
            color: var(--text-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            transition: all 0.3s ease;
        }
        
        .progress-step.completed .step-number {
            background: var(--accent-color);
            color: white;
        }
        
        .progress-step.active .step-number {
            background: #ffc107;
            color: white;
            animation: pulse 2s infinite;
            box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
        }
        
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
            }
        }
        
        .step-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-color);
            text-align: center;
        }
        
        .progress-step.completed .step-label {
            color: var(--primary-color);
        }
        
        .progress-step.active .step-label {
            color: #ffc107;
            font-weight: 700;
        }
        
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 20px 10px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .progress-steps {
                flex-direction: column;
                gap: 20px;
            }
            
            .progress-step::after {
                display: none;
            }
        }
    </style>
</head>
<body>
     <a href="/admin/logout.php" class="logout-btn">
         <i class="fas fa-sign-out-alt"></i> Logout
     </a>
    
    <div class="dashboard-container">
        <!-- Header -->
        <div class="dashboard-header">
            <h1>Welcome, <?php echo htmlspecialchars($userName); ?>!</h1>
            <p>Track your onboarding progress and status</p>
        </div>
        
        <!-- Status Card -->
        <div class="status-card status-<?php echo $userStatus; ?>">
            <div class="status-header">
                <div class="status-icon">
                    <?php if ($userStatus === 'pending'): ?>
                        <i class="fas fa-clock"></i>
                    <?php elseif ($userStatus === 'submitted'): ?>
                        <i class="fas fa-paper-plane"></i>
                    <?php elseif ($userStatus === 'approved'): ?>
                        <i class="fas fa-check-circle"></i>
                    <?php elseif ($userStatus === 'rejected'): ?>
                        <i class="fas fa-times-circle"></i>
                    <?php endif; ?>
                </div>
                <div class="status-info">
                    <h3>Application Status</h3>
                    <p>Current Status: <span class="status-badge"><?php echo ucfirst($userStatus); ?></span></p>
                </div>
            </div>
            
            <?php if ($userStatus === 'pending'): ?>
                <p>Your account has been created. Please complete your onboarding form to proceed.</p>
                <div class="action-buttons">
                     <a href="/admin/user-onboarding.php" class="btn-primary">
                         <i class="fas fa-file-alt"></i> Complete Onboarding
                     </a>
                </div>
            <?php elseif ($userStatus === 'submitted'): ?>
                <p>Your onboarding form has been submitted and is under review. Our HR team will contact you soon with updates.</p>
                <div class="action-buttons">
                    <a href="../contact.php" class="btn-primary">
                        <i class="fas fa-envelope"></i> Contact HR Team
                    </a>
                </div>
            <?php elseif ($userStatus === 'approved'): ?>
                <p>Congratulations! Your application has been approved. Welcome to the Logan Express Care team!</p>
                <div class="action-buttons">
                    <a href="/admin/success.php" class="btn-primary">
                        <i class="fas fa-check-circle"></i> View Success Page
                    </a>
                </div>
            <?php elseif ($userStatus === 'rejected'): ?>
                <p>Unfortunately, your application was not successful at this time. Please contact our HR team for more information.</p>
                <div class="action-buttons">
                    <a href="../contact.php" class="btn-primary">
                        <i class="fas fa-envelope"></i> Contact HR Team
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Progress Section -->
        <div class="progress-section">
            <h3>Onboarding Progress</h3>
            <div class="progress-steps">
                <div class="progress-step completed">
                    <div class="step-number">1</div>
                    <div class="step-label">Account Created</div>
                </div>
                <div class="progress-step <?php echo in_array($userStatus, ['submitted', 'under_review', 'approved', 'rejected']) ? 'completed' : ($userStatus === 'pending' ? 'active' : ''); ?>">
                    <div class="step-number">2</div>
                    <div class="step-label">Form Submitted</div>
                </div>
                <div class="progress-step <?php echo in_array($userStatus, ['approved', 'rejected']) ? 'completed' : (in_array($userStatus, ['submitted', 'under_review']) ? 'active' : ''); ?>">
                    <div class="step-number">3</div>
                    <div class="step-label">Under Review</div>
                </div>
                <div class="progress-step <?php echo $userStatus === 'approved' ? 'completed active' : ($userStatus === 'rejected' ? 'active' : ''); ?>">
                    <div class="step-number">4</div>
                    <div class="step-label"><?php echo $userStatus === 'rejected' ? 'Decision' : 'Approved'; ?></div>
                </div>
            </div>
        </div>
        
        <!-- User Information -->
        <?php if ($userData): ?>
        <div class="status-card">
            <h3>Your Information</h3>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($userData['email']); ?></p>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($userData['phone']); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Position:</strong> <?php echo htmlspecialchars($userData['position'] ?? 'Not specified'); ?></p>
                    <p><strong>Employment Type:</strong> <?php echo htmlspecialchars($userData['employment_type'] ?? 'Not specified'); ?></p>
                    <p><strong>Created:</strong> <?php echo date('M d, Y', strtotime($userData['created_at'])); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <script src="../js/jquery-3.7.1.min.js"></script>
    <script src="../js/wow.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize WOW.js for animations
            new WOW().init();
        });
    </script>
</body>
</html>
