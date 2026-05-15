<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (float)$_POST['amount'];
    $category = $conn->real_escape_string($_POST['category']);
    $description = $conn->real_escape_string($_POST['description']);
    $expense_date = $_POST['expense_date'];

    $stmt = $conn->prepare("INSERT INTO expenses (amount, category, description, expense_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("dsss", $amount, $category, $description, $expense_date);
    $stmt->execute();
    flash("Expense recorded!");
}

// Summary Statistics
$stats = [
    'today' => $conn->query("SELECT SUM(amount) FROM expenses WHERE DATE(expense_date) = CURRENT_DATE()")->fetch_row()[0] ?? 0,
    'month' => $conn->query("SELECT SUM(amount) FROM expenses WHERE MONTH(expense_date) = MONTH(CURRENT_DATE()) AND YEAR(expense_date) = YEAR(CURRENT_DATE())")->fetch_row()[0] ?? 0,
    'total' => $conn->query("SELECT SUM(amount) FROM expenses")->fetch_row()[0] ?? 0
];

// Category Breakdown for Chart
$categories_res = $conn->query("SELECT category, SUM(amount) as total FROM expenses GROUP BY category");
$cat_labels = [];
$cat_data = [];
while($cr = $categories_res->fetch_assoc()) {
    $cat_labels[] = $cr['category'];
    $cat_data[] = $cr['total'];
}

// Month Filtering Logic
$month_filter = $_GET['month'] ?? 'current';
$where = "WHERE 1=1";
if ($month_filter === 'current') {
    $where .= " AND MONTH(expense_date) = MONTH(CURRENT_DATE()) AND YEAR(expense_date) = YEAR(CURRENT_DATE())";
} elseif ($month_filter === 'previous') {
    $where .= " AND MONTH(expense_date) = MONTH(CURRENT_DATE() - INTERVAL 1 MONTH) AND YEAR(expense_date) = YEAR(CURRENT_DATE() - INTERVAL 1 MONTH)";
}

$expenses = $conn->query("SELECT * FROM expenses $where ORDER BY expense_date DESC");
$total_expense = $conn->query("SELECT SUM(amount) FROM expenses $where")->fetch_row()[0] ?? 0;

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Expense Ledger</h1>
                <p class="text-muted">Viewing: <strong><?php echo ucfirst($month_filter); ?> Month</strong> | Total: <strong class="text-danger"><?php echo CURRENCY . number_format($total_expense, 2); ?></strong></p>
            </div>
            <div class="col-sm-6 text-right">
                <div class="btn-group shadow-sm mr-2">
                    <a href="?month=current" class="btn btn-<?php echo ($month_filter === 'current') ? 'primary' : 'outline-primary'; ?> btn-sm">Current Month</a>
                    <a href="?month=previous" class="btn btn-<?php echo ($month_filter === 'previous') ? 'primary' : 'outline-primary'; ?> btn-sm">Previous Month</a>
                    <a href="list.php" class="btn btn-outline-primary btn-sm">All Time</a>
                </div>
                <button type="button" class="btn btn-success btn-sm px-3 shadow-sm mr-1" onclick="exportToCSV()">
                    <i class="fas fa-file-csv"></i> CSV
                </button>
                <button type="button" class="btn btn-danger btn-sm px-3 shadow-sm mr-1" onclick="window.print()">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <button type="button" class="btn btn-primary btn-sm px-3 shadow-sm" data-toggle="modal" data-target="#addExpenseModal">
                    <i class="fas fa-plus-circle"></i> Add New
                </button>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Stats Boxes -->
        <div class="row">
            <div class="col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-info"><i class="fas fa-calendar-day"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Today's Expense</span>
                        <span class="info-box-number"><?php echo CURRENCY . number_format($stats['today']); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-success"><i class="fas fa-calendar-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">This Month</span>
                        <span class="info-box-number"><?php echo CURRENCY . number_format($stats['month']); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box shadow-sm">
                    <span class="info-box-icon bg-warning"><i class="fas fa-wallet"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total All Time</span>
                        <span class="info-box-number"><?php echo CURRENCY . number_format($stats['total']); ?></span>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="info-box shadow-sm no-print" style="cursor:pointer" data-toggle="modal" data-target="#chartModal">
                    <span class="info-box-icon bg-danger"><i class="fas fa-chart-pie"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Category Split</span>
                        <span class="info-box-number">View Chart</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-primary shadow">
            <div class="card-body p-0">
                <table class="table table-hover mb-0" id="expenseTable">
                    <thead class="bg-light">
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($expenses->num_rows > 0): ?>
                            <?php while($row = $expenses->fetch_assoc()): ?>
                            <tr>
                                <td><i class="far fa-calendar-alt text-muted mr-1"></i> <?php echo date('d M Y', strtotime($row['expense_date'])); ?></td>
                                <td><span class="badge badge-info px-2 py-1"><?php echo $row['category']; ?></span></td>
                                <td class="font-weight-bold text-danger"><?php echo CURRENCY . number_format($row['amount'], 2); ?></td>
                                <td class="text-muted small"><?php echo $row['description']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center py-4">No records found for this period.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Chart Modal -->
<div class="modal fade" id="chartModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Expense Breakdown by Category</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <canvas id="expenseCategoryChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('expenseCategoryChart').getContext('2d');
    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($cat_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($cat_data); ?>,
                backgroundColor: ['#ff5532', '#28a745', '#ffc107', '#17a2b8', '#6610f2', '#e83e8c', '#343a40']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});

function exportToCSV() {
    let table = document.getElementById("expenseTable");
    let rows = Array.from(table.rows);
    let csvContent = rows.map(row => Array.from(row.cells).map(cell => `"${cell.innerText}"`).join(",")).join("\n");
    let blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    let link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "Expense_Report_<?php echo $month_filter; ?>.csv";
    link.click();
}
</script>

<?php include '../includes/footer.php'; ?>

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="list.php" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Record New Expense</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" class="form-control" required>
                            <option value="Electricity">Electricity</option>
                            <option value="Salary">Salary</option>
                            <option value="Rent">Rent</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Internet">Internet</option>
                            <option value="Office Supplies">Office Supplies</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amount (<?php echo CURRENCY; ?>)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="expense_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Expense</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
