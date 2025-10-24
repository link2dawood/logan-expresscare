<?php
// Simple authentication - In production, use proper authentication system
session_start();

// Basic password protection
$admin_password = "admin123"; // CHANGE THIS IN PRODUCTION

if (!isset($_SESSION['admin_logged_in'])) {
    if (isset($_POST['admin_password'])) {
        if ($_POST['admin_password'] === $admin_password) {
            $_SESSION['admin_logged_in'] = true;
        } else {
            $error = "Invalid password";
        }
    }
    
    if (!isset($_SESSION['admin_logged_in'])) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Admin Login - Staff Applications</title>
            <link href="css/bootstrap.min.css" rel="stylesheet">
            <link href="css/custom.css" rel="stylesheet">
        </head>
        <body>
            <div class="container" style="margin-top: 100px;">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body" style="padding: 40px;">
                                <h2 style="text-align: center; margin-bottom: 30px;">Admin Login</h2>
                                <?php if (isset($error)): ?>
                                    <div class="alert alert-danger"><?php echo $error; ?></div>
                                <?php endif; ?>
                                <form method="POST">
                                    <div class="form-group mb-4">
                                        <input type="password" name="admin_password" class="form-control" placeholder="Enter Admin Password" required>
                                    </div>
                                    <button type="submit" class="btn-default" style="width: 100%;">Login</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Fetch applications from Laravel API
$api_url = "http://127.0.0.1:8000/api/v1/staff-onboarding?per_page=100";
$applications = array();
$api_applications = array();

try {
    // Initialize cURL
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json'
    ));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    // Execute request
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200) {
        $result = json_decode($response, true);
        if ($result && isset($result['success']) && $result['success'] === true && isset($result['data']['data'])) {
            $api_applications = $result['data']['data'];
            
            // Transform API data to match CSV format for compatibility
            foreach ($api_applications as $app) {
                $applications[] = array(
                    'Submission Date' => $app['created_at'],
                    'First Name' => $app['first_name'],
                    'Last Name' => $app['last_name'],
                    'Email' => $app['email'],
                    'Phone' => $app['phone'],
                    'Position' => $app['position'],
                    'Employment Type' => $app['employment_type'],
                    'NDIS Screening' => $app['ndis_screening'],
                    'Children Check' => $app['children_check'],
                    'Children Check Expiry' => $app['children_check_expiry'],
                    'First Aid Expiry' => $app['first_aid_expiry'],
                    'CPR Expiry' => $app['cpr_expiry'],
                    'Police Check Expiry' => $app['police_check_expiry'],
                    'Status' => $app['status'] ?? 'pending',
                    'ID' => $app['id']
                );
            }
        }
    }
} catch (Exception $e) {
    // If API fails, try to read from CSV as fallback
    $csv_file = 'uploads/staff_applications.csv';
    if (file_exists($csv_file)) {
        if (($handle = fopen($csv_file, "r")) !== FALSE) {
            $headers = fgetcsv($handle);
            while (($data = fgetcsv($handle)) !== FALSE) {
                $applications[] = array_combine($headers, $data);
            }
            fclose($handle);
        }
    }
    // Reverse array to show newest first for CSV data
    $applications = array_reverse($applications);
}

include 'layouts/header.php';
?>

