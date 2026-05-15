<?php
require_once '../config/database.php';
require_once '../includes/auth.php';

checkLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_name = $conn->real_escape_string($_POST['client_name']);
    $client_phone = $conn->real_escape_string($_POST['client_phone']);
    $client_address = $conn->real_escape_string($_POST['client_address']);
    $service_description = $conn->real_escape_string($_POST['service_description']);
    $amount = (float)$_POST['amount'];
    $tax = (float)$_POST['tax'];
    $payment_mode = $conn->real_escape_string($_POST['payment_mode']);
    $invoice_date = $_POST['invoice_date'];
    
    $total_amount = $amount + $tax;
    $invoice_no = "CL-" . date('Ymd') . "-" . rand(100, 999);

    $stmt = $conn->prepare("INSERT INTO client_invoices (invoice_no, client_name, client_phone, client_address, service_description, amount, tax, total_amount, payment_mode, invoice_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssddsss", $invoice_no, $client_name, $client_phone, $client_address, $service_description, $amount, $tax, $total_amount, $payment_mode, $invoice_date);

    if ($stmt->execute()) {
        flash("Client invoice created successfully!");
        redirect('invoices/client_list.php');
    } else {
        flash("Error: " . $conn->error, "danger");
    }
}

include '../includes/header.php';
?>

<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0" style="font-weight: 700; color: #ff5532;">Create Client Invoice</h1>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <form action="client_create.php" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Client/Company Name</label>
                                <input type="text" name="client_name" class="form-control" required placeholder="e.g. Acme Corp">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Contact Number</label>
                                <input type="text" name="client_phone" class="form-control" placeholder="e.g. +91 98765 43210">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Client Address</label>
                        <textarea name="client_address" class="form-control" rows="2"></textarea>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label>Service/Product Description</label>
                        <textarea name="service_description" class="form-control" rows="3" required placeholder="Describe what you are billing for..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Amount (<?php echo CURRENCY; ?>)</label>
                                <input type="number" step="0.01" name="amount" class="form-control" required id="amount">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tax (<?php echo CURRENCY; ?>)</label>
                                <input type="number" step="0.01" name="tax" class="form-control" value="0.00">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Payment Mode</label>
                                <select name="payment_mode" class="form-control">
                                    <option value="Cash">Cash</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="UPI/Digital">UPI / Digital</option>
                                    <option value="Cheque">Cheque</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Invoice Date</label>
                                <input type="date" name="invoice_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="text-right mt-4">
                        <a href="client_list.php" class="btn btn-secondary px-4">Cancel</a>
                        <button type="submit" class="btn btn-success px-4 ml-2">Generate Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
