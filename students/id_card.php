<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

$id = (int)$_GET['id'];
$student_res = $conn->query("SELECT s.*, c.course_name FROM students s LEFT JOIN courses c ON s.course_id = c.id WHERE s.id = $id");
if ($student_res->num_rows == 0) {
    die("Student not found!");
}
$s = $student_res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ID Card - <?php echo $s['full_name']; ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .id-card-container { background: #fff; width: 350px; height: 500px; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2); position: relative; border: 1px solid #ddd; }
        .header { background: #ff5532; height: 140px; padding: 20px; text-align: center; color: white; position: relative; }
        .header h2 { margin: 10px 0 5px; font-size: 20px; text-transform: uppercase; letter-spacing: 1px; }
        .header p { margin: 0; font-size: 12px; opacity: 0.9; }
        .photo-container { position: absolute; top: 90px; left: 50%; transform: translateX(-50%); width: 120px; height: 120px; background: #fff; border-radius: 50%; padding: 5px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .photo-container img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
        .content { margin-top: 80px; padding: 20px; text-align: center; }
        .student-name { font-size: 22px; font-weight: 800; color: #333; margin-bottom: 5px; }
        .student-role { font-size: 14px; font-weight: 600; color: #ff5532; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 20px; }
        .details { text-align: left; background: #f9f9f9; padding: 15px; border-radius: 10px; font-size: 13px; }
        .detail-row { display: flex; justify-content: space-between; margin-bottom: 8px; border-bottom: 1px dashed #eee; padding-bottom: 4px; }
        .detail-row .label { color: #888; font-weight: 600; }
        .detail-row .value { color: #333; font-weight: 700; }
        .footer { position: absolute; bottom: 0; width: 100%; background: #333; color: #fff; text-align: center; padding: 10px 0; font-size: 11px; }
        .qr-placeholder { position: absolute; bottom: 40px; right: 20px; width: 60px; height: 60px; background: #eee; display: flex; align-items: center; justify-content: center; font-size: 8px; color: #999; border: 1px solid #ddd; }
        @media print {
            body { background: none; }
            .no-print { display: none; }
            .id-card-container { box-shadow: none; border: 1px solid #000; }
        }
    </style>
</head>
<body>

<div class="id-card-container">
    <div class="header">
        <h2><?php echo APP_NAME; ?></h2>
        <p>Innovation in Education</p>
    </div>
    
    <div class="photo-container">
        <?php if($s['photo']): ?>
            <img src="<?php echo BASE_URL . $s['photo']; ?>" alt="Photo">
        <?php else: ?>
            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($s['full_name']); ?>&background=ff5532&color=fff&size=128" alt="Avatar">
        <?php endif; ?>
    </div>

    <div class="content">
        <div class="student-name"><?php echo $s['full_name']; ?></div>
        <div class="student-role">Student</div>
        
        <div class="details">
            <div class="detail-row">
                <span class="label">Student ID:</span>
                <span class="value"><?php echo $s['student_id']; ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Course:</span>
                <span class="value"><?php echo $s['course_name'] ?: 'N/A'; ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Phone:</span>
                <span class="value"><?php echo $s['phone']; ?></span>
            </div>
            <div class="detail-row">
                <span class="label">Valid Upto:</span>
                <span class="value"><?php echo date('Y', strtotime('+1 year')) . "-12-31"; ?></span>
            </div>
        </div>
    </div>

    <div class="qr-placeholder">
        <div style="text-align: center;">Verified<br>System</div>
    </div>

    <div class="footer">
        Website: netcodererp.com | Support: support@netcoder.com
    </div>
</div>

<div class="no-print" style="position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%);">
    <button onclick="window.print()" style="padding: 10px 30px; background: #ff5532; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; box-shadow: 0 5px 15px rgba(255,85,50,0.4);">
        <i class="fas fa-print"></i> Print ID Card
    </button>
</div>

</body>
</html>
