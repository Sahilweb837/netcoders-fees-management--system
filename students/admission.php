<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

// Generate Student ID
$last_id_res = $conn->query("SELECT id FROM students ORDER BY id DESC LIMIT 1");
$next_id = ($last_id_res->num_rows > 0) ? $last_id_res->fetch_assoc()['id'] + 1 : 1;
$student_id = "STU-" . date('Y') . "-" . str_pad($next_id, 4, '0', STR_PAD_LEFT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $conn->real_escape_string($_POST['full_name']);
    $father_name = $conn->real_escape_string($_POST['father_name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $address = $conn->real_escape_string($_POST['address']);
    $course_id = (int)$_POST['course_id'];
    $batch_id = (int)$_POST['batch_id'];
    $admission_date = $_POST['admission_date'];
    $total_fee = (float)$_POST['total_fee'];
    $discount = (float)$_POST['discount'];
    $paid_fee = (float)$_POST['paid_fee'];
    $due_fee = ($total_fee - $discount) - $paid_fee;

    // Handle Photo Upload
    $photo = "";
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo = "student_" . time() . "." . $ext;
        move_uploaded_file($_FILES['photo']['tmp_name'], "../uploads/" . $photo);
    }

    $stmt = $conn->prepare("INSERT INTO students (student_id, full_name, father_name, phone, email, address, photo, course_id, batch_id, admission_date, total_fee, discount, paid_fee, due_fee) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssiisdddd", $student_id, $full_name, $father_name, $phone, $email, $address, $photo, $course_id, $batch_id, $admission_date, $total_fee, $discount, $paid_fee, $due_fee);

    if ($stmt->execute()) {
        $student_db_id = $stmt->insert_id;
        
        // If initial payment is made, generate invoice
        if ($paid_fee > 0) {
            $invoice_no = "INV-" . rand(10000, 99999);
            $inv_stmt = $conn->prepare("INSERT INTO invoices (invoice_no, student_id, amount, final_amount, invoice_date) VALUES (?, ?, ?, ?, ?)");
            $inv_stmt->bind_param("sidds", $invoice_no, $student_db_id, $paid_fee, $paid_fee, $admission_date);
            $inv_stmt->execute();
            
            $invoice_id = $inv_stmt->insert_id;
            $pay_stmt = $conn->prepare("INSERT INTO payments (invoice_id, student_id, amount, payment_date, remarks) VALUES (?, ?, ?, ?, 'Admission Fee')");
            $pay_stmt->bind_param("iids", $invoice_id, $student_db_id, $paid_fee, $admission_date);
            $pay_stmt->execute();
        }

        flash("Student admission successful! Student ID: $student_id");
        redirect('students/list.php');
    } else {
        $error = "Error during admission: " . $conn->error;
    }
}

$courses = $conn->query("SELECT * FROM courses WHERE is_active = 1");
$batches = $conn->query("SELECT * FROM batches");

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Student Admission</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="admission.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="card card-primary card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Personal Information</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Student ID</label>
                                        <input type="text" class="form-control" value="<?php echo $student_id; ?>" readonly>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Full Name</label>
                                        <input type="text" name="full_name" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Father's Name</label>
                                        <input type="text" name="father_name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <input type="text" name="phone" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email Address</label>
                                        <input type="email" name="email" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Photo</label>
                                        <input type="file" name="photo" class="form-control-file">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-success card-outline">
                        <div class="card-header">
                            <h3 class="card-title">Course & Fees</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Course</label>
                                <select name="course_id" id="course_id" class="form-control" required onchange="updateFees()">
                                    <option value="">Select Course</option>
                                    <?php while($c = $courses->fetch_assoc()): ?>
                                        <option value="<?php echo $c['id']; ?>" data-fee="<?php echo $c['fees']; ?>"><?php echo $c['course_name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Batch</label>
                                <select name="batch_id" class="form-control">
                                    <option value="">Select Batch</option>
                                    <?php while($b = $batches->fetch_assoc()): ?>
                                        <option value="<?php echo $b['id']; ?>"><?php echo $b['batch_name']; ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Admission Date</label>
                                <input type="date" name="admission_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Total Course Fee</label>
                                <input type="number" step="0.01" name="total_fee" id="total_fee" class="form-control" required oninput="calculateBalance()">
                            </div>
                            <div class="form-group">
                                <label>Discount (<?php echo CURRENCY; ?>)</label>
                                <input type="number" step="0.01" name="discount" id="discount" class="form-control" value="0" oninput="calculateBalance()">
                            </div>
                            <div class="form-group">
                                <label>Initial Payment</label>
                                <input type="number" step="0.01" name="paid_fee" id="paid_fee" class="form-control" value="0" oninput="calculateBalance()">
                            </div>
                            <div class="form-group">
                                <label>Balance Due</label>
                                <input type="text" id="balance_due" class="form-control" value="0" readonly style="background-color: #f8f9fa; font-weight: bold; color: var(--first-color);">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-block btn-success btn-lg">Register Student</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<script>
function updateFees() {
    var select = document.getElementById('course_id');
    var fee = select.options[select.selectedIndex].getAttribute('data-fee');
    document.getElementById('total_fee').value = fee;
    calculateBalance();
}

function calculateBalance() {
    var total = parseFloat(document.getElementById('total_fee').value) || 0;
    var discount = parseFloat(document.getElementById('discount').value) || 0;
    var paid = parseFloat(document.getElementById('paid_fee').value) || 0;
    
    var balance = (total - discount) - paid;
    document.getElementById('balance_due').value = "<?php echo CURRENCY; ?> " + balance.toFixed(2);
}
</script>

<?php include '../includes/footer.php'; ?>
