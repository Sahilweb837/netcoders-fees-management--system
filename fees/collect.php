<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = (int)$_POST['student_id'];
    $amount = (float)$_POST['amount'];
    $payment_mode = $conn->real_escape_string($_POST['payment_mode']);
    $payment_date = $_POST['payment_date'];
    $remarks = $conn->real_escape_string($_POST['remarks']);

    // Generate Invoice
    $invoice_no = "INV-" . rand(10000, 99999);
    $inv_stmt = $conn->prepare("INSERT INTO invoices (invoice_no, student_id, amount, final_amount, payment_mode, invoice_date) VALUES (?, ?, ?, ?, ?, ?)");
    $inv_stmt->bind_param("siddss", $invoice_no, $student_id, $amount, $amount, $payment_mode, $payment_date);
    
    if ($inv_stmt->execute()) {
        $invoice_id = $inv_stmt->insert_id;
        
        // Record Payment
        $pay_stmt = $conn->prepare("INSERT INTO payments (invoice_id, student_id, amount, payment_date, remarks) VALUES (?, ?, ?, ?, ?)");
        $pay_stmt->bind_param("iidss", $invoice_id, $student_id, $amount, $payment_date, $remarks);
        $pay_stmt->execute();

        // Update Student Table (Paid and Due)
        $conn->query("UPDATE students SET paid_fee = paid_fee + $amount, due_fee = due_fee - $amount WHERE id = $student_id");

        flash("Fee collected successfully! Invoice: $invoice_no");
        redirect('invoices/list.php');
    } else {
        $error = "Error collecting fee: " . $conn->error;
    }
}

$selected_student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;
$students = $conn->query("SELECT s.id, s.student_id, s.full_name, s.due_fee, c.course_name 
                          FROM students s 
                          LEFT JOIN courses c ON s.course_id = c.id 
                          WHERE s.status = 1");
$student_list = [];
while($row = $students->fetch_assoc()) {
    $student_list[] = $row;
}

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Collect Fees</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-info">
                    <div class="card-header">
                        <h3 class="card-title">New Payment</h3>
                    </div>
                    <form action="collect.php" method="POST">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Select Student</label>
                                <select name="student_id" id="student_id" class="form-control select2" required onchange="updateDue()">
                                    <option value="">-- Select Student --</option>
                                    <?php foreach($student_list as $s): ?>
                                        <option value="<?php echo $s['id']; ?>" data-due="<?php echo $s['due_fee']; ?>" <?php echo ($selected_student_id == $s['id']) ? 'selected' : ''; ?>>
                                            <?php echo $s['student_id'] . " | " . $s['full_name'] . " (" . ($s['course_name'] ?: 'No Course') . ")"; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Current Due Amount</label>
                                <input type="text" id="due_display" class="form-control" readonly>
                            </div>
                            <div class="form-group">
                                <label>Amount to Pay (<?php echo CURRENCY; ?>)</label>
                                <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Payment Mode</label>
                                <select name="payment_mode" class="form-control">
                                    <option value="Cash">Cash</option>
                                    <option value="UPI/Online">UPI / Online</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Payment Date</label>
                                <input type="date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Remarks</label>
                                <textarea name="remarks" class="form-control" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-info btn-block">Generate Invoice & Record Payment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
window.onload = function() {
    if (document.getElementById('student_id').value != "") {
        updateDue();
    }
};

function updateDue() {
    var select = document.getElementById('student_id');
    var due = select.options[select.selectedIndex].getAttribute('data-due');
    document.getElementById('due_display').value = "<?php echo CURRENCY; ?> " + due;
    document.getElementById('amount').value = due;
}
</script>

<?php include '../includes/footer.php'; ?>
