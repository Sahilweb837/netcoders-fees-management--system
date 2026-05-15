<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

if (!isset($_GET['id'])) {
    die("Payment ID missing");
}

$payment_id = (int)$_GET['id'];
$query = "SELECT p.*, s.name as staff_name, s.role, s.phone, s.email, s.joining_date 
          FROM staff_payments p 
          JOIN staff s ON p.staff_id = s.id 
          WHERE p.id = $payment_id";
$res = $conn->query($query);

if ($res->num_rows == 0) {
    die("Payment record not found");
}

$p = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Salary Slip - <?php echo $p['staff_name']; ?></title>
    <style>
        body { font-family: 'Inter', sans-serif; color: #333; line-height: 1.6; padding: 40px; background: #f4f4f4; }
        .slip-box { max-width: 800px; margin: auto; padding: 40px; background: #fff; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); border: 1px solid #eee; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ff5532; padding-bottom: 20px; margin-bottom: 30px; }
        .header img { max-height: 70px; }
        .header-info { text-align: right; }
        .header-info h2 { margin: 0; color: #ff5532; font-size: 24px; font-weight: 800; }
        
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; margin-bottom: 40px; }
        .info-section h4 { text-transform: uppercase; font-size: 12px; color: #888; letter-spacing: 1px; margin-bottom: 10px; border-bottom: 1px solid #f0f0f0; padding-bottom: 5px; }
        .info-section p { margin: 3px 0; font-weight: 600; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        th { background: #fdfdfd; text-align: left; padding: 15px; border-bottom: 2px solid #eee; font-size: 14px; text-transform: uppercase; color: #666; }
        td { padding: 15px; border-bottom: 1px solid #f9f9f9; font-size: 15px; }
        
        .total-row { background: #fffaf9; font-weight: 800; font-size: 18px; color: #ff5532; }
        
        .footer { margin-top: 60px; display: flex; justify-content: space-between; text-align: center; font-size: 13px; }
        .signature { border-top: 1px solid #ddd; width: 200px; padding-top: 8px; margin-top: 50px; }
        
        .no-print { text-align: center; margin-bottom: 30px; }
        .btn { padding: 12px 25px; border: none; border-radius: 6px; cursor: pointer; font-weight: 700; text-decoration: none; display: inline-block; }
        .btn-print { background: #ff5532; color: #fff; margin-right: 10px; }
        .btn-back { background: #333; color: #fff; }

        @media print {
            .no-print { display: none; }
            body { background: #fff; padding: 0; }
            .slip-box { box-shadow: none; border: none; width: 100%; max-width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print">
    <button onclick="window.print()" class="btn btn-print">Print Salary Slip</button>
    <a href="salary.php" class="btn btn-back">Back to List</a>
</div>

<div class="slip-box">
    <div class="header">
        <img src="<?php echo BASE_URL; ?>image.png" alt="Logo">
        <div class="header-info">
            <h2>SALARY SLIP</h2>
            <p style="margin:0; color: #666;">For the month of <strong><?php echo $p['payment_month']; ?></strong></p>
        </div>
    </div>

    <div class="info-grid">
        <div class="info-section">
            <h4>Employee Details</h4>
            <p><?php echo $p['staff_name']; ?></p>
            <p>Designation: <?php echo $p['role']; ?></p>
            <p>Phone: <?php echo $p['phone']; ?></p>
            <p>Joining Date: <?php echo date('d M, Y', strtotime($p['joining_date'])); ?></p>
        </div>
        <div class="info-section">
            <h4>Payment Info</h4>
            <p>Slip ID: #SLP-<?php echo str_pad($p['id'], 5, '0', STR_PAD_LEFT); ?></p>
            <p>Payment Date: <?php echo date('d M, Y', strtotime($p['payment_date'])); ?></p>
            <p>Method: <?php echo $p['payment_method']; ?></p>
            <p>Status: <span style="color: green;">Disbursed</span></p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Basic Salary for <?php echo $p['payment_month']; ?></td>
                <td style="text-align: right;"><?php echo CURRENCY . number_format($p['amount'], 2); ?></td>
            </tr>
            <tr>
                <td>Allowances / Bonus</td>
                <td style="text-align: right;"><?php echo CURRENCY; ?> 0.00</td>
            </tr>
            <tr>
                <td>Deductions (Professional Tax / Other)</td>
                <td style="text-align: right;"><?php echo CURRENCY; ?> 0.00</td>
            </tr>
            <tr class="total-row">
                <td>Net Salary Payable</td>
                <td style="text-align: right;"><?php echo CURRENCY . number_format($p['amount'], 2); ?></td>
            </tr>
        </tbody>
    </table>

    <p style="font-size: 13px; color: #666;"><strong>Remarks:</strong> <?php echo $p['remarks'] ? $p['remarks'] : 'Monthly salary disbursement.'; ?></p>

    <div class="footer">
        <div>
            <div class="signature">Employee Signature</div>
        </div>
        <div>
            <div class="signature">Authorized Signatory</div>
        </div>
    </div>

    <div style="margin-top: 40px; text-align: center; font-size: 11px; color: #999; border-top: 1px solid #f0f0f0; padding-top: 15px;">
        This is a computer-generated salary slip and does not require a physical seal. <br>
        <strong>Netcoder Technology - Dharamshala</strong>
    </div>
</div>

</body>
</html>
