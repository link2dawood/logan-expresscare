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
    <meta name="description" content="Logan Express Care - Applications Management">
    <meta name="keywords" content="admin, applications, logan express care">
    <meta name="author" content="Logan Express Care">
    <!-- Page Title -->
    <title>Applications Management - Logan Express Care</title>
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
        
        .filters-card {
            background: var(--white-color);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .filter-row {
            display: flex;
            gap: 20px;
            align-items: end;
            flex-wrap: wrap;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 5px;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid var(--divider-color);
            border-radius: 8px;
            font-size: 14px;
        }
        
        .filter-group select:focus,
        .filter-group input:focus {
            border-color: var(--accent-color);
            outline: none;
        }
        
        .btn-filter {
            background: var(--accent-color);
            color: var(--white-color);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-filter:hover {
            background: var(--primary-color);
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
            display: flex;
            justify-content: between;
            align-items: center;
        }
        
        .card-title {
            font-size: 20px;
            color: var(--primary-color);
            margin: 0;
            font-weight: 600;
        }
        
        .card-body {
            padding: 0;
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
            background: var(--secondary-color);
        }
        
        .table td {
            padding: 15px;
            vertical-align: middle;
            border-top: 1px solid var(--divider-color);
        }
        
        .table tbody tr:hover {
            background: var(--secondary-color);
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
            transition: all 0.3s ease;
        }
        
        .btn-view {
            background: var(--accent-color);
            color: var(--white-color);
        }
        
        .btn-view:hover {
            background: var(--primary-color);
            color: var(--white-color);
        }
        
        .btn-approve {
            background: #28a745;
            color: var(--white-color);
        }
        
        .btn-approve:hover {
            background: #1e7e34;
            color: var(--white-color);
        }
        
        .btn-reject {
            background: #dc3545;
            color: var(--white-color);
        }
        
        .btn-reject:hover {
            background: #c82333;
            color: var(--white-color);
        }
        
        .pagination {
            padding: 20px 30px;
            border-top: 1px solid var(--divider-color);
            background: var(--secondary-color);
        }
        
        .page-link {
            color: var(--primary-color);
            border-color: var(--divider-color);
        }
        
        .page-link:hover {
            background: var(--accent-color);
            border-color: var(--accent-color);
            color: var(--white-color);
        }
        
        .page-item.active .page-link {
            background: var(--accent-color);
            border-color: var(--accent-color);
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
            
            .filter-row {
                flex-direction: column;
            }
            
            .filter-group {
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
                <h1 class="page-title">Applications Management</h1>
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
            <!-- Filters -->
            <div class="filters-card">
                <form method="GET" id="filterForm">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label for="status">Status</label>
                            <select name="status" id="status">
                                <option value="">All Statuses</option>
                                <option value="pending" <?php echo (isset($_GET['status']) && $_GET['status'] === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                <option value="under_review" <?php echo (isset($_GET['status']) && $_GET['status'] === 'under_review') ? 'selected' : ''; ?>>Under Review</option>
                                <option value="approved" <?php echo (isset($_GET['status']) && $_GET['status'] === 'approved') ? 'selected' : ''; ?>>Approved</option>
                                <option value="rejected" <?php echo (isset($_GET['status']) && $_GET['status'] === 'rejected') ? 'selected' : ''; ?>>Rejected</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="position">Position</label>
                            <select name="position" id="position">
                                <option value="">All Positions</option>
                                <option value="disability_support_worker" <?php echo (isset($_GET['position']) && $_GET['position'] === 'disability_support_worker') ? 'selected' : ''; ?>>Disability Support Worker</option>
                                <option value="nurse" <?php echo (isset($_GET['position']) && $_GET['position'] === 'nurse') ? 'selected' : ''; ?>>Nurse</option>
                                <option value="other" <?php echo (isset($_GET['position']) && $_GET['position'] === 'other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="employment_type">Employment Type</label>
                            <select name="employment_type" id="employment_type">
                                <option value="">All Types</option>
                                <option value="permanent" <?php echo (isset($_GET['employment_type']) && $_GET['employment_type'] === 'permanent') ? 'selected' : ''; ?>>Permanent</option>
                                <option value="casual" <?php echo (isset($_GET['employment_type']) && $_GET['employment_type'] === 'casual') ? 'selected' : ''; ?>>Casual</option>
                                <option value="contractor" <?php echo (isset($_GET['employment_type']) && $_GET['employment_type'] === 'contractor') ? 'selected' : ''; ?>>Contractor</option>
                            </select>
                        </div>
                        
                        <div class="filter-group">
                            <label for="search">Search</label>
                            <input type="text" name="search" id="search" placeholder="Search by name or email..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        </div>
                        
                        <div class="filter-group">
                            <button type="submit" class="btn-filter">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Applications Table -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">Staff Applications</h3>
                </div>
                <div class="card-body">
                    <?php
                    require_once 'config/database.php';
                    $database = new Database();
                    $db = $database->getConnection();
                    
                    // Build query with filters
                    $whereConditions = [];
                    $params = [];
                    
                    if (!empty($_GET['status'])) {
                        $whereConditions[] = "status = :status";
                        $params[':status'] = $_GET['status'];
                    }
                    
                    if (!empty($_GET['position'])) {
                        $whereConditions[] = "position = :position";
                        $params[':position'] = $_GET['position'];
                    }
                    
                    if (!empty($_GET['employment_type'])) {
                        $whereConditions[] = "employment_type = :employment_type";
                        $params[':employment_type'] = $_GET['employment_type'];
                    }
                    
                    if (!empty($_GET['search'])) {
                        $whereConditions[] = "(first_name LIKE :search OR last_name LIKE :search OR email LIKE :search)";
                        $params[':search'] = '%' . $_GET['search'] . '%';
                    }
                    
                    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
                    
                    // Pagination
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $limit = 20;
                    $offset = ($page - 1) * $limit;
                    
                    // Get total count
                    $countQuery = "SELECT COUNT(*) as total FROM staff_onboarding $whereClause";
                    $countStmt = $db->prepare($countQuery);
                    foreach ($params as $key => $value) {
                        $countStmt->bindValue($key, $value);
                    }
                    $countStmt->execute();
                    $totalRecords = $countStmt->fetch()['total'];
                    $totalPages = ceil($totalRecords / $limit);
                    
                    // Get applications
                    $query = "SELECT id, first_name, last_name, email, phone, position, employment_type, status, created_at, reviewed_at
                              FROM staff_onboarding 
                              $whereClause
                              ORDER BY created_at DESC 
                              LIMIT :limit OFFSET :offset";
                    
                    $stmt = $db->prepare($query);
                    foreach ($params as $key => $value) {
                        $stmt->bindValue($key, $value);
                    }
                    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                    $stmt->execute();
                    $applications = $stmt->fetchAll();
                    ?>
                    
                    <?php if (empty($applications)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No applications found matching your criteria.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Position</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Applied</th>
                                        <th>Reviewed</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($applications as $app): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($app['email']); ?></td>
                                            <td><?php echo htmlspecialchars($app['phone']); ?></td>
                                            <td><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $app['position']))); ?></td>
                                            <td><?php echo htmlspecialchars(ucfirst($app['employment_type'])); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $app['status']; ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($app['created_at'])); ?></td>
                                            <td>
                                                <?php echo $app['reviewed_at'] ? date('M j, Y', strtotime($app['reviewed_at'])) : '-'; ?>
                                            </td>
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
                        
                        <?php if ($totalPages > 1): ?>
                            <div class="pagination">
                                <nav aria-label="Applications pagination">
                                    <ul class="pagination justify-content-center">
                                        <?php if ($page > 1): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Previous</a>
                                            </li>
                                        <?php endif; ?>
                                        
                                        <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"><?php echo $i; ?></a>
                                            </li>
                                        <?php endfor; ?>
                                        
                                        <?php if ($page < $totalPages): ?>
                                            <li class="page-item">
                                                <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next</a>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </nav>
                            </div>
                        <?php endif; ?>
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
