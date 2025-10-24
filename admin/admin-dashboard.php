<?php
// Start session before any output
session_start();

require_once 'includes/auth.php';

$auth = new Auth();
$auth->requireLogin();

$user = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <meta name="description" content="Logan Express Care - Admin Dashboard">
    <meta name="keywords" content="admin, dashboard, logan express care">
    <meta name="author" content="Logan Express Care">
    <!-- Page Title -->
    <title>Admin Dashboard - Logan Express Care</title>
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: var(--white-color);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .stat-icon.pending {
            background: #fff3cd;
            color: #856404;
        }
        
        .stat-icon.approved {
            background: #d4edda;
            color: #155724;
        }
        
        .stat-icon.rejected {
            background: #f8d7da;
            color: #721c24;
        }
        
        .stat-icon.total {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: var(--text-color);
            font-size: 14px;
            margin: 0;
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
        
        .table {
            margin: 0;
        }
        
        .table th {
            border-top: none;
            border-bottom: 2px solid var(--divider-color);
            color: var(--primary-color);
            font-weight: 600;
            padding: 15px;
        }
        
        .table td {
            padding: 15px;
            vertical-align: middle;
            border-top: 1px solid var(--divider-color);
        }
        
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
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
        
        .btn-action {
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            margin-right: 5px;
        }
        
        .btn-view {
            background: var(--accent-color);
            color: var(--white-color);
        }
        
        .btn-approve {
            background: #28a745;
            color: var(--white-color);
        }
        
        .btn-reject {
            background: #dc3545;
            color: var(--white-color);
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
            
            .stats-grid {
                grid-template-columns: 1fr;
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
                <a href="dashboard.php" class="nav-link active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="applications.php" class="nav-link">
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
                <h1 class="page-title">Dashboard</h1>
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
            <?php
            require_once 'config/database.php';
            $database = new Database();
            $db = $database->getConnection();
            
            // Get statistics
            $stats = [];
            
            // Total applications
            $query = "SELECT COUNT(*) as total FROM staff_onboarding";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $stats['total'] = $stmt->fetch()['total'];
            
            // Pending applications
            $query = "SELECT COUNT(*) as pending FROM staff_onboarding WHERE status = 'pending'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $stats['pending'] = $stmt->fetch()['pending'];
            
            // Approved applications
            $query = "SELECT COUNT(*) as approved FROM staff_onboarding WHERE status = 'approved'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $stats['approved'] = $stmt->fetch()['approved'];
            
            // Rejected applications
            $query = "SELECT COUNT(*) as rejected FROM staff_onboarding WHERE status = 'rejected'";
            $stmt = $db->prepare($query);
            $stmt->execute();
            $stats['rejected'] = $stmt->fetch()['rejected'];
            ?>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon total">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['total']; ?></div>
                    <p class="stat-label">Total Applications</p>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon pending">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['pending']; ?></div>
                    <p class="stat-label">Pending Review</p>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon approved">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['approved']; ?></div>
                    <p class="stat-label">Approved</p>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon rejected">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-number"><?php echo $stats['rejected']; ?></div>
                    <p class="stat-label">Rejected</p>
                </div>
            </div>
            
            <!-- Recent Applications -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Recent Applications</h3>
                </div>
                <div class="card-body">
                    <?php
                    // Get recent applications
                    $query = "SELECT id, first_name, last_name, email, position, employment_type, status, created_at 
                              FROM staff_onboarding 
                              ORDER BY created_at DESC 
                              LIMIT 10";
                    $stmt = $db->prepare($query);
                    $stmt->execute();
                    $applications = $stmt->fetchAll();
                    ?>
                    
                    <?php if (empty($applications)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No applications found.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Position</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applications as $app): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></td>
                                            <td><?php echo htmlspecialchars($app['email']); ?></td>
                                            <td><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $app['position']))); ?></td>
                                            <td><?php echo htmlspecialchars(ucfirst($app['employment_type'])); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $app['status']; ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($app['created_at'])); ?></td>
                                            <td>
                                                <a href="application-details.php?id=<?php echo $app['id']; ?>" class="btn-action btn-view">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <?php if ($app['status'] === 'pending'): ?>
                                                    <a href="application-details.php?id=<?php echo $app['id']; ?>&action=approve" class="btn-action btn-approve">
                                                        <i class="fas fa-check"></i> Approve
                                                    </a>
                                                    <a href="application-details.php?id=<?php echo $app['id']; ?>&action=reject" class="btn-action btn-reject">
                                                        <i class="fas fa-times"></i> Reject
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
        });
    </script>
</body>
</html>
