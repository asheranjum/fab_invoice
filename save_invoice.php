<?php
require 'config/database.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input) {
        $inv_date = $input['date'] ?? '';
        $inv_invoice = $input['invoice'] ?? '';
        $inv_company = $input['company'] ?? '';
        $inv_address = $input['address'] ?? '';
        $inv_phone = $input['phone'] ?? '';
        $inv_postal_code = $input['postal_code'] ?? '';
        $inv_abn = $input['abn'] ?? '';
        $inv_runsheet = $input['runsheet'] ?? '';
        $sub_total = $input['sub_total'] ?? '0';
        $tax_rate = $input['tax_rate'] ?? '0';
        $other_cost = $input['other_cost'] ?? '0';
        $total_cost = $input['total_cost'] ?? '0';
        $items = $input['items'] ?? [];

        // Use prepared statement for inserting invoice
        $sql = "INSERT INTO invoice (date, invoice, company, address, phone, postal_code, abn, runsheet, sub_total, tax_rate, other_cost, total_cost)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssdddd", $inv_date, $inv_invoice, $inv_company, $inv_address, $inv_phone, $inv_postal_code, $inv_abn, $inv_runsheet, $sub_total, $tax_rate, $other_cost, $total_cost);

        if (mysqli_stmt_execute($stmt)) {
            $invoiceId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            // Save items using prepared statement
            $sqlItem = "INSERT INTO invoice_items (invoice_id, customer_invoice_name, customer_invoice_no, item_row_id, item_name, item_value, runsheet_number, runsheet_date) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtItem = mysqli_prepare($conn, $sqlItem);

            foreach ($items as $item) {
                $customerInvoiceNo = $item['customer_inv_no'] ?? '';
                $customerInvoiceName = $item['customer_inv_name'] ?? '';
                $itemRowId = $item['item_row_id'] ?? ''; // Fetch item_row_id
                $itemName = $item['item_name'] ?? '';
                $itemValue = $item['item_value'] ?? '0';
                $runsheetNumber = $item['runsheet_number'] ?? '';
                $runsheetDate = $item['runsheet_date'] ?? '';

                mysqli_stmt_bind_param($stmtItem, "isssssss", $invoiceId, $customerInvoiceName, $customerInvoiceNo, $itemRowId, $itemName, $itemValue, $runsheetNumber, $runsheetDate);
                mysqli_stmt_execute($stmtItem);
            }

            mysqli_stmt_close($stmtItem);
            $response = ['success' => true, 'message' => 'Invoice saved successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)];
        }
    }
}

echo json_encode($response);
mysqli_close($conn);
?>
