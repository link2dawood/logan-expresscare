<?php
session_start();

require_once 'includes/auth.php';
require_once 'config/database.php';

$auth = new Auth();
$auth->requireLogin();
$user = $auth->getCurrentUser();

$database = new Database();
$db = $database->getConnection();

$status = isset($_GET['status']) ? trim($_GET['status']) : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = [];
$params = [];

if ($status !== 'all' && $status !== '') {
    $where[] = 'status = :status';
    $params[':status'] = $status;
}

if ($search !== '') {
    $where[] = '(first_name LIKE :search OR last_name LIKE :search OR email_address LIKE :search OR phone_number LIKE :search)';
    $params[':search'] = '%' . $search . '%';
}

$whereSql = '';
if (!empty($where)) {
    $whereSql = 'WHERE ' . implode(' AND ', $where);
}

$query = "SELECT id, first_name, last_name, phone_number, email_address, primary_service, status, submission_date, created_at
          FROM referrals
          $whereSql
          ORDER BY created_at DESC
          LIMIT 300";
$stmt = $db->prepare($query);
$stmt->execute($params);
$referrals = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">
    <title>Referrals - Logan Express Care</title>
    <link rel="shortcut icon" type="image/x-icon" href="../images/favicon.png">
    <link href="../css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="../css/all.min.css" rel="stylesheet" media="screen">
    <link href="../css/custom.css" rel="stylesheet" media="screen">
    <style>
        .admin-content{padding:30px}.card{border:none;border-radius:12px;box-shadow:0 4px 16px rgba(0,0,0,.08)}
        .badge.pending{background:#ffc107}.badge.under_review{background:#17a2b8}.badge.approved{background:#28a745}.badge.rejected{background:#dc3545}
        .admin-sidebar{position:fixed;left:0;top:0;height:100vh;width:280px;background:var(--primary-color);z-index:1000;overflow-y:auto}
        .admin-main{margin-left:280px;min-height:100vh;background:var(--secondary-color)}
        .sidebar-header{padding:30px 20px;border-bottom:1px solid rgba(255,255,255,.1)}
        .sidebar-logo{max-width:100%;filter:brightness(0) invert(1)} .sidebar-nav{padding:20px 0}
        .nav-link{display:flex;align-items:center;padding:15px 25px;color:rgba(255,255,255,.85);text-decoration:none;border-left:3px solid transparent}
        .nav-link.active,.nav-link:hover{background:rgba(255,255,255,.1);color:#fff;border-left-color:var(--accent-color)}
        .nav-link i{width:20px;margin-right:15px}.admin-header{background:#fff;padding:20px 30px;box-shadow:0 2px 10px rgba(0,0,0,.08)}
        .page-title{font-size:28px;color:var(--primary-color);margin:0;font-family:var(--accent-font)}
    </style>
</head>
<body>
<div class="admin-sidebar">
    <div class="sidebar-header"><img src="../images/logoLEC-full-light.png" alt="Logan Express Care" class="sidebar-logo"></div>
    <nav class="sidebar-nav">
        <a href="admin-dashboard.php" class="nav-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
        <a href="applications.php" class="nav-link"><i class="fas fa-file-alt"></i><span>Applications</span></a>
        <a href="referrals.php" class="nav-link active"><i class="fas fa-user-check"></i><span>Referrals</span></a>
        <a href="users.php" class="nav-link"><i class="fas fa-users"></i><span>Users</span></a>
        <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </nav>
</div>
<div class="admin-main">
    <div class="admin-header"><h1 class="page-title">Referrals</h1></div>
    <div class="admin-content">
        <div class="card mb-4"><div class="card-body">
            <form method="get" class="row g-3">
                <div class="col-md-3"><label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="all" <?php echo $status==='all'?'selected':''; ?>>All</option>
                        <option value="pending" <?php echo $status==='pending'?'selected':''; ?>>Pending</option>
                        <option value="under_review" <?php echo $status==='under_review'?'selected':''; ?>>Under Review</option>
                        <option value="approved" <?php echo $status==='approved'?'selected':''; ?>>Approved</option>
                        <option value="rejected" <?php echo $status==='rejected'?'selected':''; ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-md-5"><label class="form-label">Search</label><input class="form-control" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Name, email, phone"></div>
                <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100" type="submit">Filter</button></div>
            </form>
        </div></div>

        <div class="card"><div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead><tr><th>#</th><th>Name</th><th>Contact</th><th>Primary Service</th><th>Status</th><th>Submitted</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php if (empty($referrals)): ?>
                        <tr><td colspan="7" class="text-center py-4">No referrals found.</td></tr>
                    <?php else: foreach ($referrals as $r): ?>
                        <tr>
                            <td>#<?php echo (int)$r['id']; ?></td>
                            <td><?php echo htmlspecialchars($r['first_name'] . ' ' . $r['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($r['phone_number']); ?><br><small><?php echo htmlspecialchars($r['email_address'] ?: 'N/A'); ?></small></td>
                            <td><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', (string)$r['primary_service']))); ?></td>
                            <td><span class="badge <?php echo htmlspecialchars($r['status']); ?>"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', (string)$r['status']))); ?></span></td>
                            <td><?php echo htmlspecialchars(date('d M Y, h:i A', strtotime((string)$r['submission_date']))); ?></td>
                            <td><a class="btn btn-sm btn-outline-primary" href="referral-details.php?id=<?php echo (int)$r['id']; ?>">View</a></td>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div></div>
    </div>
</div>
</body>
</html>
