<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';

checkLogin();
$user = getLoggedInUser($conn);

// Fetch Fees/Payments
$fees = $conn->query("SELECT f.*, s.student_name, s.entity_id 
                      FROM fees f 
                      JOIN students s ON f.student_id = s.id 
                      ORDER BY f.date_collected DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Management | Netcoder IT ERP</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .wrapper { display: flex; width: 100%; align-items: stretch; }
        #content { width: 100%; padding: 0; min-height: 100vh; background: #f4f7fe; }
    </style>
</head>
<body>

    <div class="wrapper">
        <?php include '../includes/sidebar.php'; ?>

        <div id="content">
            <?php include '../includes/header.php'; ?>

            <div class="container-fluid px-4">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800 fw-bold">Fee Tracking</h1>
                    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#collectFeeModal">
                        <i class="fas fa-hand-holding-usd me-1"></i> Collect Payment
                    </button>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="feesTable" class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Intern</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Mode</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($f = $fees->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo date('d M, Y', strtotime($f['date_collected'])); ?></td>
                                        <td>
                                            <div class="fw-bold"><?php echo $f['student_name']; ?></div>
                                            <small class="text-muted"><?php echo $f['entity_id']; ?></small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border"><?php echo ucfirst($f['fee_type']); ?></span></td>
                                        <td class="fw-bold"><?php echo formatCurrency($f['amount']); ?></td>
                                        <td><span class="badge bg-info text-white"><?php echo strtoupper($f['payment_mode']); ?></span></td>
                                        <td>
                                            <span class="badge <?php 
                                                echo $f['status'] == 'paid' ? 'bg-success' : ($f['status'] == 'pending' ? 'bg-warning' : 'bg-danger'); 
                                            ?>">
                                                <?php echo ucfirst($f['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="../invoice.php?id=<?php echo $f['id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa fa-print"></i></a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collect Fee Modal -->
    <div class="modal fade" id="collectFeeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title fw-bold">Collect Fee</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="../ajax/collect_fee.php" method="POST">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label">Fee Type</label>
                            <select name="fee_type" class="form-select">
                                <option value="monthly">Monthly</option>
                                <option value="registration">Registration</option>
                                <option value="exam">Exam</option>
                                <option value="full_payment">Full Payment</option>
                                <option value="service">Service</option>
                                <option value="advance">Advance</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount</label>
                            <input type="number" name="amount" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Payment Mode</label>
                            <select name="payment_mode" class="form-select">
                                <option value="cash">Cash</option>
                                <option value="online">Online</option>
                                <option value="upi">UPI</option>
                                <option value="cheque">Cheque</option>
                                <option value="card">Card</option>
                                <option value="neft">NEFT</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">UTR/Reference Number</label>
                            <input type="text" name="utr_number" class="form-control" placeholder="For UPI/Online/Cheque">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#feesTable').DataTable();
            $('#sidebarCollapse').on('click', function() {
                $('#sidebar').toggleClass('active');
            });
        });
    </script>
</body>
</html>
