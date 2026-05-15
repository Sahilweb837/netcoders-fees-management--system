<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

if (!isset($_GET['id'])) {
    die("Invoice ID not found.");
}

$id = (int)$_GET['id'];
$query = $conn->query("SELECT i.*, s.full_name, s.student_id as s_id, s.phone, s.address, c.course_name 
                       FROM invoices i 
                       JOIN students s ON i.student_id = s.id 
                       LEFT JOIN courses c ON s.course_id = c.id
                       WHERE i.id = $id");

if ($query->num_rows === 0) {
    die("Invoice not found.");
}

$inv = $query->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - <?php echo $inv['invoice_no']; ?></title>
    <style>
        :root { --main-color: #ff5532; }
        body { font-family: 'Inter', sans-serif; color: #444; background: #f4f4f4; }
        .invoice-box {
            max-width: 800px;
            margin: 40px auto;
            padding: 40px;
            border: 1px solid #ddd;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            font-size: 14px;
            line-height: 24px;
            background: #fff;
            border-radius: 12px;
        }
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        .invoice-box table td { padding: 10px; vertical-align: top; }
        .invoice-box table tr td:nth-child(2) { text-align: right; }
        .invoice-box table tr.top table td { padding-bottom: 30px; }
        .invoice-box table tr.top table td.title { font-size: 36px; line-height: 36px; color: var(--main-color); font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
        .invoice-box table tr.information table td { padding-bottom: 40px; }
        .invoice-box table tr.heading td { background: #f8f9fa; border-bottom: 2px solid var(--main-color); font-weight: bold; color: #333; }
        .invoice-box table tr.item td { border-bottom: 1px solid #eee; }
        .invoice-box table tr.item.last td { border-bottom: none; }
        .invoice-box table tr.total td:nth-child(2) { border-top: 3px solid var(--main-color); font-weight: 900; font-size: 20px; color: #000; }
        .badge-paid { background: #28a745; color: #fff; padding: 5px 15px; border-radius: 50px; font-weight: bold; text-transform: uppercase; font-size: 12px; display: inline-block; }
        @media print {
            .no-print { display: none; }
            body { background: #fff; margin: 0; }
            .invoice-box { border: none; box-shadow: none; width: 100%; max-width: 100%; margin: 0; padding: 20px; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print" style="text-align: center; margin-bottom: 20px;">
    <button onclick="window.print()" style="padding: 12px 25px; cursor: pointer; background: #ff5532; color: #fff; border: none; border-radius: 5px; font-weight: bold;">Print Receipt</button>
    <a href="list.php" style="padding: 12px 25px; text-decoration: none; background: #333; color: #fff; border-radius: 5px; font-weight: bold;">Back to List</a>
</div>

<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2">
                <table>
                    <tr>
                        <td class="title">
                            <img src="<?php echo BASE_URL; ?>image.png" style="width:100%; max-width:180px;">
                        </td>
                        <td>
                            <h2 style="margin:0; color: #ff5532;">FEE RECEIPT</h2>
                            Receipt #: <?php echo $inv['invoice_no']; ?><br>
                            Date: <?php echo date('F d, Y', strtotime($inv['invoice_date'])); ?><br>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="information">
            <td colspan="2">
                <table>
                    <tr>
                        <td>
                            <strong><?php echo APP_NAME; ?></strong><br>
                            Professional Training Institute<br>
                            Admin Office, New Delhi<br>
                            Contact: +91 9876543210
                        </td>
                        <td>
                            <strong>Student Details:</strong><br>
                            <?php echo $inv['full_name']; ?> (<?php echo $inv['s_id']; ?>)<br>
                            <?php echo $inv['phone']; ?><br>
                            <?php echo $inv['address']; ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="heading">
            <td>Payment Details</td>
            <td>Status</td>
        </tr>
        <tr class="details">
            <td>Method: <?php echo $inv['payment_mode']; ?></td>
            <td><span class="badge-paid">Paid</span></td>
        </tr>

        <tr class="heading">
            <td>Description</td>
            <td>Amount</td>
        </tr>
        <tr class="item">
            <td>Admission/Monthly Fee for <?php echo $inv['course_name']; ?></td>
            <td><?php echo CURRENCY . number_format($inv['amount'], 2); ?></td>
        </tr>
        <tr class="item">
            <td>Service Charges / Tax</td>
            <td><?php echo CURRENCY . number_format($inv['tax'], 2); ?></td>
        </tr>
        <tr class="item last">
            <td>Scholarship / Discount</td>
            <td>- <?php echo CURRENCY . number_format($inv['discount'], 2); ?></td>
        </tr>

        <tr class="total">
            <td></td>
            <td>Net Paid: <?php echo CURRENCY . number_format($inv['final_amount'], 2); ?></td>
        </tr>
    </table>
    <div style="margin-top: 60px; display: flex; justify-content: space-between; font-size: 13px;">
        <div style="text-align: center; border-top: 1px solid #ddd; width: 200px; padding-top: 5px;">Student Signature</div>
        <div style="text-align: center; border-top: 1px solid #ddd; width: 200px; padding-top: 5px;">Authorized Signatory</div>
    </div>
    <div style="margin-top: 40px; text-align: center; font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 10px;">
        Thank you for choosing <?php echo APP_NAME; ?>. This is a computer-generated fee receipt.
    </div>
</div>

</body>
</html>
