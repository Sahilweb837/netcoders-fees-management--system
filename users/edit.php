<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();
checkRole(['root_admin', 'super_admin', 'admin']);

$id = (int)$_GET['id'];
$user_res = $conn->query("SELECT * FROM users WHERE id = $id");
if ($user_res->num_rows == 0) {
    flash("User not found!", "danger");
    redirect('users/list.php');
}
$user = $user_res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $username = $conn->real_escape_string($_POST['username']);
    $role = $_POST['role'];
    $status = (int)$_POST['status'];
    $branch_id = !empty($_POST['branch_id']) ? (int)$_POST['branch_id'] : "NULL";

    $update_query = "UPDATE users SET name = '$name', username = '$username', role = '$role', status = $status, branch_id = $branch_id";
    
    // Update password if provided
    if (!empty($_POST['password'])) {
        $plain_pass = $_POST['password'];
        $hashed_pass = password_hash($plain_pass, PASSWORD_BCRYPT);
        $update_query .= ", password = '$hashed_pass', plain_password = '$plain_pass'";
    }
    
    $update_query .= " WHERE id = $id";

    if ($conn->query($update_query)) {
        logActivity("Updated user profile: $username");
        flash("User updated successfully!");
        redirect('users/list.php');
    } else {
        $error = "Error updating user. Username might already exist.";
    }
}

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit User Profile</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-orange card-outline">
                    <form action="edit.php?id=<?php echo $id; ?>" method="POST">
                        <div class="card-body">
                            <?php if(isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?php echo $user['name']; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" value="<?php echo $user['username']; ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label>New Password (Leave blank to keep current)</label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control" id="passwordField">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePass()"><i class="fas fa-eye"></i></button>
                                    </div>
                                </div>
                                <small class="text-muted">Current Plain Pass: <strong><?php echo $user['plain_password'] ?: 'N/A'; ?></strong></small>
                            </div>

                            <div class="form-group">
                                <label>Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="staff" <?php echo $user['role'] == 'staff' ? 'selected' : ''; ?>>Staff</option>
                                    <option value="accountant" <?php echo $user['role'] == 'accountant' ? 'selected' : ''; ?>>Accountant</option>
                                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    <option value="super_admin" <?php echo $user['role'] == 'super_admin' ? 'selected' : ''; ?>>Super Admin</option>
                                    <option value="root_admin" <?php echo $user['role'] == 'root_admin' ? 'selected' : ''; ?>>Root Admin</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="1" <?php echo $user['status'] == 1 ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo $user['status'] == 0 ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Branch Assignment</label>
                                <select name="branch_id" class="form-control">
                                    <option value="">-- Global / No Branch --</option>
                                    <?php
                                    $branches = $conn->query("SELECT * FROM branches");
                                    while($b = $branches->fetch_assoc()) {
                                        $sel = ($user['branch_id'] == $b['id']) ? 'selected' : '';
                                        echo "<option value='{$b['id']}' $sel>{$b['branch_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-block">Update User Details</button>
                            <a href="list.php" class="btn btn-default btn-block">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function togglePass() {
    var x = document.getElementById("passwordField");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}
</script>

<?php include '../includes/footer.php'; ?>
