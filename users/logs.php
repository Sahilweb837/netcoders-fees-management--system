<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();
checkRole(['root_admin', 'super_admin']);

$logs = $conn->query("SELECT l.*, u.name as user_name FROM activity_logs l LEFT JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC LIMIT 100");

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">System Activity Logs</h1>
                <p class="text-muted">Tracking all administrative and user actions.</p>
            </div>
            <div class="col-sm-6 text-right">
                <a href="list.php" class="btn btn-outline-primary btn-sm"><i class="fas fa-users"></i> Back to Users</a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-dark">
            <div class="card-body p-0">
                <table class="table table-striped table-valign-middle">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>IP Address</th>
                            <th>Device / Agent</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $logs->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <i class="far fa-clock text-muted"></i> <?php echo date('d M Y, h:i:s A', strtotime($row['created_at'])); ?>
                            </td>
                            <td>
                                <span class="badge badge-info"><?php echo $row['user_name'] ?: 'System/Guest'; ?></span>
                            </td>
                            <td>
                                <strong class="text-primary"><?php echo $row['action']; ?></strong>
                            </td>
                            <td>
                                <code><?php echo $row['ip_address']; ?></code>
                            </td>
                            <td>
                                <small class="text-muted" title="<?php echo $row['user_agent']; ?>">
                                    <?php echo substr($row['user_agent'], 0, 50) . '...'; ?>
                                </small>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
