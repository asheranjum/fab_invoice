<?php
require 'config/database.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input) {
        $invoiceId = mysqli_real_escape_string($conn, $input['invoice_id'] ?? '');
        $customerInvoiceId = mysqli_real_escape_string($conn, $input['customer_invoice_id'] ?? '');

        if ($invoiceId && $customerInvoiceId) {
            // 1. Delete all items for this customer_invoice_id and invoice_id
            $sqlDelete = "DELETE FROM invoice_items WHERE customer_invoice_id = ? AND invoice_id = ?";
            $stmt = $conn->prepare($sqlDelete);
            $stmt->bind_param("ii", $customerInvoiceId, $invoiceId);

            if ($stmt->execute()) {
                $stmt->close();

                // 2. Recalculate sub_total for remaining items
                $sqlSum = "SELECT COALESCE(SUM(item_value),0) AS new_sub_total FROM invoice_items WHERE invoice_id = ?";
                $stmtSum = $conn->prepare($sqlSum);
                $stmtSum->bind_param("i", $invoiceId);
                $stmtSum->execute();
                $newSubTotal = $stmtSum->get_result()->fetch_assoc()['new_sub_total'];
                $stmtSum->close();

                // 3. Fetch tax_rate from invoices table
                $sqlFetch = "SELECT COALESCE(tax_rate, 0) as tax_rate FROM invoices WHERE id = ?";
                $stmtFetch = $conn->prepare($sqlFetch);
                $stmtFetch->bind_param("i", $invoiceId);
                $stmtFetch->execute();
                $invoiceData = $stmtFetch->get_result()->fetch_assoc();
                $stmtFetch->close();

                $taxRate = (float)$invoiceData['tax_rate'];

                // 4. Calculate new total cost
                $newTotalCost = $newSubTotal + $taxRate;

                // 5. Update invoice totals
                $sqlUpdate = "UPDATE invoices SET sub_total = ?, total_cost = ? WHERE id = ?";
                $stmtUpd = $conn->prepare($sqlUpdate);
                $stmtUpd->bind_param("ddi", $newSubTotal, $newTotalCost, $invoiceId);

                if ($stmtUpd->execute()) {
                    $response = [
                        'success' => true,
                        'message' => 'Customer invoice row deleted and totals updated',
                        'sub_total' => $newSubTotal,
                        'total_cost' => $newTotalCost
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Could not update invoice totals: ' . $stmtUpd->error
                    ];
                }
                $stmtUpd->close();
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Could not delete items: ' . $stmt->error
                ];
                $stmt->close();
            }
        } else {
            $response = [
                'success' => false,
                'message' => 'Missing invoice_id or customer_invoice_id'
            ];
        }
    }
}


echo json_encode($response);
mysqli_close($conn);
?>