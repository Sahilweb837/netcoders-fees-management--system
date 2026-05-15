<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();
checkRole(['root_admin', 'super_admin', 'admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $username = $conn->real_escape_string($_POST['username']);
    $plain_pass = $_POST['password'];
    $password = password_hash($plain_pass, PASSWORD_BCRYPT);
    $role = $_POST['role'];
    $branch_id = !empty($_POST['branch_id']) ? (int)$_POST['branch_id'] : NULL;

    $stmt = $conn->prepare("INSERT INTO users (name, username, password, plain_password, role, branch_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $name, $username, $password, $plain_pass, $role, $branch_id);

    if ($stmt->execute()) {
        logActivity("Created new user: $username");
        flash("User created successfully!");
        redirect('users/list.php');
    } else {
        $error = "Error: Username might already exist or database connection issue.";
    }
}

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Add System User</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-primary">
                    <form action="add.php" method="POST">
                        <div class="card-body">
                            <?php if(isset($error)): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <select name="role" class="form-control select2" required>
                                    <option value="staff">Staff</option>
                                    <option value="accountant">Accountant</option>
                                    <option value="admin">Admin</option>
                                    <option value="super_admin">Super Admin</option>
                                    <option value="root_admin">Root Admin</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Assign Branch (Optional)</label>
                                <select name="branch_id" class="form-control">
                                    <option value="">-- All Branches (Global) --</option>
                                    <?php
                                    $branches = $conn->query("SELECT * FROM branches");
                                    if ($branches) {
                                        while($b = $branches->fetch_assoc()) {
                                            echo "<option value='{$b['id']}'>{$b['branch_name']}</option>";
                                        }
                                    }
                                    ?>
                                </select>
                                <small class="text-muted">Staff and Admins should usually be assigned to a branch.</small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-block">Create User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
