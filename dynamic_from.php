<?php
// Include the database connection
require 'config/database.php';

// Initialize variables
$inv_invoice_id = $inv_date = $inv_invoice = $inv_company = $inv_address = $inv_phone = $inv_postal_code = $inv_abn = '';
$inv_runsheet = $inv_customer_invoice_no = $inv_amount = $inv_sub_total = $inv_tax_rate = $inv_other_cost = $inv_total_cost = '';

// Handle the form submission for creating or updating the invoice
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data with null coalescing to handle missing fields
    $inv_date = mysqli_real_escape_string($conn, $_POST['date'] ?? '');
    $inv_invoice = mysqli_real_escape_string($conn, $_POST['invoice'] ?? '');
    $inv_company = mysqli_real_escape_string($conn, $_POST['company'] ?? '');
    $inv_address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
    $inv_phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $inv_postal_code = mysqli_real_escape_string($conn, $_POST['postal_code'] ?? '');
    $inv_abn = mysqli_real_escape_string($conn, $_POST['abn'] ?? '');
    $inv_runsheet = mysqli_real_escape_string($conn, $_POST['runsheet'] ?? '');
    $inv_customer_invoice_no = mysqli_real_escape_string($conn, $_POST['customer_invoice_no'] ?? '');
    $inv_amount = mysqli_real_escape_string($conn, $_POST['amount'] ?? '0');
    $inv_sub_total = mysqli_real_escape_string($conn, $_POST['sub_total'] ?? '0');
    $inv_tax_rate = mysqli_real_escape_string($conn, $_POST['tax_rate'] ?? '0');
    $inv_other_cost = mysqli_real_escape_string($conn, $_POST['other_cost'] ?? '0');
    $inv_total_cost = mysqli_real_escape_string($conn, $_POST['total_cost'] ?? '0');

    // If we are updating an existing invoice
    if (isset($_GET['invoice_id']) && is_numeric($_GET['invoice_id'])) {
        $inv_invoice_id = $_GET['invoice_id'];
        $sql = "UPDATE invoice SET date = '$inv_date', invoice = '$inv_invoice', company = '$inv_company', 
                address = '$inv_address', phone = '$inv_phone', postal_code = '$inv_postal_code', abn = '$inv_abn',
                runsheet = '$inv_runsheet', customer_invoice_no = '$inv_customer_invoice_no', 
                amount = '$inv_amount', sub_total = '$inv_sub_total', tax_rate = '$inv_tax_rate', 
                other_cost = '$inv_other_cost', total_cost = '$inv_total_cost' WHERE invoice_id = $inv_invoice_id";
    } else {
        // If we are creating a new invoice
        $sql = "INSERT INTO invoice (date, invoice, company, address, phone, postal_code, abn, runsheet, customer_invoice_no, 
                amount, sub_total, tax_rate, other_cost, total_cost)
                VALUES ('$inv_date', '$inv_invoice', '$inv_company', '$inv_address', '$inv_phone', '$inv_postal_code', 
                '$inv_abn', '$inv_runsheet', '$inv_customer_invoice_no','$inv_amount', '$inv_sub_total', 
                '$inv_tax_rate', '$inv_other_cost', '$inv_total_cost')";
    }

    // Execute query and handle the result
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Fetch invoice data for editing if 'invoice_id' is present in URL
if (isset($_GET['invoice_id']) && is_numeric($_GET['invoice_id'])) {
    $inv_invoice_id = $_GET['invoice_id'];
    $sql = "SELECT * FROM invoice WHERE invoice_id = $inv_invoice_id";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $invoice = mysqli_fetch_assoc($result);
        $inv_date = $invoice['date'] ?? '';
        $inv_invoice = $invoice['invoice'] ?? '';
        $inv_company = $invoice['company'] ?? '';
        $inv_address = $invoice['address'] ?? '';
        $inv_phone = $invoice['phone'] ?? '';
        $inv_postal_code = $invoice['postal_code'] ?? '';
        $inv_abn = $invoice['abn'] ?? '';
        $inv_runsheet = $invoice['runsheet'] ?? '';
        $inv_customer_invoice_no = $invoice['customer_invoice_no'] ?? '';
        $inv_amount = $invoice['amount'] ?? '0';
        $inv_sub_total = $invoice['sub_total'] ?? '0';
        $inv_tax_rate = $invoice['tax_rate'] ?? '0';
        $inv_other_cost = $invoice['other_cost'] ?? '0';
        $inv_total_cost = $invoice['total_cost'] ?? '0';
    }
}

