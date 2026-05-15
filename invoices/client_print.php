<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

if (!isset($_GET['id'])) {
    die("Invoice ID missing");
}

$id = (int)$_GET['id'];
$res = $conn->query("SELECT * FROM client_invoices WHERE id = $id");
if ($res->num_rows === 0) {
    die("Invoice not found");
}
$inv = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Client Invoice - <?php echo $inv['invoice_no']; ?></title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #333; line-height: 1.6; background: #eee; padding: 30px; }
        .invoice-box { max-width: 850px; margin: auto; padding: 40px; background: #fff; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #ff5532; padding-bottom: 20px; margin-bottom: 30px; }
        .header img { height: 75px; }
        .header h1 { margin: 0; color: #ff5532; font-weight: 900; }
        
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .info-table td { vertical-align: top; padding: 10px 0; }
        .info-table h3 { margin: 0 0 10px 0; color: #555; text-transform: uppercase; font-size: 14px; letter-spacing: 1px; }

        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .items-table th { background: #f8f9fa; border-bottom: 2px solid #eee; padding: 15px; text-align: left; font-size: 13px; color: #666; }
        .items-table td { padding: 15px; border-bottom: 1px solid #eee; font-size: 15px; }
        
        .totals-section { float: right; width: 300px; margin-top: 20px; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; }
        .total-row.grand-total { border-top: 2px solid #ff5532; margin-top: 10px; padding-top: 15px; font-weight: 900; font-size: 20px; color: #ff5532; }

        .signature-section { margin-top: 80px; display: flex; justify-content: space-between; }
        .sig-box { text-align: center; border-top: 1px solid #ddd; width: 220px; padding-top: 10px; font-size: 14px; color: #666; }

        .no-print { text-align: center; margin-bottom: 25px; }
        .btn { padding: 12px 25px; text-decoration: none; border: none; border-radius: 5px; font-weight: bold; cursor: pointer; display: inline-block; }
        .btn-print { background: #ff5532; color: #fff; margin-right: 10px; }
        .btn-back { background: #333; color: #fff; }

        @media print {
            .no-print { display: none; }
            body { background: #fff; padding: 0; }
            .invoice-box { box-shadow: none; border: none; max-width: 100%; }
        }
    </style>
</head>
<body onload="window.print()">

<div class="no-print">
    <button onclick="window.print()" class="btn btn-print">Print Invoice</button>
    <a href="client_list.php" class="btn btn-back">Back to List</a>
</div>

<div class="invoice-box">
    <div class="header">
        <div>
            <img src="<?php echo BASE_URL; ?>image.png" alt="Logo">
            <p style="margin-top: 10px; font-weight: bold; font-size: 18px; color: #333;">Netcoder Technology - Dharamshala</p>
            <p style="margin: 0; font-size: 13px; color: #777;">
                Dari, Dharamshala, HP 176215<br>
                Contact: +91 98167 32055
            </p>
        </div>
        <div style="text-align: right;">
            <h1>TAX INVOICE</h1>
            <p style="margin: 5px 0;"><strong>Invoice #:</strong> <?php echo $inv['invoice_no']; ?></p>
            <p style="margin: 0;"><strong>Date:</strong> <?php echo date('F d, Y', strtotime($inv['invoice_date'])); ?></p>
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%">
                <h3>Bill To:</h3>
                <p style="margin: 0; font-size: 18px; font-weight: bold;"><?php echo $inv['client_name']; ?></p>
                <p style="margin: 5px 0; color: #555;">
                    <?php echo nl2br($inv['client_address']); ?><br>
                    <strong>Phone:</strong> <?php echo $inv['client_phone']; ?>
                </p>
            </td>
            <td style="text-align: right;">
                <h3>Payment Details:</h3>
                <p style="margin: 0;"><strong>Method:</strong> <?php echo $inv['payment_mode']; ?></p>
                <p style="margin: 5px 0; color: green; font-weight: bold;">Status: Paid / Disbursed</p>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Description</th>
                <th style="text-align: right; width: 150px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="height: 150px; vertical-align: top;">
                    <strong>Service Details:</strong><br>
                    <?php echo nl2br($inv['service_description']); ?>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <?php echo CURRENCY . number_format($inv['amount'], 2); ?>
                </td>
            </tr>
        </tbody>
    </table>

    <div style="overflow: hidden;">
        <div class="totals-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span><?php echo CURRENCY . number_format($inv['amount'], 2); ?></span>
            </div>
            <div class="total-row">
                <span>Taxes (GST/Service):</span>
                <span><?php echo CURRENCY . number_format($inv['tax'], 2); ?></span>
            </div>
            <div class="total-row grand-total">
                <span>Total Amount:</span>
                <span><?php echo CURRENCY . number_format($inv['total_amount'], 2); ?></span>
            </div>
        </div>
    </div>

    <p style="margin-top: 40px; font-size: 14px; color: #666;">
        <strong>Total in Words:</strong> Only <?php echo CURRENCY . number_format($inv['total_amount'], 2); ?> disbursed.
    </p>

    <div class="signature-section">
        <div class="sig-box">Client Signature</div>
        <div class="sig-box">Authorized Signatory</div>
    </div>

    <div style="margin-top: 60px; text-align: center; font-size: 11px; color: #aaa; border-top: 1px solid #eee; padding-top: 15px;">
        This is a computer-generated invoice and does not require a physical seal. <br>
        Thank you for choosing Netcoder Technology.
    </div>
</div>

</body>
</html>
