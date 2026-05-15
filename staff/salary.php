<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

// Auto-Migration: Ensure table exists
$conn->query("CREATE TABLE IF NOT EXISTS `staff_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_month` varchar(20) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(50) DEFAULT 'Cash',
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `staff_id` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay_salary'])) {
    $staff_id = (int)$_POST['staff_id'];
    $amount = (float)$_POST['amount'];
    $payment_month = $conn->real_escape_string($_POST['payment_month']);
    $payment_date = $_POST['payment_date'];
    $payment_method = $conn->real_escape_string($_POST['payment_method']);
    $remarks = $conn->real_escape_string($_POST['remarks']);

    $stmt = $conn->prepare("INSERT INTO staff_payments (staff_id, amount, payment_month, payment_date, payment_method, remarks) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("idssss", $staff_id, $amount, $payment_month, $payment_date, $payment_method, $remarks);

    if ($stmt->execute()) {
        // Also log as an expense
        $desc = "Staff Salary - " . $payment_month;
        $conn->query("INSERT INTO expenses (amount, category, description, expense_date) VALUES ($amount, 'Salary', '$desc', '$payment_date')");
        
        flash("Salary payment recorded successfully!");
    } else {
        flash("Error recording payment: " . $conn->error, "danger");
    }
    redirect('staff/salary.php');
}

$staff_list = $conn->query("SELECT id, name, salary, role FROM staff ORDER BY name ASC");
$payments = $conn->query("SELECT p.*, s.name as staff_name FROM staff_payments p JOIN staff s ON p.staff_id = s.id ORDER BY p.payment_date DESC LIMIT 50");

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0" style="font-weight: 700; color: #ff5532;">
                    <i class="fas fa-hand-holding-usd mr-2"></i> Staff Salary Management
                </h1>
            </div>
            <div class="col-sm-6 text-right">
                <button class="btn btn-primary" data-toggle="modal" data-target="#paySalaryModal">
                    <i class="fas fa-plus-circle"></i> Record Salary Payment
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Recent Salary Disbursements</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Staff Name</th>
                            <th>Month</th>
                            <th>Amount</th>
                            <th>Date Paid</th>
                            <th>Method</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($payments->num_rows > 0): ?>
                            <?php while($p = $payments->fetch_assoc()): ?>
                            <tr>
                                <td class="font-weight-bold"><?php echo $p['staff_name']; ?></td>
                                <td><span class="badge badge-info"><?php echo $p['payment_month']; ?></span></td>
                                <td class="text-success font-weight-bold"><?php echo CURRENCY . number_format($p['amount'], 2); ?></td>
                                <td><?php echo date('d M, Y', strtotime($p['payment_date'])); ?></td>
                                <td><?php echo $p['payment_method']; ?></td>
                                <td class="text-muted small"><?php echo $p['remarks']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-4">No salary records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Pay Salary Modal -->
<div class="modal fade" id="paySalaryModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="salary.php" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Record Salary Disbursement</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Select Staff Member</label>
                        <select name="staff_id" class="form-control select2" style="width: 100%;" required onchange="updateSalaryField(this)">
                            <option value="">-- Choose Staff --</option>
                            <?php while($s = $staff_list->fetch_assoc()): ?>
                                <option value="<?php echo $s['id']; ?>" data-salary="<?php echo $s['salary']; ?>"><?php echo $s['name']; ?> (<?php echo $s['role']; ?>)</option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Amount (<?php echo CURRENCY; ?>)</label>
                                <input type="number" step="0.01" name="amount" id="salary_amount" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>For Month</label>
                                <select name="payment_month" class="form-control" required>
                                    <?php 
                                    for($i=0; $i<6; $i++) {
                                        $m = date('F Y', strtotime("-$i months"));
                                        echo "<option value='$m'>$m</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date Paid</label>
                                <input type="date" name="payment_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Method</label>
                                <select name="payment_method" class="form-control">
                                    <option value="Cash">Cash</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="UPI">UPI / Digital</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2" placeholder="e.g. Performance bonus included"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" name="pay_salary" class="btn btn-success">Confirm Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updateSalaryField(select) {
    var salary = select.options[select.selectedIndex].getAttribute('data-salary');
    document.getElementById('salary_amount').value = salary;
}
</script>

<?php include '../includes/footer.php'; ?>
