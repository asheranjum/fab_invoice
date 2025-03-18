<?php
require 'config/database.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input) {
        $invoiceId = mysqli_real_escape_string($conn, $input['invoice_id'] ?? '');
        $inv_date = mysqli_real_escape_string($conn, $input['date'] ?? '');
        $inv_invoice = mysqli_real_escape_string($conn, $input['invoice'] ?? '');
        $inv_company = mysqli_real_escape_string($conn, $input['company'] ?? '');
        $inv_address = mysqli_real_escape_string($conn, $input['address'] ?? '');
        $inv_phone = mysqli_real_escape_string($conn, $input['phone'] ?? '');
        $inv_postal_code = mysqli_real_escape_string($conn, $input['postal_code'] ?? '');
        $inv_abn = mysqli_real_escape_string($conn, $input['abn'] ?? '');
        $inv_runsheet = mysqli_real_escape_string($conn, $input['runsheet'] ?? '');
        $sub_total = mysqli_real_escape_string($conn, $input['sub_total'] ?? '0');
        $tax_rate = mysqli_real_escape_string($conn, $input['tax_rate'] ?? '0');
        $other_cost = mysqli_real_escape_string($conn, $input['other_cost'] ?? '0');
        $total_cost = mysqli_real_escape_string($conn, $input['total_cost'] ?? '0');
        $items = $input['existing_items'] ?? [];
        $newItems = $input['new_items'] ?? [];
        $unchecked_items = $input['unchecked_items'] ?? []; // Unchecked items to delete

    }

    // Update invoice details
    $sqlUpdate = "UPDATE invoices SET date=?, company_name=?, address=?, phone=?, postal_code =?, abn=? WHERE id=?";
    $stmt = $conn->prepare($sqlUpdate);
    $stmt->bind_param("ssssssi", $inv_date, $inv_company, $inv_address, $inv_phone, $inv_postal_code, $inv_abn, $invoiceId);

    if ($stmt->execute()) {
        foreach ($items as $item) {
            $itemRowId = mysqli_real_escape_string($conn, $item['item_row_id'] ?? '');
            $customerInvoiceNo = mysqli_real_escape_string($conn, $item['customer_inv_no'] ?? '');
            $customerInvoiceName = mysqli_real_escape_string($conn, $item['customer_inv_name'] ?? '');
            $runsheet_number = mysqli_real_escape_string($conn, $item['runsheet_number'] ?? '');
            $runsheet_date = mysqli_real_escape_string($conn, $item['runsheet_date'] ?? '');

            // Track items to keep
            $itemsToKeep = [];

            foreach ($item['items'] as $entry) {
                $itemName = mysqli_real_escape_string($conn, $entry['item_name'] ?? '');
                $itemValue = mysqli_real_escape_string($conn, $entry['item_value'] ?? 0);
                $itemId = mysqli_real_escape_string($conn, $entry['item_id'] ?? 0);

                if ($itemValue != 0) {
                    $itemsToKeep[] = $itemId;

                    // Check if item exists, update if it does, insert if not
                    $checkItem = "SELECT COUNT(*) AS count FROM invoice_items WHERE id=? AND invoice_id=? AND item_row_id=? AND item_name=?";
                    $stmtCheck = $conn->prepare($checkItem);
                    $stmtCheck->bind_param("iiis", $itemId, $invoiceId, $itemRowId, $itemName);
                    $stmtCheck->execute();
                    $result = $stmtCheck->get_result()->fetch_assoc();

                    if ($result['count'] > 0) {
                        // Update existing item
                        $sqlUpdateItem = "UPDATE invoice_items SET customer_invoice_name=?, customer_invoice_no=?, item_value=? WHERE id=? AND invoice_id=? AND item_row_id=? AND item_name=? AND runsheet_number=? AND runsheet_date=?";
                        $stmtUpdate = $conn->prepare($sqlUpdateItem);
                        $stmtUpdate->bind_param("ssssissss", $customerInvoiceName, $customerInvoiceNo, $itemValue, $itemId, $invoiceId, $itemRowId, $itemName, $runsheet_number, $runsheet_date);
                        $stmtUpdate->execute();
                    } else {
                        // Insert new item
                        $sqlInsertItem = "INSERT INTO invoice_items (invoice_id, customer_invoice_name, customer_invoice_no, item_row_id, item_name, item_value, runsheet_number, runsheet_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                        $stmtInsert = $conn->prepare($sqlInsertItem);
                        $stmtInsert->bind_param("isssssss", $invoiceId, $customerInvoiceName, $customerInvoiceNo, $itemRowId, $itemName, $itemValue, $runsheet_number, $runsheet_date);
                        $stmtInsert->execute();

                        // Get the last inserted ID and add to itemsToKeep
                        $itemsToKeep[] = $stmtInsert->insert_id;
                    }
                }
            }           
        }

        if (!empty($unchecked_items)) {
            $uncheckedItemsList = implode(",", $unchecked_items);
            $sqlDeleteUncheckedItems = "DELETE FROM invoice_items WHERE id IN ($uncheckedItemsList)";
            $stmtDeleteUnchecked = $conn->prepare($sqlDeleteUncheckedItems);
            $stmtDeleteUnchecked->execute();
        }

        // Insert New Items
        foreach ($newItems as $item) {
            $itemRowId = mysqli_real_escape_string($conn, $item['item_row_id'] ?? '');
            $customerInvoiceNo = mysqli_real_escape_string($conn, $item['customer_inv_no'] ?? '');
            $customerInvoiceName = mysqli_real_escape_string($conn, $item['customer_inv_name'] ?? '');
            $runsheet_number = mysqli_real_escape_string($conn, $item['runsheet_number'] ?? '');
            $runsheet_date = mysqli_real_escape_string($conn, $item['runsheet_date'] ?? '');

            foreach ($item['items'] as $entry) {
                $itemName = mysqli_real_escape_string($conn, $entry['item_name'] ?? '');
                $itemValue = mysqli_real_escape_string($conn, $entry['item_value'] ?? '0');

                $sqlInsertItem = "INSERT INTO invoice_items (invoice_id, customer_invoice_name, customer_invoice_no, item_row_id, item_name, item_value, runsheet_number, runsheet_date) VALUES (?, ?, ?, ?, ?,?,?,?)";
                $stmtInsert = $conn->prepare($sqlInsertItem);
                $stmtInsert->bind_param("isssssss", $invoiceId, $customerInvoiceName, $customerInvoiceNo, $itemRowId, $itemName, $itemValue, $runsheet_number, $runsheet_date);
                $stmtInsert->execute();
            }
        }

        $response = ['success' => true, 'message' => 'Invoice updated successfully'];
    } else {
        $response = ['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)];
    }
}

echo json_encode($response);
mysqli_close($conn);
