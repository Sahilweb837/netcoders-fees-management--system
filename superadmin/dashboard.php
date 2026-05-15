<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

checkLogin();
$user = getLoggedInUser($conn);

// Stats Calculation
$total_students = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
$total_fees = $conn->query("SELECT SUM(amount) as total FROM fees WHERE status = 'paid'")->fetch_assoc()['total'];
$total_branches = $conn->query("SELECT COUNT(*) as count FROM branches")->fetch_assoc()['count'];
$pending_fees = $conn->query("SELECT SUM(amount) as total FROM fees WHERE status = 'pending' OR status = 'unpaid'")->fetch_assoc()['total'];

// Recent Activities
$activities = $conn->query("SELECT a.*, u.full_name FROM activity_logs a JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Netcoder IT ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 0; min-height: 100vh; background: #f4f7fe; }
    </style>
</head>
<body>

    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>

        <div id="content">
            <?php include '../includes/header.php'; ?>

            <div class="container-fluid px-4">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800 fw-bold">IT Institute Dashboard</h1>
                    <a href="#" class="btn btn-sm btn-primary shadow-sm" style="background: #e67e22; border: none;">
                        <i class="fas fa-download fa-sm text-white-50 me-1"></i> Export Report
                    </a>
                </div>

                <!-- Stats Row -->
                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card bg-primary text-white h-100 shadow border-0">
                            <div class="d-flex justify-content-between align-items-center p-3">
                                <div>
                                    <div class="text-white-50 small fw-bold text-uppercase mb-1">Total Interns</div>
                                    <div class="h3 mb-0 fw-bold"><?php echo number_format($total_students); ?></div>
                                </div>
                                <div class="icon h2 opacity-50"><i class="fas fa-user-graduate"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card bg-success text-white h-100 shadow border-0">
                            <div class="d-flex justify-content-between align-items-center p-3">
                                <div>
                                    <div class="text-white-50 small fw-bold text-uppercase mb-1">Total Collection</div>
                                    <div class="h3 mb-0 fw-bold"><?php echo formatCurrency($total_fees); ?></div>
                                </div>
                                <div class="icon h2 opacity-50"><i class="fas fa-hand-holding-usd"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card bg-info text-white h-100 shadow border-0">
                            <div class="d-flex justify-content-between align-items-center p-3">
                                <div>
                                    <div class="text-white-50 small fw-bold text-uppercase mb-1">Branches</div>
                                    <div class="h3 mb-0 fw-bold"><?php echo number_format($total_branches); ?></div>
                                </div>
                                <div class="icon h2 opacity-50"><i class="fas fa-network-wired"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card stat-card bg-warning text-white h-100 shadow border-0">
                            <div class="d-flex justify-content-between align-items-center p-3">
                                <div>
                                    <div class="text-white-50 small fw-bold text-uppercase mb-1">Pending Fees</div>
                                    <div class="h3 mb-0 fw-bold"><?php echo formatCurrency($pending_fees); ?></div>
                                </div>
                                <div class="icon h2 opacity-50"><i class="fas fa-clock"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h6 class="m-0 font-weight-bold text-primary">Revenue Trend</h6>
                            </div>
                            <div class="card-body">
                                <canvas id="revenueChart" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm mb-4">
                            <div class="card-header bg-white py-3 border-0">
                                <h6 class="m-0 font-weight-bold text-primary">Recent Logs</h6>
                            </div>
                            <div class="card-body">
                                <?php while($log = $activities->fetch_assoc()): ?>
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="p-2 bg-light rounded text-primary"><i class="fas fa-info-circle"></i></div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="small fw-bold"><?php echo $log['action']; ?></div>
                                        <div class="text-muted small"><?php echo $log['full_name']; ?> • <?php echo time_elapsed_string($log['created_at']); ?></div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // Placeholder for Chart.js
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Fees Collection',
                    data: [12000, 19000, 3000, 5000, 2000, 3000],
                    borderColor: '#f39c12',
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(243, 156, 18, 0.1)'
                }]
            }
        });
    </script>
</body>
</html>
