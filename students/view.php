<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

if (!isset($_GET['id'])) {
    redirect('students/list.php');
}

$id = (int)$_GET['id'];
$query = $conn->query("
    SELECT s.*, c.course_name, b.batch_name 
    FROM students s 
    LEFT JOIN courses c ON s.course_id = c.id 
    LEFT JOIN batches b ON s.batch_id = b.id 
    WHERE s.id = $id
");

if ($query->num_rows === 0) {
    redirect('students/list.php');
}

$student = $query->fetch_assoc();

// Fetch Payment History
$payments = $conn->query("
    SELECT p.*, i.invoice_no 
    FROM payments p 
    JOIN invoices i ON p.invoice_id = i.id 
    WHERE p.student_id = $id 
    ORDER BY p.payment_date DESC
");

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Student Profile</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="list.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
                <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Edit Profile</a>
                <button type="button" class="btn btn-warning" onclick="generateLogin(<?php echo $id; ?>, '<?php echo $student['student_id']; ?>')"><i class="fas fa-key"></i> Generate Login</button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <!-- Profile Image -->
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile text-center">
                        <?php if($student['photo']): ?>
                            <img class="profile-user-img img-fluid img-circle" src="../uploads/<?php echo $student['photo']; ?>" alt="User profile picture">
                        <?php else: ?>
                            <i class="fas fa-user-circle fa-7x text-muted mb-3"></i>
                        <?php endif; ?>

                        <h3 class="profile-username text-center"><?php echo $student['full_name']; ?></h3>
                        <p class="text-muted text-center"><?php echo $student['course_name']; ?></p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Student ID</b> <a class="float-right"><?php echo $student['student_id']; ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Phone</b> <a class="float-right"><?php echo $student['phone']; ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Batch</b> <a class="float-right"><?php echo $student['batch_name'] ?? 'Not Assigned'; ?></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Fees Info -->
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">Fee Summary</h3>
                    </div>
                    <div class="card-body">
                        <strong>Total Fees</strong>
                        <p class="text-muted"><?php echo CURRENCY . number_format($student['total_fee'], 2); ?></p>
                        <hr>
                        <strong>Paid Amount</strong>
                        <p class="text-success"><?php echo CURRENCY . number_format($student['paid_fee'], 2); ?></p>
                        <hr>
                        <strong>Due Amount</strong>
                        <p class="text-danger"><?php echo CURRENCY . number_format($student['due_fee'], 2); ?></p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#payments" data-toggle="tab">Payment History</a></li>
                            <li class="nav-item"><a class="nav-link" href="#details" data-toggle="tab">Details</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="active tab-pane" id="payments">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Invoice #</th>
                                            <th>Amount</th>
                                            <th>Remarks</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while($p = $payments->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo date('d M Y', strtotime($p['payment_date'])); ?></td>
                                                <td><?php echo $p['invoice_no']; ?></td>
                                                <td><?php echo CURRENCY . number_format($p['amount'], 2); ?></td>
                                                <td><?php echo $p['remarks']; ?></td>
                                                <td>
                                                    <a href="../invoices/print.php?id=<?php echo $p['invoice_id']; ?>" class="btn btn-xs btn-primary" target="_blank">Print</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                        <?php if($payments->num_rows == 0): ?>
                                            <tr><td colspan="5" class="text-center">No payments found.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="tab-pane" id="details">
                                <strong><i class="fas fa-envelope mr-1"></i> Email</strong>
                                <p class="text-muted"><?php echo $student['email']; ?></p>
                                <hr>
                                <strong><i class="fas fa-user-friends mr-1"></i> Father's Name</strong>
                                <p class="text-muted"><?php echo $student['father_name']; ?></p>
                                <hr>
                                <strong><i class="fas fa-map-marker-alt mr-1"></i> Address</strong>
                                <p class="text-muted"><?php echo $student['address']; ?></p>
                                <hr>
                                <strong><i class="fas fa-calendar-alt mr-1"></i> Admission Date</strong>
                                <p class="text-muted"><?php echo date('d M Y', strtotime($student['admission_date'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function generateLogin(studentId, username) {
    if (confirm("Generate login credentials for student " + username + "? Default password will be 'student123'")) {
        $.post("<?php echo BASE_URL; ?>ajax/generate_user.php", {
            entity_id: studentId,
            username: username,
            type: 'student'
        }, function(response) {
            var data = JSON.parse(response);
            alert(data.message);
        });
    }
}
</script>

<?php include '../includes/footer.php'; ?>
