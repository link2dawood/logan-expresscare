<?php
// Start session before any output
session_start();

require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        try {
            // Check if email already exists
            $checkQuery = "SELECT id FROM staff_onboarding WHERE email = :email";
            $checkStmt = $db->prepare($checkQuery);
            $checkStmt->bindParam(':email', $email);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() > 0) {
                $error = 'An account with this email already exists.';
            } else {
                // Hash password
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user with pending status
                $insertQuery = "INSERT INTO staff_onboarding (first_name, last_name, email, phone, password, status, created_at) 
                               VALUES (:first_name, :last_name, :email, :phone, :password, 'pending', NOW())";
                
                $insertStmt = $db->prepare($insertQuery);
                $insertStmt->bindParam(':first_name', $firstName);
                $insertStmt->bindParam(':last_name', $lastName);
                $insertStmt->bindParam(':email', $email);
                $insertStmt->bindParam(':phone', $phone);
                $insertStmt->bindParam(':password', $hashedPassword);
                
                if ($insertStmt->execute()) {
                    $userId = $db->lastInsertId();
                    
                    // Set session for user
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_name'] = $firstName . ' ' . $lastName;
                    $_SESSION['user_signed_up'] = true;
                    $_SESSION['user_type'] = 'staff';
                    
                    $success = 'Account created successfully! You can now complete your onboarding.';
                } else {
                    $error = 'Failed to create account. Please try again.';
                }
            }
        } catch (Exception $e) {
            error_log("Signup error: " . $e->getMessage());
            $error = 'An error occurred. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <title>Staff Signup - Logan Express Care</title>
    <link rel="shortcut icon" type="image/x-icon" href="../images/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/all.min.css" rel="stylesheet">
    <link href="../css/custom.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #273a29 0%, #e2a76f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        
        .signup-container {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            max-width: 600px;
            width: 100%;
            margin: 20px;
        }
        
        .signup-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .signup-header .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
        
        .signup-header h1 {
            font-size: 28px;
            color: #273a29;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .signup-header p {
            color: #666;
            margin-bottom: 0;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #273a29;
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            height: 50px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0 20px;
            font-size: 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #e2a76f;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(226, 167, 111, 0.25);
        }
        
        .btn-signup {
            width: 100%;
            height: 55px;
            background: #e2a76f;
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-signup:hover {
            background: #273a29;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(39, 58, 41, 0.3);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-link a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover {
            color: #e2a76f;
        }
        
        @media (max-width: 576px) {
            .signup-container {
                padding: 30px 20px;
            }
            
            .signup-header h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-header">
            <img src="../images/logoLEC-full2.png" alt="Logan Express Care" class="logo">
            <h1>Staff Signup</h1>
            <p>Create your account to begin the onboarding process</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
            <div class="login-link">
                <a href="/admin/index.php" class="btn-signup" style="display: inline-block; padding: 15px 40px; height: auto; width: auto;">
                    <i class="fas fa-arrow-right"></i> Go to Dashboard
                </a>
            </div>
        <?php else: ?>
            <form method="POST" id="signupForm">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter your first name" required value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter your last name" required value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="your.email@example.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter your phone number" required value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
                </div>
                
                <button type="submit" class="btn-signup">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>
            
            <div class="login-link">
                <a href="/admin/user-login.php">
                    <i class="fas fa-sign-in-alt"></i> Already have an account? Login
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <script src="../js/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#signupForm').on('submit', function(e) {
                const firstName = $('#first_name').val().trim();
                const lastName = $('#last_name').val().trim();
                const email = $('#email').val().trim();
                const phone = $('#phone').val().trim();
                
                if (!firstName || !lastName || !email || !phone) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                    return false;
                }
                
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    e.preventDefault();
                    alert('Please enter a valid email address.');
                    return false;
                }
            });
        });
    </script>
</body>
</html>
