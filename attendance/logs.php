    <?php
    require_once '../config/database.php';
    require_once '../includes/auth.php';

    checkLogin();

    $type = $_GET['type'] ?? 'student';
    $where = "WHERE 1=1";

    if ($type === 'student') {
        $query = "SELECT a.*, s.full_name as name, s.student_id as identifier 
                FROM attendance a 
                JOIN students s ON a.student_id = s.id 
                ORDER BY a.attendance_date DESC, a.check_in_time DESC";
    } else {
        $query = "SELECT a.*, st.name, st.phone as identifier 
                FROM attendance a 
                JOIN staff st ON a.staff_id = st.id 
                ORDER BY a.attendance_date DESC, a.check_in_time DESC";
    }

    // Handle Check-out Action
    if (isset($_GET['checkout_id'])) {
        $c_id = (int)$_GET['checkout_id'];
        $now_time = date('H:i:s');
        $conn->query("UPDATE attendance SET check_out_time = '$now_time' WHERE id = $c_id");
        flash("Checked out successfully at $now_time");
        redirect('attendance/logs.php?type=' . $type);
    }

    $logs = $conn->query($query);

    include '../includes/header.php';
    ?>

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Attendance Logs (<?php echo ucfirst($type); ?>)</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <div class="btn-group shadow-sm">
                        <a href="?type=student" class="btn btn-<?php echo ($type === 'student') ? 'primary' : 'outline-primary'; ?>">Student Logs</a>
                        <a href="?type=staff" class="btn btn-<?php echo ($type === 'staff') ? 'primary' : 'outline-primary'; ?>">Staff Logs</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-body p-0">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Date</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th><?php echo ($type === 'student') ? 'Student ID' : 'Staff Phone'; ?></th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Fine</th>
                                <th class="no-print">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($logs->num_rows > 0): ?>
                                <?php while($row = $logs->fetch_assoc()): ?>
                                    <tr>
                                        <td><i class="far fa-calendar-alt mr-1 text-muted"></i> <?php echo date('d M, Y', strtotime($row['attendance_date'])); ?></td>
                                        <td>
                                            <span class="text-success font-weight-bold">
                                                <i class="fas fa-sign-in-alt mr-1"></i> 
                                                <?php echo $row['check_in_time'] ? date('h:i A', strtotime($row['check_in_time'])) : '--:--'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-danger font-weight-bold">
                                                <i class="fas fa-sign-out-alt mr-1"></i> 
                                                <?php echo $row['check_out_time'] ? date('h:i A', strtotime($row['check_out_time'])) : '--:--'; ?>
                                            </span>
                                        </td>
                                        <td><code><?php echo $row['identifier']; ?></code></td>
                                        <td><strong><?php echo $row['name']; ?></strong></td>
                                        <td>
                                            <?php 
                                            $badge = 'success';
                                            if($row['status'] == 'absent') $badge = 'danger';
                                            if($row['status'] == 'late') $badge = 'warning';
                                            if($row['status'] == 'leave') $badge = 'info';
                                            ?>
                                            <span class="badge badge-<?php echo $badge; ?> px-3 py-2" style="text-transform: uppercase;"><?php echo $row['status']; ?></span>
                                        </td>
                                        <td>
                                            <?php if($row['fine_amount'] > 0): ?>
                                                <span class="text-danger font-weight-bold"><?php echo CURRENCY . $row['fine_amount']; ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="no-print">
                                            <?php if(!$row['check_out_time'] && $row['status'] == 'present' && $row['attendance_date'] == date('Y-m-d')): ?>
                                                <a href="?type=<?php echo $type; ?>&checkout_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger">
                                                    Checkout <i class="fas fa-walking ml-1"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted small">Completed</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center py-5 text-muted">No attendance logs found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <?php include '../includes/footer.php'; ?>
