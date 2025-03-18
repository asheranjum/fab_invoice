<?php
// Include the database connection
require 'config/database.php';

// Check if 'invoice_id' is set in the URL
if (isset($_GET['invoice_id']) && !empty($_GET['invoice_id'])) {
    $invoice_id = $_GET['invoice_id']; // Get the invoice ID from the URL

    // Ensure the ID is numeric to prevent SQL injection
    if (is_numeric($invoice_id)) {
        // SQL query to delete the invoice
        $sql = "DELETE FROM invoices WHERE id = {$invoice_id}";

        // Execute the query
        if (mysqli_query($conn, $sql)) {
            // Redirect to the main page after successful deletion
            header("Location: index.php?message=Invoice deleted successfully");
            exit();
        } else {
            echo "Error deleting invoice: " . mysqli_error($conn);
        }
    } else {
        echo "Invalid invoice ID provided.";
    }
} else {
    echo "No invoice ID specified for deletion.";
}

// Close the database connection
mysqli_close($conn);
?>
