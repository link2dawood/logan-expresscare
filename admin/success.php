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

// Get user data
$userData = null;
try {
    $query = "SELECT * FROM staff_onboarding WHERE email = :email";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':email', $userEmail);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $userData = $stmt->fetch();
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
    <title>Application Submitted - Logan Express Care</title>
    <link rel="shortcut icon" type="image/x-icon" href="../images/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/all.min.css" rel="stylesheet">
    <link href="../css/custom.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding-top: 60px;
        }
        
        /* Navigation Bar */
        .top-nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 15px 30px;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .nav-buttons {
            display: flex;
            gap: 10px;
        }
        
        .btn-back, .btn-logout {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-back {
            background: var(--accent-color);
            color: white;
        }
        
        .btn-back:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 58, 41, 0.3);
        }
        
        .btn-logout {
            background: #dc3545;
            color: white;
        }
        
        .btn-logout:hover {
            background: #c82333;
            color: white;
            transform: translateY(-2px);
        }
        
        .success-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }
        
        .success-card {
            background: white;
            border-radius: 20px;
            padding: 60px 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .success-icon {
            margin-bottom: 30px;
        }
        
        .checkmark-circle {
            width: 120px;
            height: 120px;
            position: relative;
            display: inline-block;
            vertical-align: top;
        }
        
        .checkmark-background {
            width: 120px;
            height: 120px;
            background: var(--accent-color);
            border-radius: 50%;
            position: absolute;
            animation: scaleIn 0.5s ease-in-out;
        }
        
        .checkmark {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: block;
            stroke-width: 3;
            stroke: white;
            stroke-miterlimit: 10;
            box-shadow: inset 0px 0px 0px var(--accent-color);
            animation: fill 0.4s ease-in-out 0.4s forwards, scale 0.3s ease-in-out 0.9s both;
        }
        
        .checkmark-circle-svg {
            stroke-dasharray: 166;
            stroke-dashoffset: 166;
            stroke-width: 3;
            stroke-miterlimit: 10;
            stroke: white;
            fill: none;
            animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
        }
        
        .checkmark-check {
            transform-origin: 50% 50%;
            stroke-dasharray: 48;
            stroke-dashoffset: 48;
            animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
        }
        
        @keyframes stroke {
            100% { stroke-dashoffset: 0; }
        }
        
        @keyframes scaleIn {
            0% { transform: scale(0); }
            100% { transform: scale(1); }
        }
        
        @keyframes fill {
            100% { box-shadow: inset 0px 0px 0px 60px var(--accent-color); }
        }
        
        .success-message h1 {
            color: var(--primary-color);
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .success-message p {
            color: var(--text-color);
            font-size: 18px;
            margin-bottom: 40px;
        }
        
        .next-steps {
            background: var(--secondary-color);
            border-radius: 15px;
            padding: 30px;
            margin-top: 40px;
            text-align: left;
        }
        
        .next-steps h3 {
            color: var(--primary-color);
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .timeline-item {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid var(--divider-color);
        }
        
        .timeline-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .timeline-icon {
            width: 60px;
            height: 60px;
            background: var(--accent-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            flex-shrink: 0;
        }
        
        .timeline-content h4 {
            color: var(--primary-color);
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .timeline-content p {
            color: var(--text-color);
            font-size: 14px;
            margin: 0;
        }
        
        .info-boxes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .info-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
        }
        
        .info-box i {
            font-size: 32px;
            color: var(--accent-color);
            margin-bottom: 10px;
        }
        
        .info-box h4 {
            color: var(--primary-color);
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .info-box p {
            color: var(--text-color);
            font-size: 14px;
            margin: 0;
        }
        
        .action-button {
            display: inline-block;
            background: var(--accent-color);
            color: white;
            padding: 15px 40px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            margin-top: 30px;
            transition: all 0.3s ease;
        }
        
        .action-button:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(39, 58, 41, 0.3);
            color: white;
        }
        
        @media (max-width: 768px) {
            .success-card {
                padding: 40px 20px;
            }
            
            .success-message h1 {
                font-size: 24px;
            }
            
            .top-nav {
                padding: 10px 15px;
            }
            
            .btn-back, .btn-logout {
                padding: 8px 15px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <div class="top-nav">
        <div class="logo">
            <img src="../images/logoLEC-full2.png" alt="Logan Express Care" style="height: 40px;">
        </div>
        <div class="nav-buttons">
            <a href="/admin/index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <a href="/admin/logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
    
    <div class="success-container">
        <div class="success-card">
            <!-- Success Icon -->
            <div class="success-icon">
                <div class="checkmark-circle">
                    <div class="checkmark-background"></div>
                    <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                        <circle class="checkmark-circle-svg" cx="26" cy="26" r="25" fill="none"/>
                        <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>
            </div>

            <!-- Success Message -->
            <div class="success-message">
                <h1>Application Successfully Submitted!</h1>
                <p class="lead">Thank you, <?php echo htmlspecialchars($userName); ?>, for completing your staff onboarding application.</p>
            </div>

            <!-- Next Steps -->
            <div class="next-steps">
                <h3><i class="fas fa-clipboard-list"></i> What Happens Next?</h3>
                <div class="timeline-items">
                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                        <div class="timeline-content">
                            <h4>Application Review</h4>
                            <p>Our HR team will review your application and documents within 2-3 business days.</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div class="timeline-content">
                            <h4>Contact & Interview</h4>
                            <p>If your application meets our requirements, we'll contact you to schedule an interview.</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="timeline-content">
                            <h4>Verification Process</h4>
                            <p>We'll verify all your documents and certifications with relevant authorities.</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <div class="timeline-content">
                            <h4>Welcome Onboard</h4>
                            <p>Once approved, you'll receive your official welcome package and start date.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Information Boxes -->
            <div class="info-boxes">
                <div class="info-box">
                    <i class="fas fa-clock"></i>
                    <h4>Review Timeline</h4>
                    <p>2-3 business days</p>
                </div>
                
                <div class="info-box">
                    <i class="fas fa-envelope"></i>
                    <h4>Email Updates</h4>
                    <p>Check your inbox regularly</p>
                </div>
                
                <div class="info-box">
                    <i class="fas fa-phone"></i>
                    <h4>Questions?</h4>
                    <p>Contact our HR team</p>
                </div>
            </div>

            <!-- Action Button -->
            <a href="/admin/index.php" class="action-button">
                <i class="fas fa-tachometer-alt"></i> Go to Dashboard
            </a>
        </div>
    </div>
    
    <script src="../js/jquery-3.7.1.min.js"></script>
</body>
</html>
