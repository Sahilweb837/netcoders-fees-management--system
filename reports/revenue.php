<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

// Fetch monthly revenue for chart
$monthly_revenue = $conn->query("
    SELECT DATE_FORMAT(payment_date, '%b %Y') as month, SUM(amount) as total 
    FROM payments 
    GROUP BY month 
    ORDER BY payment_date DESC 
    LIMIT 12
");

$months = [];
$totals = [];
while($row = $monthly_revenue->fetch_assoc()) {
    $months[] = $row['month'];
    $totals[] = $row['total'];
}

// Reverse to show chronological order
$months = array_reverse($months);
$totals = array_reverse($totals);

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Revenue Reports</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Monthly Revenue Analysis</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="monthlyChart" style="min-height: 400px; height: 400px; max-height: 400px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Transactions</h3>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Invoice No</th>
                                    <th>Student Name</th>
                                    <th>Amount</th>
                                    <th>Mode</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $payments = $conn->query("
                                    SELECT p.*, i.invoice_no, s.full_name 
                                    FROM payments p 
                                    JOIN invoices i ON p.invoice_id = i.id 
                                    JOIN students s ON p.student_id = s.id 
                                    ORDER BY p.payment_date DESC 
                                    LIMIT 20
                                ");
                                while($row = $payments->fetch_assoc()) {
                                    echo "<tr>
                                        <td>" . date('d M Y', strtotime($row['payment_date'])) . "</td>
                                        <td>{$row['invoice_no']}</td>
                                        <td>{$row['full_name']}</td>
                                        <td>" . CURRENCY . number_format($row['amount'], 2) . "</td>
                                        <td>" . (isset($row['payment_mode']) ? $row['payment_mode'] : 'Cash') . "</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: [{
                label: 'Monthly Revenue (<?php echo CURRENCY; ?>)',
                data: <?php echo json_encode($totals); ?>,
                backgroundColor: 'rgba(52, 152, 219, 0.7)',
                borderColor: 'rgba(52, 152, 219, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php include '../includes/footer.php'; ?>
