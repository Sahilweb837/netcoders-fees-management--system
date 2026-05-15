<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

$month_filter = $_GET['month'] ?? 'all';
$where = "WHERE 1=1";
if ($month_filter === 'current') {
    $where .= " AND MONTH(i.invoice_date) = MONTH(CURRENT_DATE()) AND YEAR(i.invoice_date) = YEAR(CURRENT_DATE())";
}

$invoices = $conn->query("SELECT i.*, s.full_name, s.student_id as s_id FROM invoices i JOIN students s ON i.student_id = s.id $where ORDER BY i.id DESC");

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Billing & Invoices</h1>
            </div>
            <div class="col-sm-6 text-right">
                <div class="btn-group shadow-sm mr-2">
                    <a href="?month=current" class="btn btn-<?php echo ($month_filter === 'current') ? 'primary' : 'outline-primary'; ?> btn-sm">Current Month</a>
                    <a href="list.php" class="btn btn-<?php echo ($month_filter === 'all') ? 'primary' : 'outline-primary'; ?> btn-sm">All Time</a>
                </div>
                <button type="button" class="btn btn-success btn-sm px-3 shadow-sm mr-1" onclick="exportInvoicesToCSV()">
                    <i class="fas fa-file-csv"></i> CSV
                </button>
                <button type="button" class="btn btn-danger btn-sm px-3 shadow-sm" onclick="window.print()">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
                <table class="table table-hover table-striped mb-0 shadow-sm" id="invoiceTable">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>Invoice No</th>
                            <th>Student</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Mode</th>
                            <th class="no-print">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($invoices->num_rows > 0): ?>
                            <?php while($row = $invoices->fetch_assoc()): ?>
                            <tr>
                                <td><strong>#<?php echo $row['invoice_no']; ?></strong></td>
                                <td><?php echo $row['full_name'] . " (" . $row['s_id'] . ")"; ?></td>
                                <td><i class="far fa-calendar-alt text-muted mr-1"></i> <?php echo date('d M Y', strtotime($row['invoice_date'])); ?></td>
                                <td class="font-weight-bold text-success"><?php echo CURRENCY . number_format($row['final_amount'], 2); ?></td>
                                <td><span class="badge badge-light border"><?php echo $row['payment_mode']; ?></span></td>
                                <td class="no-print">
                                    <a href="print.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary px-3 shadow-sm" target="_blank">
                                        <i class="fas fa-print mr-1"></i> Print
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-4 text-muted">No records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script>
function exportInvoicesToCSV() {
    let table = document.getElementById("invoiceTable");
    let rows = Array.from(table.rows);
    let csvContent = rows.map(row => Array.from(row.cells).map(cell => `"${cell.innerText}"`).join(",")).join("\n");
    let blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    let link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "Billing_Report.csv";
    link.click();
}
</script>

<?php include '../includes/footer.php'; ?>
