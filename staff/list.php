<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM staff WHERE id = $id");
    flash("Staff deleted successfully!");
    redirect('staff/list.php');
}

$staff = $conn->query("SELECT * FROM staff ORDER BY id DESC");

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Staff Management</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Staff</a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Phone</th>
                            <th>Salary</th>
                            <th>Joining Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $staff->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if($row['photo']): ?>
                                    <img src="../uploads/<?php echo $row['photo']; ?>" width="50" height="50" class="img-circle">
                                <?php else: ?>
                                    <i class="fas fa-user-tie fa-3x text-muted"></i>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['role']; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                            <td><?php echo CURRENCY . number_format($row['salary'], 2); ?></td>
                            <td><?php echo date('d M Y', strtotime($row['joining_date'])); ?></td>
                            <td>
                                <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-edit"></i></a>
                                <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
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
