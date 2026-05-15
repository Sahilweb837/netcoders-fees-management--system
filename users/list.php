<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();
checkRole(['root_admin', 'super_admin', 'admin']);

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Prevent self-deletion
    if ($id != $_SESSION['user_id']) {
        $conn->query("DELETE FROM users WHERE id = $id");
        flash("User deleted successfully!");
    } else {
        flash("You cannot delete yourself!", "danger");
    }
    redirect('users/list.php');
}

$search = $_GET['search'] ?? '';
$where = "WHERE 1=1";
if ($search) {
    $where .= " AND (name LIKE '%$search%' OR username LIKE '%$search%')";
}

$users = $conn->query("SELECT * FROM users $where ORDER BY id DESC");

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">System Users</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="add.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Add New User</a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
                <!-- Search Bar -->
                <form class="mb-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search by name or username..." value="<?php echo $search; ?>">
                        <div class="input-group-append">
                            <button class="btn btn-dark" type="submit"><i class="fas fa-search"></i> Search</button>
                        </div>
                    </div>
                </form>

                <table class="table table-hover table-striped shadow-sm">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>User Details</th>
                            <th>Credentials</th>
                            <th>Role & Status</th>
                            <th>Access Log</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $users->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="font-weight-bold"><?php echo $row['name']; ?></div>
                                <small class="text-muted">ID: #<?php echo $row['id']; ?></small>
                            </td>
                            <td>
                                <div><i class="fas fa-user-tag mr-1"></i> <?php echo $row['username']; ?></div>
                                <div class="text-danger small">
                                    <i class="fas fa-key mr-1"></i> Pass: <strong><?php echo $row['plain_password'] ?: '********'; ?></strong>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-primary px-2"><?php echo strtoupper(str_replace('_', ' ', $row['role'])); ?></span><br>
                                <?php echo $row['status'] == 1 ? '<span class="text-success small"><i class="fas fa-check-circle"></i> Active</span>' : '<span class="text-danger small"><i class="fas fa-times-circle"></i> Inactive</span>'; ?>
                            </td>
                            <td>
                                <div class="small"><i class="fas fa-clock mr-1"></i> <?php echo $row['last_login'] ? date('d M, h:i A', strtotime($row['last_login'])) : 'Never'; ?></div>
                                <div class="small text-muted"><i class="fas fa-network-wired mr-1"></i> <?php echo $row['last_ip'] ?: '0.0.0.0'; ?></div>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                    <?php if($row['id'] != $_SESSION['user_id']): ?>
                                    <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
