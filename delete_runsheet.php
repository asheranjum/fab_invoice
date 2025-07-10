<?php
require 'config/database.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input) {
        $invoiceId = mysqli_real_escape_string($conn, $input['invoice_id'] ?? '');
        $runsheetNumber = mysqli_real_escape_string($conn, $input['runsheet_number'] ?? '');
        $runsheetDate = mysqli_real_escape_string($conn, $input['runsheet_date'] ?? '');

        if ($runsheetNumber && $runsheetDate) {
            $sqlDelete = "DELETE FROM invoice_items WHERE runsheet_number=? AND runsheet_date=? AND invoice_id=?";
            $stmt = $conn->prepare($sqlDelete);
            $stmt->bind_param("ssi", $runsheetNumber, $runsheetDate, $invoiceId);

            if ($stmt->execute()) {
                

                // 1) Re-sum remaining items
                $sqlSum = "SELECT COALESCE(SUM(item_value),0) AS new_sub_total  FROM invoice_items  WHERE invoice_id = ?";
                $stmtSum = $conn->prepare($sqlSum);
                $stmtSum->bind_param("i", $invoiceId);
                $stmtSum->execute();
                $newSubTotal = $stmtSum->get_result()->fetch_assoc()['new_sub_total'];
                $stmtSum->close();

                // 2) Fetch tax_rate from the invoice
                $sqlFetch = "SELECT tax_rate  FROM invoices  WHERE id = ?";
                $stmtFetch = $conn->prepare($sqlFetch);
                $stmtFetch->bind_param("i", $invoiceId);
                $stmtFetch->execute();
                $invoiceData = $stmtFetch->get_result()->fetch_assoc();
                $stmtFetch->close();

                $taxRate   = (float)$invoiceData['tax_rate'];

                // 3) Calculate the new total cost
                //    (adjust this formula if you have discounts or additional fees)
                $newTotalCost = $newSubTotal
                            + $taxRate;

                // 4) Update the invoice
                $sqlUpdate = "UPDATE invoices 
                            SET sub_total = ?, total_cost = ? 
                            WHERE id = ?";
                $stmtUpd = $conn->prepare($sqlUpdate);
                $stmtUpd->bind_param("ddi", 
                    $newSubTotal, 
                    $newTotalCost, 
                    $invoiceId
                );

                if ($stmtUpd->execute()) {
                    $response = [
                        'success' => true,
                        'message' => 'Runsheet items deleted, invoice totals recalculated'
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'message' => 'Could not update invoice totals: ' . $stmtUpd->error
                    ];
                }
                $stmtUpd->close();


                $response = ['success' => true, 'message' => 'Runsheet and linked items deleted successfully'];
            } else {
                $response = ['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)];
            }
        } else {
            $response = ['success' => false, 'message' => 'Missing runsheet number or date'];
        }
    }
}

echo json_encode($response);
mysqli_close($conn);
?>