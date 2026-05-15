<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

// Auto-Migration: Ensure check_in_time column exists
$conn->query("ALTER TABLE attendance ADD COLUMN IF NOT EXISTS check_in_time TIME NULL AFTER attendance_date");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendance_date = $_POST['attendance_date'];
    $attendance_data = $_POST['attendance']; // Array of staff_id => status

    foreach ($attendance_data as $staff_id => $status) {
        $staff_id = (int)$staff_id;
        $check_in_time = ($status === 'present') ? date('H:i:s') : NULL;
        $check_in_sql = ($check_in_time) ? "'$check_in_time'" : "NULL";

        // Check if already marked
        $check = $conn->query("SELECT id FROM attendance WHERE staff_id = $staff_id AND attendance_date = '$attendance_date'");
        if ($check->num_rows > 0) {
            $conn->query("UPDATE attendance SET status = '$status', check_in_time = $check_in_sql WHERE staff_id = $staff_id AND attendance_date = '$attendance_date'");
        } else {
            $conn->query("INSERT INTO attendance (staff_id, status, attendance_date, check_in_time) VALUES ($staff_id, '$status', '$attendance_date', $check_in_sql)");
        }
    }
    flash("Staff attendance marked successfully!");
}

$selected_date = $_GET['date'] ?? date('Y-m-d');
$staff_query = "SELECT s.id, s.name, s.role, a.status as today_status, a.check_in_time 
               FROM staff s 
               LEFT JOIN attendance a ON s.id = a.staff_id AND a.attendance_date = '$selected_date'
               ORDER BY s.name ASC";
$staff_list = $conn->query($staff_query);
$current_date = date('Y-m-d');

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Staff Attendance</h1>
                <p class="text-muted"><i class="far fa-clock mr-1"></i> Current Time: <span id="liveClock" style="font-weight: bold; color: var(--first-color);"></span></p>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-primary card-outline shadow">
            <form action="attendance.php" method="POST">
                <div class="card-header bg-white">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label>Select Date</label>
                            <input type="date" name="attendance_date" class="form-control" value="<?php echo $selected_date; ?>" onchange="window.location.href='?date='+this.value" required>
                        </div>
                        <div class="col-md-9 text-right">
                            <button type="button" class="btn btn-outline-primary btn-sm mr-2" onclick="markAll('present')"><i class="fas fa-check-double"></i> Mark All Present</button>
                            <span class="badge badge-info p-2">Showing records for selected date</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Staff Member</th>
                                <th class="text-center">Logged Time</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($s = $staff_list->fetch_assoc()): ?>
                            <tr class="<?php echo $s['today_status'] ? 'table-secondary' : ''; ?>">
                                <td>
                                    <div class="font-weight-bold"><?php echo $s['name']; ?></div>
                                    <small class="text-muted"><?php echo strtoupper($s['role']); ?></small>
                                </td>
                                <td class="text-center">
                                    <?php if($s['check_in_time']): ?>
                                        <span class="badge badge-success p-2">
                                            <i class="fas fa-clock mr-1"></i> <?php echo date('h:i A', strtotime($s['check_in_time'])); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted small">Not Logged</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-outline-success btn-sm <?php echo ($s['today_status'] === 'present' || !$s['today_status']) ? 'active' : ''; ?>">
                                            <input type="radio" name="attendance[<?php echo $s['id']; ?>]" value="present" class="status-present" <?php echo ($s['today_status'] === 'present' || !$s['today_status']) ? 'checked' : ''; ?>> Present
                                        </label>
                                        <label class="btn btn-outline-danger btn-sm <?php echo ($s['today_status'] === 'absent') ? 'active' : ''; ?>">
                                            <input type="radio" name="attendance[<?php echo $s['id']; ?>]" value="absent" class="status-absent" <?php echo ($s['today_status'] === 'absent') ? 'checked' : ''; ?>> Absent
                                        </label>
                                        <label class="btn btn-outline-warning btn-sm <?php echo ($s['today_status'] === 'late') ? 'active' : ''; ?>">
                                            <input type="radio" name="attendance[<?php echo $s['id']; ?>]" value="late" class="status-late" <?php echo ($s['today_status'] === 'late') ? 'checked' : ''; ?>> Late
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">Save Attendance</button>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
function updateClock() {
    const now = new Date();
    const options = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
    document.getElementById('liveClock').innerText = now.toLocaleTimeString('en-IN', options);
}

function markAll(status) {
    document.querySelectorAll('.status-' + status).forEach(input => {
        input.checked = true;
        input.parentElement.classList.add('active');
        input.closest('.btn-group').querySelectorAll('label').forEach(label => {
            if (label !== input.parentElement) label.classList.remove('active');
        });
    });
}

setInterval(updateClock, 1000);
updateClock();
</script>

<?php include '../includes/footer.php'; ?>
