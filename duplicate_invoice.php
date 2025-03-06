<?php
// Include the database connection
require 'config/database.php';

// Check if 'invoice_id' is set in the URL
if (isset($_GET['invoice_id']) && is_numeric($_GET['invoice_id'])) {
    $invoice_id = $_GET['invoice_id']; // Get the invoice ID from the URL

    // Fetch the invoice details (excluding invoice_id)
    $sqlFetch = "SELECT date, company, address, phone, abn, runsheet, sub_total, tax_rate, other_cost, total_cost 
                 FROM invoice WHERE invoice_id = ?";
    $stmt = $conn->prepare($sqlFetch);
    $stmt->bind_param("i", $invoice_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $invoiceData = $result->fetch_assoc();

    if (!$invoiceData) {
        die("Invoice not found.");
    }

    // Generate a new unique invoice number
    $sqlLastInvoice = "SELECT MAX(CAST(invoice AS UNSIGNED)) AS last_invoice FROM invoice";
    $resultLastInvoice = mysqli_query($conn, $sqlLastInvoice);
    $rowLastInvoice = mysqli_fetch_assoc($resultLastInvoice);
    $lastInvoiceNumber = (int) $rowLastInvoice['last_invoice'];

    $newInvoiceNumber = $lastInvoiceNumber + 1; // Generate next invoice number

    // Insert duplicate invoice with a new unique invoice number
    $sqlInsert = "INSERT INTO invoice (date, invoice, company, address, phone, abn, runsheet, sub_total, tax_rate, other_cost, total_cost) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sqlInsert);
    $stmt->bind_param(
        "sssssssssss",
        $invoiceData['date'],
        $newInvoiceNumber,
        $invoiceData['company'],
        $invoiceData['address'],
        $invoiceData['phone'],
        $invoiceData['abn'],
        $invoiceData['runsheet'],
        $invoiceData['sub_total'],
        $invoiceData['tax_rate'],
        $invoiceData['other_cost'],
        $invoiceData['total_cost']
    );

    if ($stmt->execute()) {
        // Redirect to index.php with success message
        header("Location: index.php?message=Invoice duplicated successfully");
        exit();
    } else {
        echo "Error duplicating invoice: " . $conn->error;
    }
} else {
    echo "Invalid invoice ID.";
}

// Close the database connection
mysqli_close($conn);
?>
