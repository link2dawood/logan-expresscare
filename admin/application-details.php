<?php
// Start session before any output
session_start();

require_once 'includes/auth.php';

$auth = new Auth();
$auth->requireLogin();

$user = $auth->getCurrentUser();

// Get application ID
$applicationId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$applicationId) {
    header('Location: applications.php');
    exit();
}

require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();

// Handle actions (approve/reject)
if (isset($_POST['action']) && in_array($_POST['action'], ['approve', 'reject', 'request_more_info'])) {
    $action = $_POST['action'];
    $notes = trim($_POST['notes'] ?? '');
    
    $status = '';
    switch ($action) {
        case 'approve':
            $status = 'approved';
            break;
        case 'reject':
            $status = 'rejected';
            break;
        case 'request_more_info':
            $status = 'incomplete';
            break;
    }
    
    try {
        // Update application status
        $query = "UPDATE staff_applications 
                  SET status = :status, reviewed_by = :reviewed_by, reviewed_at = NOW(), notes = :notes 
                  WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':reviewed_by', $user['id']);
        $stmt->bindParam(':notes', $notes);
        $stmt->bindParam(':id', $applicationId);
        
        if ($stmt->execute()) {
            // Add note to application_notes table
            if (!empty($notes)) {
                $noteQuery = "INSERT INTO application_notes (application_id, admin_user_id, note_text) 
                              VALUES (:app_id, :admin_id, :note_text)";
                $noteStmt = $db->prepare($noteQuery);
                $noteStmt->bindParam(':app_id', $applicationId);
                $noteStmt->bindParam(':admin_id', $user['id']);
                $noteStmt->bindParam(':note_text', $notes);
                $noteStmt->execute();
            }
            
            // Send email notification
            require_once 'includes/email-notification.php';
            $emailNotification = new EmailNotification();
            
            // Get user details for email
            $userQuery = "SELECT first_name, last_name, email FROM staff_applications WHERE id = :id";
            $userStmt = $db->prepare($userQuery);
            $userStmt->bindParam(':id', $applicationId);
            $userStmt->execute();
            $userDetails = $userStmt->fetch();
            
            if ($userDetails) {
                $userName = $userDetails['first_name'] . ' ' . $userDetails['last_name'];
                $emailNotification->sendStatusNotification(
                    $userDetails['email'], 
                    $userName, 
                    $status, 
                    $notes
                );
            }
            
            $successMessage = "Application " . ucfirst($action) . "d successfully! Email notification sent.";
        } else {
            $errorMessage = "Failed to update application status.";
        }
    } catch (Exception $e) {
        error_log("Application update error: " . $e->getMessage());
        $errorMessage = "An error occurred while updating the application.";
    }
}

