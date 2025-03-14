<?php
require 'config/database.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['invoice_id'])) {
    $invoiceId = mysqli_real_escape_string($conn, $_GET['invoice_id']);

    // Fetch invoice details
    $sqlInvoice = "SELECT * FROM invoice WHERE invoice_id = ?";
    $stmt = $conn->prepare($sqlInvoice);
    $stmt->bind_param("i", $invoiceId);
    $stmt->execute();
    $resultInvoice = $stmt->get_result();
    $invoice = $resultInvoice->fetch_assoc();

    if ($invoice) {
        // Fetch invoice items
        $sqlItems = "SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY row_order ASC";
        $stmt = $conn->prepare($sqlItems);
        $stmt->bind_param("i", $invoiceId);
        $stmt->execute();
        $resultItems = $stmt->get_result();

        $items = [];
        while ($row = $resultItems->fetch_assoc()) {
            $items[] = $row;
        }

        $invoice['items'] = $items;
        $response = ['success' => true, 'invoice' => $invoice];
    } else {
        $response['message'] = 'Invoice not found';
    }
}

echo json_encode($response);
mysqli_close($conn);
?>
