<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM students WHERE id = $id");
    flash("Student deleted successfully!");
    redirect('students/list.php');
}

// Filter Logic
$where = "WHERE 1=1";
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $where .= " AND (s.full_name LIKE '%$search%' OR s.student_id LIKE '%$search%' OR s.phone LIKE '%$search%')";
}
if (isset($_GET['course_filter']) && !empty($_GET['course_filter'])) {
    $c_id = (int)$_GET['course_filter'];
    $where .= " AND s.course_id = $c_id";
}

$students = $conn->query("SELECT s.*, c.course_name FROM students s LEFT JOIN courses c ON s.course_id = c.id $where ORDER BY s.id DESC");
$courses_filter = $conn->query("SELECT id, course_name FROM courses");

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0" style="font-weight: 700; color: var(--first-color);">
                    <i class="fas fa-user-graduate mr-2"></i> Student Directory
                </h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="admission.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Register New Student</a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body">
                <form action="list.php" method="GET">
                    <div class="row">
                        <div class="col-md-5">
                            <input type="text" name="search" class="form-control" placeholder="Search by Name, ID or Phone" value="<?php echo $_GET['search'] ?? ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <select name="course_filter" class="form-control">
                                <option value="">-- Filter by Course --</option>
                                <?php while($cf = $courses_filter->fetch_assoc()): ?>
                                    <option value="<?php echo $cf['id']; ?>" <?php echo (isset($_GET['course_filter']) && $_GET['course_filter'] == $cf['id']) ? 'selected' : ''; ?>>
                                        <?php echo $cf['course_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-dark btn-block"><i class="fas fa-filter"></i> Apply Filters</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered table-striped table-responsive-md">
                    <thead>
                        <tr>
                            <th>Student ID</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Total Fee</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $students->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['student_id']; ?></td>
                            <td>
                                <?php if($row['photo']): ?>
                                    <img src="../uploads/<?php echo $row['photo']; ?>" width="40" height="40" class="img-circle">
                                <?php else: ?>
                                    <i class="fas fa-user-circle fa-2x text-muted"></i>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row['full_name']; ?></td>
                            <td><?php echo $row['course_name']; ?></td>
                            <td><?php echo CURRENCY . number_format($row['total_fee'], 2); ?></td>
                            <td class="text-success"><?php echo CURRENCY . number_format($row['paid_fee'], 2); ?></td>
                            <td class="text-danger"><?php echo CURRENCY . number_format($row['due_fee'], 2); ?></td>
                            <td>
                                <a href="view.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
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