// Get application details
try {
    $query = "SELECT sa.*, au.full_name as reviewed_by_name 
              FROM staff_applications sa 
              LEFT JOIN admin_users au ON sa.reviewed_by = au.id 
              WHERE sa.id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $applicationId);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        header('Location: applications.php');
        exit();
    }
    
    $application = $stmt->fetch();
    
    // Get application notes
    $notesQuery = "SELECT an.*, au.full_name as admin_name 
                   FROM application_notes an 
                   LEFT JOIN admin_users au ON an.admin_user_id = au.id 
                   WHERE an.application_id = :id 
                   ORDER BY an.created_at DESC";
    $notesStmt = $db->prepare($notesQuery);
    $notesStmt->bindParam(':id', $applicationId);
    $notesStmt->execute();
    $notes = $notesStmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Application fetch error: " . $e->getMessage());
    $errorMessage = "Failed to load application details.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <meta name="description" content="Logan Express Care - Application Details">
    <meta name="keywords" content="admin, application, details, logan express care">
    <meta name="author" content="Logan Express Care">
    <!-- Page Title -->
    <title>Application Details - Logan Express Care</title>
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
        .admin-sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: 280px;
            background: var(--primary-color);
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        
        .admin-sidebar.collapsed {
            width: 80px;
        }
        
        .sidebar-header {
            padding: 30px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-logo {
            max-width: 100%;
            height: auto;
            filter: brightness(0) invert(1);
        }
        
        .sidebar-nav {
            padding: 20px 0;
        }
        
        .nav-item {
            margin-bottom: 5px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: var(--white-color);
            border-left-color: var(--accent-color);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 15px;
            font-size: 18px;
        }
        
        .nav-text {
            transition: opacity 0.3s ease;
        }
        
        .admin-sidebar.collapsed .nav-text {
            opacity: 0;
        }
        
        .admin-main {
            margin-left: 280px;
            min-height: 100vh;
            background: var(--secondary-color);
            transition: all 0.3s ease;
        }
        
        .admin-main.sidebar-collapsed {
            margin-left: 80px;
        }
        
        .admin-header {
            background: var(--white-color);
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 20px;
            color: var(--text-color);
            cursor: pointer;
            padding: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .sidebar-toggle:hover {
            background: var(--secondary-color);
        }
        
        .page-title {
            font-size: 28px;
            color: var(--primary-color);
            margin: 0;
            font-family: var(--accent-font);
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-left: auto;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-color);
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--accent-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white-color);
            font-weight: 600;
        }
        
        .logout-btn {
            background: var(--accent-color);
            color: var(--white-color);
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background: var(--primary-color);
            color: var(--white-color);
        }
        
        .admin-content {
            padding: 30px;
        }
        
        .back-btn {
            background: var(--secondary-color);
            color: var(--primary-color);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            margin-bottom: 20px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: var(--accent-color);
            color: var(--white-color);
        }
        
        .application-header {
            background: var(--white-color);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            margin-bottom: 30px;
        }
        
        .application-title {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .applicant-name {
            font-size: 32px;
            color: var(--primary-color);
            margin: 0;
            font-family: var(--accent-font);
        }
        
        .status-badge {
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-approved {
            background: #d4edda;
            color: #155724;
        }
        
        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-under-review {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-incomplete {
            background: #f8d7da;
            color: #721c24;
        }
        
        .application-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        
        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .meta-label {
            font-size: 12px;
            color: var(--text-color);
            text-transform: uppercase;
            font-weight: 600;
        }
        
        .meta-value {
            font-size: 16px;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .sidebar-content {
            display: flex;
            flex-direction: column;
            gap: 30px;
        }
        
        .content-card {
            background: var(--white-color);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        .card-header {
            padding: 25px 30px;
            border-bottom: 1px solid var(--divider-color);
            background: var(--secondary-color);
        }
        
        .card-title {
            font-size: 20px;
            color: var(--primary-color);
            margin: 0;
            font-weight: 600;
        }
        
        .card-body {
            padding: 30px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .info-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .info-label {
            font-size: 14px;
            color: var(--text-color);
            font-weight: 600;
        }
        
        .info-value {
            font-size: 16px;
            color: var(--primary-color);
        }
        
        .document-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .document-item {
            display: flex;
            justify-content: between;
            align-items: center;
            padding: 15px;
            background: var(--secondary-color);
            border-radius: 10px;
        }
        
        .document-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .document-icon {
            width: 40px;
            height: 40px;
            background: var(--accent-color);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white-color);
        }
        
        .document-details h6 {
            margin: 0;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .document-details small {
            color: var(--text-color);
        }
        
        .document-actions {
            display: flex;
            gap: 10px;
        }
        
        .btn-download {
            background: var(--accent-color);
            color: var(--white-color);
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 12px;
            transition: all 0.3s ease;
        }
        
        .btn-download:hover {
            background: var(--primary-color);
            color: var(--white-color);
        }
        
        .action-form {
            background: var(--white-color);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--divider-color);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--accent-color);
            outline: none;
        }
        
        .btn-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .btn-action {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            min-width: 120px;
        }
        
        .btn-approve {
            background: #28a745;
            color: var(--white-color);
        }
        
        .btn-approve:hover {
            background: #1e7e34;
        }
        
        .btn-reject {
            background: #dc3545;
            color: var(--white-color);
        }
        
        .btn-reject:hover {
            background: #c82333;
        }
        
        .btn-more-info {
            background: #ffc107;
            color: var(--primary-color);
        }
        
        .btn-more-info:hover {
            background: #e0a800;
        }
        
        .notes-list {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .note-item {
            background: var(--secondary-color);
            border-radius: 10px;
            padding: 20px;
        }
        
        .note-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .note-author {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .note-date {
            font-size: 12px;
            color: var(--text-color);
        }
        
        .note-content {
            color: var(--text-color);
            line-height: 1.6;
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
            }
            
            .admin-sidebar.show {
                transform: translateX(0);
            }
            
            .admin-main {
                margin-left: 0;
            }
            
            .admin-header {
                padding: 15px 20px;
            }
            
            .page-title {
                font-size: 20px;
            }
            
            .admin-content {
                padding: 20px;
            }
            
            .content-grid {
                grid-template-columns: 1fr;
            }
            
            .application-title {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .btn-action {
                min-width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Admin Sidebar -->
    <div class="admin-sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="../images/logoLEC-full-light.png" alt="Logan Express Care" class="sidebar-logo">
        </div>
        
        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="dashboard.php" class="nav-link">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="applications.php" class="nav-link active">
                    <i class="fas fa-file-alt"></i>
                    <span class="nav-text">Applications</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="users.php" class="nav-link">
                    <i class="fas fa-users"></i>
                    <span class="nav-text">Users</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="reports.php" class="nav-link">
                    <i class="fas fa-chart-bar"></i>
                    <span class="nav-text">Reports</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="settings.php" class="nav-link">
                    <i class="fas fa-cog"></i>
                    <span class="nav-text">Settings</span>
                </a>
            </div>
        </nav>
    </div>
    
    <!-- Main Content -->
    <div class="admin-main" id="mainContent">
        <!-- Header -->
        <div class="admin-header">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">Application Details</h1>
            </div>
            
            <div class="header-right">
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['name'], 0, 1)); ?>
                    </div>
                    <span><?php echo htmlspecialchars($user['name']); ?></span>
                </div>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
        
        <!-- Content -->
        <div class="admin-content">
            <a href="applications.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Applications
            </a>
            
            <?php if (isset($successMessage)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($errorMessage)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>
            
            <!-- Application Header -->
            <div class="application-header">
                <div class="application-title">
                    <h1 class="applicant-name">
                        <?php echo htmlspecialchars($application['first_name'] . ' ' . $application['last_name']); ?>
                    </h1>
                    <span class="status-badge status-<?php echo $application['status']; ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $application['status'])); ?>
                    </span>
                </div>
                
                <div class="application-meta">
                    <div class="meta-item">
                        <span class="meta-label">Email</span>
                        <span class="meta-value"><?php echo htmlspecialchars($application['email']); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Phone</span>
                        <span class="meta-value"><?php echo htmlspecialchars($application['phone']); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Position</span>
                        <span class="meta-value"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $application['position']))); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Employment Type</span>
                        <span class="meta-value"><?php echo htmlspecialchars(ucfirst($application['employment_type'])); ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Applied</span>
                        <span class="meta-value"><?php echo date('M j, Y g:i A', strtotime($application['created_at'])); ?></span>
                    </div>
                    <?php if ($application['reviewed_at']): ?>
                    <div class="meta-item">
                        <span class="meta-label">Reviewed</span>
                        <span class="meta-value"><?php echo date('M j, Y g:i A', strtotime($application['reviewed_at'])); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($application['reviewed_by_name']): ?>
                    <div class="meta-item">
                        <span class="meta-label">Reviewed By</span>
                        <span class="meta-value"><?php echo htmlspecialchars($application['reviewed_by_name']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="content-grid">
                <!-- Main Content -->
                <div class="main-content">
                    <!-- Personal Information -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">Personal Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">First Name</span>
                                    <span class="info-value"><?php echo htmlspecialchars($application['first_name']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Last Name</span>
                                    <span class="info-value"><?php echo htmlspecialchars($application['last_name']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Email</span>
                                    <span class="info-value"><?php echo htmlspecialchars($application['email']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Phone</span>
                                    <span class="info-value"><?php echo htmlspecialchars($application['phone']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Position</span>
                                    <span class="info-value"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $application['position']))); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Employment Type</span>
                                    <span class="info-value"><?php echo htmlspecialchars(ucfirst($application['employment_type'])); ?></span>
                                </div>
                                <?php if ($application['employment_type'] === 'contractor'): ?>
                                <div class="info-item">
                                    <span class="info-label">ABN</span>
                                    <span class="info-value"><?php echo htmlspecialchars($application['abn'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Business Name</span>
                                    <span class="info-value"><?php echo htmlspecialchars($application['business_name'] ?? 'N/A'); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Documents -->
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">Uploaded Documents</h3>
                        </div>
                        <div class="card-body">
                            <div class="document-list">
                                <?php
                                $documents = [
                                    'NDIS Worker Screening Check' => $application['ndis_screening'],
                                    'Working with Children Check' => $application['children_check'],
                                    'Passport' => $application['passport'],
                                    'Visa' => $application['visa'],
                                    'Driver\'s License' => $application['drivers_license'],
                                    'NDIS Worker Orientation Certificate' => $application['ndis_orientation'],
                                    'Meals Certificate' => $application['meals_certificate'],
                                    'IPC Certificate' => $application['ipc_certificate'],
                                    'First Aid Certificate' => $application['first_aid_certificate'],
                                    'CPR Certificate' => $application['cpr_certificate'],
                                    'Resume' => $application['resume'],
                                    'Staff Contract' => $application['staff_contract'],
                                    'Police Check' => $application['police_check']
                                ];
                                
                                foreach ($documents as $name => $filename):
                                    if ($filename):
                                ?>
                                <div class="document-item">
                                    <div class="document-info">
                                        <div class="document-icon">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div class="document-details">
                                            <h6><?php echo $name; ?></h6>
                                            <small><?php echo htmlspecialchars($filename); ?></small>
                                        </div>
                                    </div>
                                    <div class="document-actions">
                                        <a href="../uploads/staff-onboarding/<?php echo htmlspecialchars($filename); ?>" 
                                           target="_blank" class="btn-download">
                                            <i class="fas fa-download"></i> View
                                        </a>
                                    </div>
                                </div>
                                <?php
                                    endif;
                                endforeach;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar Content -->
                <div class="sidebar-content">
                    <!-- Actions -->
                    <?php if ($application['status'] === 'pending'): ?>
                    <div class="action-form">
                        <h4 style="color: var(--primary-color); margin-bottom: 20px;">Review Application</h4>
                        <form method="POST">
                            <div class="form-group">
                                <label for="notes">Review Notes</label>
                                <textarea name="notes" id="notes" class="form-control" rows="4" 
                                          placeholder="Add your review notes here..."></textarea>
                            </div>
                            
                            <div class="btn-group">
                                <button type="submit" name="action" value="approve" class="btn-action btn-approve">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button type="submit" name="action" value="reject" class="btn-action btn-reject">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                                <button type="submit" name="action" value="request_more_info" class="btn-action btn-more-info">
                                    <i class="fas fa-info-circle"></i> More Info
                                </button>
                            </div>
                        </form>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Notes -->
                    <?php if (!empty($notes)): ?>
                    <div class="content-card">
                        <div class="card-header">
                            <h3 class="card-title">Review Notes</h3>
                        </div>
                        <div class="card-body">
                            <div class="notes-list">
                                <?php foreach ($notes as $note): ?>
                                <div class="note-item">
                                    <div class="note-header">
                                        <span class="note-author"><?php echo htmlspecialchars($note['admin_name']); ?></span>
                                        <span class="note-date"><?php echo date('M j, Y g:i A', strtotime($note['created_at'])); ?></span>
                                    </div>
                                    <div class="note-content">
                                        <?php echo nl2br(htmlspecialchars($note['note_text'])); ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Jquery Library File -->
    <script src="../js/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap js file -->
    <script src="../js/bootstrap.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Sidebar toggle
            $('#sidebarToggle').on('click', function() {
                $('#sidebar').toggleClass('collapsed');
                $('#mainContent').toggleClass('sidebar-collapsed');
            });
            
            // Mobile sidebar toggle
            if ($(window).width() <= 768) {
                $('#sidebar').addClass('collapsed');
                $('#mainContent').addClass('sidebar-collapsed');
            }
            
            $(window).on('resize', function() {
                if ($(window).width() <= 768) {
                    $('#sidebar').addClass('collapsed');
                    $('#mainContent').addClass('sidebar-collapsed');
                } else {
                    $('#sidebar').removeClass('collapsed');
                    $('#mainContent').removeClass('sidebar-collapsed');
                }
            });
            
            // Action form confirmation
            $('button[name="action"]').on('click', function(e) {
                const action = $(this).val();
                const actionText = $(this).text().trim();
                
                if (!confirm(`Are you sure you want to ${actionText.toLowerCase()} this application?`)) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
