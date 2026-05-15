<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

if (!isset($_GET['id'])) {
    redirect('students/list.php');
}

$id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $father_name = $conn->real_escape_string($_POST['father_name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $address = $conn->real_escape_string($_POST['address']);
    $course_id = (int)$_POST['course_id'];
    $batch_id = (int)$_POST['batch_id'];
    $status = (int)$_POST['status'];

    $stmt = $conn->prepare("UPDATE students SET full_name=?, father_name=?, phone=?, email=?, address=?, course_id=?, batch_id=?, status=? WHERE id=?");
    $stmt->bind_param("sssssiiii", $full_name, $father_name, $phone, $email, $address, $course_id, $batch_id, $status, $id);

    if ($stmt->execute()) {
        flash("Student updated successfully!");
        redirect('students/list.php');
    } else {
        $error = "Error updating student.";
    }
}

$student = $conn->query("SELECT * FROM students WHERE id = $id")->fetch_assoc();
$courses = $conn->query("SELECT * FROM courses WHERE is_active = 1");
$batches = $conn->query("SELECT * FROM batches");

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Student</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <form action="edit.php?id=<?php echo $id; ?>" method="POST">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo $student['full_name']; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Father's Name</label>
                                <input type="text" name="father_name" class="form-control" value="<?php echo $student['father_name']; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo $student['phone']; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo $student['email']; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Course</label>
                                <select name="course_id" class="form-control" required>
                                    <?php while($c = $courses->fetch_assoc()): ?>
                                        <option value="<?php echo $c['id']; ?>" <?php echo ($c['id'] == $student['course_id']) ? 'selected' : ''; ?>><?php echo $c['course_name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Batch</label>
                                <select name="batch_id" class="form-control">
                                    <option value="">-- No Batch --</option>
                                    <?php while($b = $batches->fetch_assoc()): ?>
                                        <option value="<?php echo $b['id']; ?>" <?php echo ($b['id'] == $student['batch_id']) ? 'selected' : ''; ?>><?php echo $b['batch_name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="1" <?php echo ($student['status'] == 1) ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo ($student['status'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="address" class="form-control" rows="2"><?php echo $student['address']; ?></textarea>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">Update Student</button>
                    <a href="list.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
