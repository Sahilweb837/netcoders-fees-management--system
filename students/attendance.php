<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

// Auto-Migration: Ensure check_in_time column exists
$conn->query("ALTER TABLE attendance ADD COLUMN IF NOT EXISTS check_in_time TIME NULL AFTER attendance_date");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendance_date = $_POST['attendance_date'];
    $attendance_data = $_POST['attendance']; // Array of student_id => status
    $fine_amount = 50.00;

    foreach ($attendance_data as $student_id => $status) {
        $student_id = (int)$student_id;
        $applied_fine = ($status === 'absent') ? $fine_amount : 0;
        $check_in_time = ($status === 'present') ? date('H:i:s') : NULL;

        // Check if already marked
        $check = $conn->query("SELECT id, fine_amount FROM attendance WHERE student_id = $student_id AND attendance_date = '$attendance_date'");
        
        if ($check->num_rows > 0) {
            $old_attendance = $check->fetch_assoc();
            $old_fine = $old_attendance['fine_amount'];
            
            // Update attendance
            $check_in_sql = ($check_in_time) ? "'$check_in_time'" : "NULL";
            $conn->query("UPDATE attendance SET status = '$status', fine_amount = $applied_fine, check_in_time = $check_in_sql WHERE student_id = $student_id AND attendance_date = '$attendance_date'");
            
            // Adjust student due if fine changed
            if ($old_fine != $applied_fine) {
                $diff = $applied_fine - $old_fine;
                $conn->query("UPDATE students SET due_fee = due_fee + $diff WHERE id = $student_id");
            }
        } else {
            // Insert new attendance
            $check_in_sql = ($check_in_time) ? "'$check_in_time'" : "NULL";
            $conn->query("INSERT INTO attendance (student_id, status, fine_amount, attendance_date, check_in_time) VALUES ($student_id, '$status', $applied_fine, '$attendance_date', $check_in_sql)");
            
            // Apply fine to student due if absent
            if ($applied_fine > 0) {
                $conn->query("UPDATE students SET due_fee = due_fee + $applied_fine WHERE id = $student_id");
                $conn->query("INSERT INTO fines (student_id, amount, reason, date_applied) VALUES ($student_id, $applied_fine, 'Absent Fine ($attendance_date)', '$attendance_date')");
            }
        }
    }
    flash("Student attendance marked! Absent students fined " . CURRENCY . "$fine_amount.");
}

$selected_date = $_GET['date'] ?? date('Y-m-d');
$students_query = "SELECT s.id, s.student_id, s.full_name, a.status as today_status, a.check_in_time 
                  FROM students s 
                  LEFT JOIN attendance a ON s.id = a.student_id AND a.attendance_date = '$selected_date'
                  WHERE s.status = 1 
                  ORDER BY s.full_name ASC";
$students_list = $conn->query($students_query);
$current_date = date('Y-m-d');

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Student Attendance</h1>
                <p class="text-muted"><i class="far fa-clock mr-1"></i> Current Time: <span id="liveClock" style="font-weight: bold; color: var(--first-color);"></span></p>
            </div>
        </div>
    </div>
</div>
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
        // Uncheck others in same group
        input.closest('.btn-group').querySelectorAll('label').forEach(label => {
            if (label !== input.parentElement) label.classList.remove('active');
        });
    });
}

setInterval(updateClock, 1000);
updateClock();
</script>

<section class="content">
    <div class="container-fluid">
        <div class="card card-orange card-outline">
            <form action="attendance.php" method="POST">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <label>Select Date</label>
                            <input type="date" name="attendance_date" id="dateSelector" class="form-control" value="<?php echo $selected_date; ?>" onchange="window.location.href='?date='+this.value" required>
                        </div>
                        <div class="col-md-9 text-right">
                            <button type="button" class="btn btn-outline-success btn-sm mr-2" onclick="markAll('present')"><i class="fas fa-check-double"></i> Mark All Present</button>
                            <span class="badge badge-danger p-2">Note: Absent students will be automatically fined <?php echo CURRENCY; ?>50</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-striped table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Student Info</th>
                                <th class="text-center">Marked Time</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($students_list->num_rows > 0): ?>
                                <?php while($s = $students_list->fetch_assoc()): ?>
                                <tr class="<?php echo $s['today_status'] ? 'table-secondary' : ''; ?>">
                                    <td>
                                        <div class="font-weight-bold"><?php echo $s['full_name']; ?></div>
                                        <small class="text-muted"><?php echo $s['student_id']; ?></small>
                                    </td>
                                    <td class="text-center">
                                        <?php if($s['check_in_time']): ?>
                                            <span class="badge badge-info p-2">
                                                <i class="fas fa-clock mr-1"></i> <?php echo date('h:i A', strtotime($s['check_in_time'])); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted small">Not Marked</span>
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
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="fas fa-user-slash fa-3x mb-3 d-block"></i>
                                        <h5>No Active Students Found</h5>
                                        <p>Make sure you have registered students and their status is set to 'Active'.</p>
                                        <a href="admission.php" class="btn btn-primary mt-2">Add Student</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary btn-lg px-5">Mark Attendance & Fines</button>
                </div>
            </form>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
