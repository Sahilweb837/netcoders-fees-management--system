<?php
require_once 'config/database.php';
require_once 'includes/auth.php';

checkLogin();

// View Mode Toggle for Root Admin
$view_mode = $_SESSION['view_mode'] ?? 'global';
if (isset($_GET['toggle_view']) && $_SESSION['role'] === 'root_admin') {
    $view_mode = ($view_mode === 'global') ? 'branch' : 'global';
    $_SESSION['view_mode'] = $view_mode;
    redirect('index.php');
}

// Fetch statistics (Global or Branch based on role/view_mode)
$branch_filter = "";
if ($_SESSION['role'] === 'super_admin' || $view_mode === 'branch') {
    $branch_id = $_SESSION['branch_id'] ?? 0;
    $branch_filter = " WHERE branch_id = $branch_id";
}

$total_students_res = $conn->query("SELECT COUNT(*) FROM students" . $branch_filter);
$total_students = ($total_students_res) ? $total_students_res->fetch_row()[0] : 0;

$total_courses_res = $conn->query("SELECT COUNT(*) FROM courses");
$total_courses = ($total_courses_res) ? $total_courses_res->fetch_row()[0] : 0;

$total_staff_res = $conn->query("SELECT COUNT(*) FROM staff" . $branch_filter);
$total_staff = ($total_staff_res) ? $total_staff_res->fetch_row()[0] : 0;

$revenue_query = "SELECT SUM(amount) FROM payments" . ($branch_filter ? " WHERE student_id IN (SELECT id FROM students $branch_filter)" : "");
$total_revenue_res = $conn->query($revenue_query);
$total_revenue = ($total_revenue_res) ? $total_revenue_res->fetch_row()[0] : 0;

$pending_fees_res = $conn->query("SELECT SUM(due_fee) FROM students" . $branch_filter);
$pending_fees = ($pending_fees_res) ? $pending_fees_res->fetch_row()[0] : 0;

// Student Enrollment Trends (Last 6 Months)
$enrollment_query = "SELECT DATE_FORMAT(admission_date, '%b %Y') as month, COUNT(*) as count 
                    FROM students 
                    " . $branch_filter . "
                    GROUP BY month 
                    ORDER BY admission_date DESC LIMIT 6";
$enrollment_res = $conn->query($enrollment_query);
$enrollment_labels = [];
$enrollment_data = [];
while($row = $enrollment_res->fetch_assoc()) {
    $enrollment_labels[] = $row['month'];
    $enrollment_data[] = $row['count'];
}
$enrollment_labels = array_reverse($enrollment_labels);
$enrollment_data = array_reverse($enrollment_data);

// Course Distribution
$course_dist_query = "SELECT c.course_name, COUNT(s.id) as count 
                      FROM courses c 
                      LEFT JOIN students s ON c.id = s.course_id 
                      " . ($branch_filter ? str_replace('WHERE', 'AND', $branch_filter) : "") . "
                      GROUP BY c.id";
$course_dist_res = $conn->query($course_dist_query);
$course_labels = [];
$course_data = [];
while($row = $course_dist_res->fetch_assoc()) {
    $course_labels[] = $row['course_name'];
    $course_data[] = $row['count'];
}

include 'includes/header.php';
?>

<style>
    .content-wrapper { background: #ffffff !important; }
    .content-header h1 { color: #000000 !important; }
    .card { border: 1px solid #eee; box-shadow: none !important; }
    .small-box { border-radius: 15px !important; overflow: hidden; }
    .text-muted { color: #555 !important; }
</style>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0" style="font-weight: 800; color: #000;">
                    <img src="<?php echo BASE_URL; ?>image.png" alt="Logo" style="height: 50px; margin-right: 15px; margin-top: -5px;">
                    Netcoder Technology - Dharamshala
                </h1>
                <p class="text-muted">Viewing: <strong><?php echo strtoupper($view_mode); ?> DATA</strong></p>
            </div>
            <?php if ($_SESSION['role'] === 'root_admin'): ?>
            <div class="col-sm-6 text-right">
                <a href="?toggle_view=1" class="btn btn-primary btn-sm px-4 shadow">
                    <i class="fas fa-sync-alt mr-1"></i> Switch to <?php echo ($view_mode === 'global') ? 'Branch View' : 'Global System'; ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box" style="background-color: var(--first-color); color: #fff;">
                    <div class="inner">
                        <h3><?php echo $total_students; ?></h3>
                        <p>Total Students</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <a href="students/list.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-dark">
                    <div class="inner">
                        <h3><?php echo $total_courses; ?></h3>
                        <p>Total Courses</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-book"></i>
                    </div>
                    <a href="courses/list.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="small-box" style="background-color: #ff8c00; color: #fff;">
                    <div class="inner">
                        <h3><?php echo $total_staff; ?></h3>
                        <p>Total Staff</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <a href="staff/list.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <?php if ($_SESSION['role'] !== 'root_admin'): ?>
            <div class="col-lg-3 col-6">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3><?php echo CURRENCY . number_format($total_revenue); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <a href="reports/revenue.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-white border-0">
                        <h3 class="card-title text-bold"><i class="fas fa-chart-bar mr-2 text-primary"></i>Student Enrollment (6 Months)</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="enrollmentChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-white border-0">
                        <h3 class="card-title text-bold"><i class="fas fa-chart-pie mr-2 text-warning"></i>Course Distribution</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="courseChart" style="min-height: 300px; height: 300px; max-height: 300px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-white border-0">
                        <h3 class="card-title text-bold">Recent Admissions</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Student Name</th>
                                    <th>Course</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $recent_students = $conn->query("SELECT s.*, c.course_name FROM students s LEFT JOIN courses c ON s.course_id = c.id $branch_filter ORDER BY s.id DESC LIMIT 5");
                                while($row = $recent_students->fetch_assoc()) {
                                    echo "<tr>
                                        <td>{$row['student_id']}</td>
                                        <td>{$row['full_name']}</td>
                                        <td>{$row['course_name']}</td>
                                        <td>" . date('d M Y', strtotime($row['admission_date'])) . "</td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php if ($_SESSION['role'] !== 'root_admin'): ?>
            <div class="col-md-4">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white">
                        <h3 class="card-title">Financial Health</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="revenueChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<?php if ($_SESSION['role'] !== 'root_admin'): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Enrollment Trends Bar Chart
        const enrollmentCtx = document.getElementById('enrollmentChart').getContext('2d');
        new Chart(enrollmentCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($enrollment_labels); ?>,
                datasets: [{
                    label: 'New Admissions',
                    data: <?php echo json_encode($enrollment_data); ?>,
                    backgroundColor: 'rgba(255, 85, 50, 0.7)',
                    borderColor: 'rgba(255, 85, 50, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // Course Distribution Doughnut Chart
        const courseCtx = document.getElementById('courseChart').getContext('2d');
        new Chart(courseCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($course_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($course_data); ?>,
                    backgroundColor: ['#ff5532', '#28a745', '#ffc107', '#17a2b8', '#6610f2', '#e83e8c', '#343a40'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'right' } }
            }
        });

        // Revenue Chart (Existing)
        const revenueEl = document.getElementById('revenueChart');
        if (revenueEl) {
            const ctx = revenueEl.getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Revenue', 'Pending'],
                    datasets: [{
                        data: [<?php echo $total_revenue; ?>, <?php echo $pending_fees; ?>],
                        backgroundColor: ['#28a745', '#dc3545'],
                    }]
                }
            });
        }
    });
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
