<?php
// Start session before any output
session_start();

// Process login BEFORE any HTML output
require_once 'includes/auth.php';

$auth = new Auth();
$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $result = $auth->login($username, $password);
        
            if ($result['success']) {
                header('Location: admin-dashboard.php');
                exit();
            } else {
            $error = $result['message'];
        }
    }
}

// NOW output HTML
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <meta name="description" content="Logan Express Care - Admin Panel">
    <meta name="keywords" content="admin, panel, logan express care">
    <meta name="author" content="Logan Express Care">
    <!-- Page Title -->
    <title>Admin Login - Logan Express Care</title>
    <!-- Favicon Icon -->
    <link rel="shortcut icon" type="image/x-icon" href="../images/favicon.png">
    <!-- Google Fonts Css-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">
    <!-- Bootstrap Css -->
    <link href="../css/bootstrap.min.css" rel="stylesheet" media="screen">
    <!-- Font Awesome Icon Css-->
    <link href="../css/all.min.css" rel="stylesheet" media="screen">
    <!-- Main Custom Css -->
    <link href="../css/custom.css" rel="stylesheet" media="screen">
    
    <style>
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: var(--default-font);
        }
        
        .login-container {
            background: var(--white-color);
            border-radius: 20px;
            padding: 60px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
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
            font-size: 32px;
            color: var(--primary-color);
            margin-bottom: 10px;
            font-family: var(--accent-font);
        }
        
        .login-header p {
            color: var(--text-color);
            margin-bottom: 0;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-control {
            height: 55px;
            border: 2px solid var(--divider-color);
            border-radius: 10px;
            padding: 0 20px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(226, 167, 111, 0.25);
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group .form-control {
            padding-left: 50px;
        }
        
        .input-group-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-color);
            font-size: 18px;
            z-index: 10;
        }
        
        .btn-login {
            width: 100%;
            height: 55px;
            background: var(--accent-color);
            border: none;
            border-radius: 10px;
            color: var(--white-color);
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(39, 58, 41, 0.3);
        }
        
        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
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
        
        .loading {
            display: none;
        }
        
        .loading.show {
            display: inline-block;
        }
        
        .back-to-site {
            text-align: center;
            margin-top: 30px;
        }
        
        .back-to-site a {
            color: var(--text-color);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }
        
        .back-to-site a:hover {
            color: var(--accent-color);
        }
        
        @media (max-width: 576px) {
            .login-container {
                padding: 40px 30px;
                margin: 10px;
            }
            
            .login-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <img src="../images/logoLEC-full2.png" alt="Logan Express Care" class="logo">
            <h1>Admin Panel</h1>
            <p>Sign in to access the admin dashboard</p>
        </div>
        
        <?php
        // Show error/success messages
        if ($error) {
            echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
        }
        if ($success) {
            echo '<div class="alert alert-success">' . htmlspecialchars($success) . '</div>';
        }
        ?>
        
        <form method="POST" id="loginForm">
            <div class="form-group">
                <div class="input-group">
                    <i class="fas fa-user input-group-icon"></i>
                    <input type="text" name="username" class="form-control" placeholder="Username" required value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <div class="input-group">
                    <i class="fas fa-lock input-group-icon"></i>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-login">
                <span class="btn-text">Sign In</span>
                <span class="loading">
                    <i class="fas fa-spinner fa-spin"></i> Signing in...
                </span>
            </button>
        </form>
        
        <div class="back-to-site">
            <a href="../index.php">
                <i class="fas fa-arrow-left"></i> Back to Main Site
            </a>
        </div>
    </div>
    
    <!-- Jquery Library File -->
    <script src="../js/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap js file -->
    <script src="../js/bootstrap.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#loginForm').on('submit', function() {
                const btn = $(this).find('.btn-login');
                const btnText = btn.find('.btn-text');
                const loading = btn.find('.loading');
                
                btn.prop('disabled', true);
                btnText.hide();
                loading.addClass('show');
            });
        });
    </script>
</body>
</html>
