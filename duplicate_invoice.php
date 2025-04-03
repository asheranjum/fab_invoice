<?php

// Include the database connection
require 'config/database.php';

// Check if 'invoice_id' is set in the URL and is numeric
if (isset($_GET['invoice_id']) && is_numeric($_GET['invoice_id'])) {
    $invoice_id = $_GET['invoice_id']; // Get the invoice ID from the URL

    // Fetch the invoice details (excluding invoice_id)
    $sqlFetch = "SELECT date, company_name, address, phone, abn 
                 FROM invoices WHERE id = ?";

    $stmt = $conn->prepare($sqlFetch);

    if (!$stmt) {
        die("SQL Error (Fetch Invoice): " . $conn->error);
    }

    $stmt->bind_param("i", $invoice_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $invoiceData = $result->fetch_assoc();

    if (!$invoiceData) {
        die("Invoice not found.");
    }

    // Generate a new unique invoice number
    $sqlLastInvoice = "SELECT MAX(CAST(invoice_number AS UNSIGNED)) AS last_invoice FROM invoices";
    $resultLastInvoice = mysqli_query($conn, $sqlLastInvoice);

    if (!$resultLastInvoice) {
        die("SQL Error (Fetch Last Invoice): " . $conn->error);
    }

    $rowLastInvoice = mysqli_fetch_assoc($resultLastInvoice);
    $lastInvoiceNumber = (int) $rowLastInvoice['last_invoice'];

    $newInvoiceNumber = $lastInvoiceNumber + 1; // Generate next invoice number

    // Insert duplicate invoice with a new unique invoice number
    $sqlInsert = "INSERT INTO invoices (date, invoice_number, company_name, address, phone, abn) 
                  VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sqlInsert);

    if (!$stmt) {
        die("SQL Error (Insert Invoice): " . $conn->error);
    }

    // Corrected bind_param() with 6 parameters (not 7)
    $stmt->bind_param(
        "sissss",  // "s" for string, "i" for integer
        $invoiceData['date'],
        $newInvoiceNumber,
        $invoiceData['company_name'],
        $invoiceData['address'],
        $invoiceData['phone'],
        $invoiceData['abn']
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
