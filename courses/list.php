<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM courses WHERE id = $id");
    flash("Course deleted successfully!");
    redirect('courses/list.php');
}

$courses = $conn->query("SELECT * FROM courses ORDER BY id DESC");

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0" style="font-weight: 700; color: var(--first-color);">
                    <i class="fas fa-book-open mr-2"></i> Course Curriculum
                </h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="add.php" class="btn btn-primary"><i class="fas fa-folder-plus"></i> Create Course Module</a>
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
                            <th>ID</th>
                            <th>Course Name</th>
                            <th>Duration</th>
                            <th>Fees</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $courses->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['course_name']; ?></td>
                            <td><?php echo $row['duration']; ?></td>
                            <td><?php echo CURRENCY . number_format($row['fees'], 2); ?></td>
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
