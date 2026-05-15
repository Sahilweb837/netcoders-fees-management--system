<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

if (!isset($_GET['id'])) redirect('courses/list.php');
$id = (int)$_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $conn->real_escape_string($_POST['course_name']);
    $duration = $conn->real_escape_string($_POST['duration']);
    $fees = (float)$_POST['fees'];
    $description = $conn->real_escape_string($_POST['description']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE courses SET course_name = ?, duration = ?, fees = ?, description = ?, is_active = ? WHERE id = ?");
    $stmt->bind_param("ssdsii", $course_name, $duration, $fees, $description, $is_active, $id);

    if ($stmt->execute()) {
        flash("Course updated successfully!");
        redirect('courses/list.php');
    } else {
        $error = "Error updating course.";
    }
}

$course = $conn->query("SELECT * FROM courses WHERE id = $id")->fetch_assoc();
if (!$course) redirect('courses/list.php');

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Course Module</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline">
            <form action="edit.php?id=<?php echo $id; ?>" method="POST">
                <div class="card-body">
                    <div class="form-group">
                        <label>Course Name</label>
                        <input type="text" name="course_name" class="form-control" value="<?php echo $course['course_name']; ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Duration</label>
                                <select name="duration" class="form-control" required>
                                    <?php 
                                    $durations = ["45 Days", "2 Months", "3 Months", "6 Months", "1 Year", "2 Years", "Custom"];
                                    foreach ($durations as $d) {
                                        $selected = ($course['duration'] == $d) ? 'selected' : '';
                                        echo "<option value='$d' $selected>$d</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fees (<?php echo CURRENCY; ?>)</label>
                                <input type="number" step="0.01" name="fees" class="form-control" value="<?php echo $course['fees']; ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php echo $course['description']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="is_active" class="custom-control-input" id="isActive" <?php echo $course['is_active'] ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="isActive">Course Active Status</label>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary px-4">Update Course</button>
                    <a href="list.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