mysqli_close($conn); // Close the connection
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style-2.css">
</head>
<body>

<script src="assets/js/bootstrap.bundle.min.js"></script>
<script>
    function calculateAmount() {
        var amount = 0;
        
        // Loop through checkboxes and add to amount if checked
        var checkboxes = document.querySelectorAll('.item-checkbox:checked');
        checkboxes.forEach(function(checkbox) {
            amount += parseFloat(checkbox.dataset.amount);
        });

        document.getElementById('amount').value = amount.toFixed(2);
        calculateSubtotal();
    }

    function calculateSubtotal() {
        var amount = parseFloat(document.getElementById('amount').value) || 0;
        var other_cost = parseFloat(document.getElementById('other_cost').value) || 0;
        var tax_rate = parseFloat(document.getElementById('tax_rate').value) || 0;

        // Subtotal is just the amount
        var subtotal = amount;

        // Total cost is subtotal + tax_rate + other_cost
        var total_cost = subtotal + tax_rate + other_cost;

        // Update the fields dynamically
        document.getElementById('sub_total').value = subtotal.toFixed(2);
        document.getElementById('total_cost').value = total_cost.toFixed(2);
    }

</script>

<div class="container">
    <form action="invoice.php<?php echo isset($_GET['invoice_id']) ? '?id=' . $_GET['invoice_id'] : ''; ?>" method="POST">
        <h2>Invoice Form</h2>

        <!-- Date and Invoice Number -->
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($inv_date); ?>" required>
        </div>
        <div class="form-group">
            <label for="invoice">Invoice #:</label>
            <input type="text" name="invoice" class="form-control" value="<?php echo htmlspecialchars($inv_invoice); ?>" required>
        </div>

        <!-- Company and Address -->
        <div class="form-group">
            <label for="company">Company:</label>
            <input type="text" name="company" class="form-control" value="<?php echo htmlspecialchars($inv_company); ?>" required>
        </div>
        <div class="form-group">
            <label for="address">Address:</label>
            <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($inv_address); ?>" required>
        </div>

        <!-- Phone and Postal Code -->
        <div class="form-group">
            <label for="phone">Phone:</label>
            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($inv_phone); ?>">
        </div>
        <div class="form-group">
            <label for="postal_code">Postal Code:</label>
            <input type="text" name="postal_code" class="form-control" value="<?php echo htmlspecialchars($inv_postal_code); ?>">
        </div>

        <!-- Additional Fields: ABN, RunSheet, Customer -->
        <div class="form-group">
            <label for="abn">ABN:</label>
            <input type="text" name="abn" class="form-control" value="<?php echo htmlspecialchars($inv_abn); ?>">
        </div>
        <div class="form-group">
            <label for="runsheet">RunSheet No:</label>
            <input type="text" name="runsheet" class="form-control" value="<?php echo htmlspecialchars($inv_runsheet); ?>">
        </div>
        <div class="form-group">
            <label for="customer">Customer:</label>
            <input type="text" name="customer_invoice_no" class="form-control" value="<?php echo htmlspecialchars($inv_customer_invoice_no); ?>">
        </div>

        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="text" id="amount" name="amount" class="form-control" value="<?php echo htmlspecialchars($inv_amount); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="sub_total">Sub Total:</label>
            <input type="text" id="sub_total" name="sub_total" class="form-control" value="<?php echo htmlspecialchars($inv_sub_total); ?>" readonly>
        </div>
        <div class="form-group">
            <label for="tax_rate">Tax Rate:</label>
            <input type="text" id="tax_rate" name="tax_rate" class="form-control" value="<?php echo htmlspecialchars($inv_tax_rate); ?>" oninput="calculateSubtotal()">
        </div>
        <div class="form-group">
            <label for="other_cost">Other Costs:</label>
            <input type="text" id="other_cost" name="other_cost" class="form-control" value="<?php echo htmlspecialchars($inv_other_cost); ?>" oninput="calculateSubtotal()">
        </div>
        <div class="form-group">
            <label for="total_cost">Total Cost:</label>
            <input type="text" id="total_cost" name="total_cost" class="form-control" value="<?php echo htmlspecialchars($inv_total_cost); ?>" readonly>
        </div>

        <button type="submit" class="btn btn-primary">Save Invoice</button>
    </form>
</div>

</body>
</html>
