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
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            // Check if user exists
            $query = "SELECT id, first_name, last_name, email, password, status FROM staff_onboarding WHERE email = :email";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Set session for user
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['user_signed_up'] = true;
                    $_SESSION['user_type'] = 'staff';
                    
                    // Redirect to user dashboard
                    header('Location: /admin/index.php');
                    exit();
                } else {
                    $error = 'Invalid email or password.';
                }
            } else {
                $error = 'No account found with this email address. Please sign up first.';
            }
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
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
    <title>Staff Login - Logan Express Care</title>
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
        
        .login-container {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: § 20px 60px rgba(0, 0, 0, 0.15);
            max-width: 500px;
            width: 100%;
            margin: 20px;
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .login-header .logo {
            max-width: 200px;
            margin-bottom: 20px;
        }
        
        .login-header h1 {
            font-size: 28px;
            color: #273a29;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .login-header p {
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
        
        .btn-login {
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
        
        .btn-login:hover {
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
        
        .signup-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .signup-link a {
            color: #666;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }
        
        .signup-link a:hover {
            color: #e2a76f;
        }
        
        @media (max-width: 576px) {
            .login-container {
                padding: 30px 20px;
            }
            
            .login-header h1 {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="../images/logoLEC-full2.png" alt="Logan Express Care" class="logo">
            <h1>Staff Login</h1>
            <p>Enter your email to access your onboarding form</p>
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
        <?php endif; ?>
        
        <form method="POST" id="loginForm">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="your.email@example.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
            </div>
            
            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>
        
        <div class="signup-link">
            <a href="/admin/user-signup.php">
                <i class="fas fa-user-plus"></i> Don't have an account? Sign up
            </a>
        </div>
    </div>
    
    <script src="../js/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#loginForm').on('submit', function(e) {
                const email = $('#email').val().trim();
                
                if (!email) {
                    e.preventDefault();
                    alert('Please enter your email address.');
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
