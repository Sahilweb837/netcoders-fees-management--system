<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $batch_name = $conn->real_escape_string($_POST['batch_name']);
    $course_id = (int)$_POST['course_id'];
    $timing = $conn->real_escape_string($_POST['timing']);

    $stmt = $conn->prepare("INSERT INTO batches (batch_name, course_id, timing) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $batch_name, $course_id, $timing);
    $stmt->execute();
    flash("Batch created successfully!");
}

$batches = $conn->query("SELECT b.*, c.course_name FROM batches b JOIN courses c ON b.course_id = c.id ORDER BY b.id DESC");
$courses = $conn->query("SELECT * FROM courses WHERE is_active = 1");

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Manage Batches</h1>
            </div>
            <div class="col-sm-6 text-right">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addBatchModal">
                    <i class="fas fa-plus"></i> Create Batch
                </button>
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
                            <th>Batch Name</th>
                            <th>Course</th>
                            <th>Timing</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $batches->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo $row['batch_name']; ?></td>
                            <td><?php echo $row['course_name']; ?></td>
                            <td><?php echo $row['timing']; ?></td>
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

<!-- Add Batch Modal -->
<div class="modal fade" id="addBatchModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="list.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Batch</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Batch Name</label>
                        <input type="text" name="batch_name" class="form-control" placeholder="e.g. Morning 09:00 AM" required>
                    </div>
                    <div class="form-group">
                        <label>Course</label>
                        <select name="course_id" class="form-control" required>
                            <option value="">-- Select Course --</option>
                            <?php while($c = $courses->fetch_assoc()): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo $c['course_name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Timing</label>
                        <input type="text" name="timing" class="form-control" placeholder="e.g. 09:00 AM - 11:00 AM">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Create Batch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
