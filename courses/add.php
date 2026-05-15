<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $conn->real_escape_string($_POST['course_name']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $fees = (float)$_POST['fees'];
    $description = $conn->real_escape_string($_POST['description']);

    $stmt = $conn->prepare("INSERT INTO courses (course_name, duration, fees, description) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $course_name, $duration, $fees, $description);

    if ($stmt->execute()) {
        flash("Course added successfully!");
        redirect('courses/list.php');
    } else {
        $error = "Error adding course.";
    }
}

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Add New Course</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <form action="add.php" method="POST">
                <div class="card-body">
                    <div class="form-group">
                        <label>Course Name</label>
                        <input type="text" name="course_name" class="form-control" placeholder="e.g. Web Development" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Duration</label>
                                <select name="duration" class="form-control" required>
                                    <option value="">-- Select Duration --</option>
                                    <option value="45 Days">45 Days</option>
                                    <option value="2 Months">2 Months</option>
                                    <option value="3 Months">3 Months</option>
                                    <option value="6 Months">6 Months</option>
                                    <option value="1 Year">1 Year</option>
                                    <option value="2 Years">2 Years</option>
                                    <option value="Custom">Custom</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fees (<?php echo CURRENCY; ?>)</label>
                                <input type="number" step="0.01" name="fees" class="form-control" placeholder="e.g. 15000" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">Save Course</button>
                    <a href="list.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
