<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

$query = "SELECT * FROM client_invoices ORDER BY invoice_date DESC";
$result = $conn->query($query);

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0" style="font-weight: 700; color: #ff5532;">
                    <i class="fas fa-file-alt mr-2"></i> Client Invoices
                </h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="client_create.php" class="btn btn-primary">
                    <i class="fas fa-plus-circle mr-1"></i> Create New Invoice
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover table-striped mb-0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th>Inv #</th>
                            <th>Client Name</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="font-weight-bold"><?php echo $row['invoice_no']; ?></td>
                                <td><?php echo $row['client_name']; ?></td>
                                <td class="text-muted small"><?php echo substr($row['service_description'], 0, 50); ?>...</td>
                                <td class="text-success font-weight-bold"><?php echo CURRENCY . number_format($row['total_amount'], 2); ?></td>
                                <td><?php echo date('d M, Y', strtotime($row['invoice_date'])); ?></td>
                                <td>
                                    <a href="client_print.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-info" target="_blank">
                                        <i class="fas fa-print"></i> Print
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">No client invoices found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