<style>
    .admin-dashboard {
        padding: 80px 0;
        min-height: 100vh;
    }
    
    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
        padding-bottom: 20px;
        border-bottom: 2px solid var(--divider-color);
    }
    
    .admin-header h1 {
        margin: 0;
    }
    
    .logout-btn {
        background: #dc3545;
        padding: 10px 20px;
        color: white;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 600;
    }
    
    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }
    
    .stat-card {
        background: var(--secondary-color);
        padding: 30px;
        border-radius: 15px;
        text-align: center;
    }
    
    .stat-card h3 {
        font-size: 40px;
        color: var(--accent-color);
        margin-bottom: 10px;
    }
    
    .stat-card p {
        margin: 0;
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .applications-table {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 5px 30px rgba(0,0,0,0.08);
        overflow-x: auto;
    }
    
    .applications-table h2 {
        margin-bottom: 20px;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    th {
        background: var(--primary-color);
        color: white;
        padding: 15px;
        text-align: left;
        font-weight: 600;
    }
    
    td {
        padding: 15px;
        border-bottom: 1px solid var(--divider-color);
    }
    
    tr:hover {
        background: var(--secondary-color);
    }
    
    .badge {
        display: inline-block;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .badge-permanent {
        background: #28a745;
        color: white;
    }
    
    .badge-casual {
        background: #17a2b8;
        color: white;
    }
    
    .badge-contractor {
        background: #ffc107;
        color: #000;
    }
    
    .view-btn {
        background: var(--accent-color);
        color: white;
        padding: 8px 15px;
        border-radius: 5px;
        text-decoration: none;
        font-size: 14px;
        display: inline-block;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-color);
    }
    
    .empty-state i {
        font-size: 80px;
        color: var(--accent-color);
        margin-bottom: 20px;
    }
    
    .filter-section {
        margin-bottom: 30px;
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    
    .filter-section input,
    .filter-section select {
        padding: 10px 15px;
        border: 1px solid var(--divider-color);
        border-radius: 5px;
        min-width: 200px;
    }
</style>

<!-- Admin Dashboard Start -->
<div class="admin-dashboard">
    <div class="container">
        <div class="admin-header">
            <div>
                <h1>Staff Applications Dashboard</h1>
                <p>Manage and review staff onboarding applications</p>
            </div>
            <div>
                <a href="?logout=1" class="logout-btn">Logout</a>
            </div>
        </div>

        <?php if (empty($api_applications)): ?>
        <div style="background: #fff3cd; border: 1px solid #ffc107; border-radius: 10px; padding: 15px 20px; margin-bottom: 30px;">
            <div style="display: flex; align-items: center; gap: 15px;">
                <i class="fas fa-exclamation-triangle" style="color: #856404; font-size: 24px;"></i>
                <div>
                    <strong style="color: #856404;">API Connection Unavailable</strong>
                    <p style="margin: 5px 0 0 0; color: #856404;">
                        Unable to connect to the backend API. Displaying data from CSV backup. 
                        Make sure your Laravel backend is running at <code>http://127.0.0.1:8000</code>
                    </p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="stats-cards">
            <div class="stat-card">
                <h3><?php echo count($applications); ?></h3>
                <p>Total Applications</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count(array_filter($applications, function($app) { 
                    return $app['Employment Type'] === 'permanent'; 
                })); ?></h3>
                <p>Permanent Positions</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count(array_filter($applications, function($app) { 
                    return $app['Employment Type'] === 'casual'; 
                })); ?></h3>
                <p>Casual Positions</p>
            </div>
            <div class="stat-card">
                <h3><?php echo count(array_filter($applications, function($app) { 
                    return $app['Employment Type'] === 'contractor'; 
                })); ?></h3>
                <p>Contractors</p>
            </div>
        </div>

        <!-- Applications Table -->
        <div class="applications-table">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0;">Recent Applications</h2>
                <div style="display: flex; align-items: center; gap: 15px;">
                    <span id="dataSource" style="font-size: 14px; color: #666;">
                        <?php if (!empty($api_applications)): ?>
                            <i class="fas fa-check-circle" style="color: #28a745;"></i> Connected to API
                        <?php else: ?>
                            <i class="fas fa-exclamation-triangle" style="color: #ffc107;"></i> Using CSV Fallback
                        <?php endif; ?>
                    </span>
                    <button onclick="location.reload()" style="padding: 8px 15px; background: var(--accent-color); color: white; border: none; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
            </div>
            
            <?php if (empty($applications)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No Applications Yet</h3>
                    <p>Staff onboarding applications will appear here once submitted.</p>
                </div>
            <?php else: ?>
                <!-- Filter Section -->
                <div class="filter-section">
                    <input type="text" id="searchInput" placeholder="Search by name or email...">
                    <select id="positionFilter">
                        <option value="">All Positions</option>
                        <option value="disability_support_worker">Disability Support Worker</option>
                        <option value="nurse">Nurse</option>
                        <option value="other">Other</option>
                    </select>
                    <select id="employmentTypeFilter">
                        <option value="">All Employment Types</option>
                        <option value="permanent">Permanent</option>
                        <option value="casual">Casual</option>
                        <option value="contractor">Contractor</option>
                    </select>
                </div>

                <table id="applicationsTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Position</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Documents</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $index => $app): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($app['Submission Date'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($app['First Name'] . ' ' . $app['Last Name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($app['Email']); ?></td>
                            <td><?php echo htmlspecialchars($app['Phone']); ?></td>
                            <td><?php echo ucwords(str_replace('_', ' ', $app['Position'])); ?></td>
                            <td>
                                <?php 
                                $type = $app['Employment Type'];
                                $badge_class = 'badge-' . $type;
                                echo '<span class="badge ' . $badge_class . '">' . ucfirst($type) . '</span>';
                                ?>
                            </td>
                            <td>
                                <?php 
                                $status = isset($app['Status']) ? $app['Status'] : 'pending';
                                $status_colors = [
                                    'pending' => '#ffc107',
                                    'under_review' => '#17a2b8',
                                    'approved' => '#28a745',
                                    'rejected' => '#dc3545',
                                    'completed' => '#6c757d'
                                ];
                                $color = $status_colors[$status] ?? '#6c757d';
                                echo '<span class="badge" style="background: ' . $color . '; color: white;">' . ucwords(str_replace('_', ' ', $status)) . '</span>';
                                ?>
                            </td>
                            <td>
                                <small>
                                    <i class="fas fa-check-circle" style="color: green;"></i> 
                                    <?php echo $app['NDIS Screening'] ? 'Complete' : 'Incomplete'; ?>
                                </small>
                            </td>
                            <td>
                                <button onclick="viewApplication(<?php echo $index; ?>)" class="view-btn">
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <!-- Application Details Modal (Simple Version) -->
        <div id="applicationModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 9999; overflow-y: auto;">
            <div style="max-width: 900px; margin: 50px auto; background: white; border-radius: 15px; padding: 40px; position: relative;">
                <button onclick="closeModal()" style="position: absolute; top: 20px; right: 20px; background: none; border: none; font-size: 30px; cursor: pointer;">&times;</button>
                <div id="modalContent"></div>
            </div>
        </div>
    </div>
</div>
<!-- Admin Dashboard End -->

<script>
    // Store applications data for JavaScript
    const applications = <?php echo json_encode($applications); ?>;

    // View application details
    function viewApplication(index) {
        const app = applications[index];
        const status = app['Status'] || 'pending';
        const statusColors = {
            'pending': '#ffc107',
            'under_review': '#17a2b8',
            'approved': '#28a745',
            'rejected': '#dc3545',
            'completed': '#6c757d'
        };
        const statusColor = statusColors[status] || '#6c757d';
        
        let html = `
            <h2>Application Details</h2>
            ${app['ID'] ? `<p style="color: #666;">Application ID: ${app['ID']}</p>` : ''}
            <hr>
            
            <h3>Personal Information</h3>
            <table style="width: 100%; margin-bottom: 30px;">
                <tr><td><strong>Name:</strong></td><td>${app['First Name']} ${app['Last Name']}</td></tr>
                <tr><td><strong>Email:</strong></td><td>${app['Email']}</td></tr>
                <tr><td><strong>Phone:</strong></td><td>${app['Phone']}</td></tr>
                <tr><td><strong>Position:</strong></td><td>${app['Position']}</td></tr>
                <tr><td><strong>Employment Type:</strong></td><td>${app['Employment Type']}</td></tr>
                <tr><td><strong>Status:</strong></td><td><span style="background: ${statusColor}; color: white; padding: 5px 15px; border-radius: 20px; font-size: 12px;">${status.replace('_', ' ').toUpperCase()}</span></td></tr>
                <tr><td><strong>Submission Date:</strong></td><td>${app['Submission Date']}</td></tr>
            </table>
            
            <h3>Document Status</h3>
            <table style="width: 100%; margin-bottom: 30px;">
                <tr><td><strong>NDIS Screening:</strong></td><td>${app['NDIS Screening'] || 'Not uploaded'}</td></tr>
                <tr><td><strong>Children Check:</strong></td><td>${app['Children Check'] || 'Not uploaded'}</td></tr>
                <tr><td><strong>Children Check Expiry:</strong></td><td>${app['Children Check Expiry'] || 'N/A'}</td></tr>
                <tr><td><strong>Police Check Expiry:</strong></td><td>${app['Police Check Expiry'] || 'N/A'}</td></tr>
                <tr><td><strong>First Aid Expiry:</strong></td><td>${app['First Aid Expiry'] || 'N/A'}</td></tr>
                <tr><td><strong>CPR Expiry:</strong></td><td>${app['CPR Expiry'] || 'N/A'}</td></tr>
            </table>
            
            <h3>File Location</h3>
            ${app['ID'] ? 
                `<p>Files are stored in the backend server at: <code>storage/app/public/staff-onboarding/</code></p>
                 <p>Files can be accessed via the Laravel API or downloaded from the admin dashboard.</p>` 
                : 
                `<p>All uploaded files are stored in: <code>uploads/staff-onboarding/</code></p>`
            }
            
            <div style="margin-top: 30px; text-align: center;">
                <button onclick="closeModal()" class="btn-default">Close</button>
            </div>
        `;
        
        document.getElementById('modalContent').innerHTML = html;
        document.getElementById('applicationModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('applicationModal').style.display = 'none';
    }

    // Filter functionality
    document.getElementById('searchInput')?.addEventListener('input', filterTable);
    document.getElementById('positionFilter')?.addEventListener('change', filterTable);
    document.getElementById('employmentTypeFilter')?.addEventListener('change', filterTable);

    function filterTable() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const positionFilter = document.getElementById('positionFilter').value;
        const employmentTypeFilter = document.getElementById('employmentTypeFilter').value;
        
        const table = document.getElementById('applicationsTable');
        const rows = table.getElementsByTagName('tr');
        
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const name = row.cells[1].textContent.toLowerCase();
            const email = row.cells[2].textContent.toLowerCase();
            const position = applications[i-1]['Position'];
            const employmentType = applications[i-1]['Employment Type'];
            
            let showRow = true;
            
            // Search filter
            if (searchTerm && !name.includes(searchTerm) && !email.includes(searchTerm)) {
                showRow = false;
            }
            
            // Position filter
            if (positionFilter && position !== positionFilter) {
                showRow = false;
            }
            
            // Employment type filter
            if (employmentTypeFilter && employmentType !== employmentTypeFilter) {
                showRow = false;
            }
            
            row.style.display = showRow ? '' : 'none';
        }
    }

    // Close modal on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
</script>

<?php
// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin-applications.php');
    exit;
}

include 'layouts/footer.php';
?>


