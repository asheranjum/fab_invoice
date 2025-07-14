<?php
require 'config/database.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input) {
        $invoice_number = mysqli_real_escape_string($conn, $input['invoice'] ?? '');
        $invoice_type = mysqli_real_escape_string($conn, $input['invoice_type'] ?? '');
        $invoiceId = mysqli_real_escape_string($conn, $input['invoice_id'] ?? '');
        $inv_date = mysqli_real_escape_string($conn, $input['date'] ?? '');
        $inv_invoice = mysqli_real_escape_string($conn, $input['invoice'] ?? '');
        $inv_company = mysqli_real_escape_string($conn, $input['company'] ?? '');
        $employer_abn = mysqli_real_escape_string($conn, $input['employer_abn'] ?? '');
        $employer_address = mysqli_real_escape_string($conn, $input['employer_address'] ?? '');
        $employer_company = mysqli_real_escape_string($conn, $input['employer_company'] ?? '');
        $employer_phone = mysqli_real_escape_string($conn, $input['employer_phone'] ?? '');
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
    $sqlUpdate = "UPDATE invoices SET date=?, invoice_number=? , invoice_type=?, employer_company = ?, employer_abn = ? , employer_address = ?, employer_phone = ? ,  company_name=?, address=?, phone=?, postal_code =?, abn=?, tax_rate=?, sub_total=? ,total_cost=? WHERE id=?";
    $stmt = $conn->prepare($sqlUpdate);
    $stmt->bind_param("sssssssssssssssi", $inv_date,$invoice_number, $invoice_type, $employer_company, $employer_abn, $employer_address, $employer_phone ,  $inv_company, $inv_address, $inv_phone, $inv_postal_code, $inv_abn, $tax_rate, $sub_total ,$total_cost , $invoiceId);

    if ($stmt->execute()) {
        foreach ($items as $item) {
            $itemRowId = mysqli_real_escape_string($conn, $item['item_row_id'] ?? '');
            $customerInvoiceNo = mysqli_real_escape_string($conn, $item['customer_inv_no'] ?? '');
            $customerInvoiceName = mysqli_real_escape_string($conn, $item['customer_inv_name'] ?? '');
            $noteText = mysqli_real_escape_string($conn, $item['note_text_value'] ?? '');
            $row_position = intval($item['row_position'] ?? 0); // <-- get row_position
            $runsheet_number = mysqli_real_escape_string($conn, $item['runsheet_number'] ?? '');
            $runsheet_date_raw = $item['runsheet_date'] ?? '';
            $runsheet_date_formatted = '';

            if (!empty($runsheet_date_raw)) {
                // Convert only if date is in valid format
                $timestamp = strtotime($runsheet_date_raw);
                if ($timestamp !== false) {
                    $runsheet_date_formatted = date('d-m-Y', $timestamp);
                }
            }

            $runsheet_date = mysqli_real_escape_string($conn, $runsheet_date_formatted);

            // Track items to keep
            $itemsToKeep = [];
            foreach ($item['items'] as $entry) {
                $itemName = mysqli_real_escape_string($conn, $entry['item_name'] ?? '');
                $itemValue = mysqli_real_escape_string($conn, $entry['item_value'] ?? 0);
                $itemValueInt = (int) ($entry['item_value'] ?? 0);
                $itemId = mysqli_real_escape_string($conn, $entry['item_id'] ?? 0);
                
              
                if ($itemValueInt != 0) {
                    $itemsToKeep[] = $itemId;

                    // Check if item exists, update if it does, insert if not
                    $checkItem = "SELECT COUNT(*) AS count FROM invoice_items WHERE id=? AND invoice_id=? AND row_position=? AND item_name=?  AND runsheet_number=? AND runsheet_date=?";
                    $stmtCheck = $conn->prepare($checkItem);
                    $stmtCheck->bind_param("iiisss", $itemId, $invoiceId, $row_position, $itemName, $runsheet_number, $runsheet_date);
                    
                    $stmtCheck->execute();
                    $result = $stmtCheck->get_result()->fetch_assoc();
                    
                    if ($result['count'] > 0) {
                        // Update existing item
                        $sqlUpdateItem = "UPDATE invoice_items SET customer_invoice_name=?, customer_invoice_no=?, note_text=?, item_value=?, row_position=? WHERE id=? AND invoice_id=? AND row_position=? AND item_name=? AND runsheet_number=? AND runsheet_date=?";
                        $stmtUpdate = $conn->prepare($sqlUpdateItem);
                        $stmtUpdate->bind_param("ssssiiissss", $customerInvoiceName, $customerInvoiceNo, $noteText, $itemValue, $row_position, $itemId, $invoiceId, $row_position, $itemName, $runsheet_number, $runsheet_date);
                        $stmtUpdate->execute();
                    } else {
                        // Insert new item
                        $sqlInsertItem = "INSERT INTO invoice_items (invoice_id, customer_invoice_name, customer_invoice_no, note_text, item_row_id, item_name, item_value, runsheet_number, runsheet_date,row_position) VALUES (?, ?, ?, ?,?, ?, ?, ?, ?, ?)";
                        $stmtInsert = $conn->prepare($sqlInsertItem);
                        $stmtInsert->bind_param("issssssssi", $invoiceId, $customerInvoiceName, $customerInvoiceNo, $noteText, $itemRowId, $itemName, $itemValue, $runsheet_number, $runsheet_date, $row_position);
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
            $noteText = mysqli_real_escape_string($conn, $item['note_text_value'] ?? '');
            $runsheet_number = mysqli_real_escape_string($conn, $item['runsheet_number'] ?? '');
            $runsheet_date_raw = $item['runsheet_date'] ?? '';
            $runsheet_date_formatted = '';
            $row_position = intval($item['row_position'] ?? 0); // <-- get row_position

             // 1) Fetch the current MAX row_position for this invoice/item_row_id
            $sqlMax = "SELECT COALESCE(MAX(row_position), 0) AS maxpos
                    FROM invoice_items
                    WHERE invoice_id = ?";
            $stmtMax = $conn->prepare($sqlMax);
            $stmtMax->bind_param("i", $invoiceId);
            $stmtMax->execute();
            $maxPos = $stmtMax->get_result()->fetch_assoc()['maxpos'];
            $stmtMax->close();

            if (!empty($runsheet_date_raw)) {
                // Convert only if date is in valid format
                $timestamp = strtotime($runsheet_date_raw);
                if ($timestamp !== false) {
                    $runsheet_date_formatted = date('d-m-Y', $timestamp);
                }
            }
            $row_position = $maxPos+1; // Use the incremented max position
            $runsheet_date = mysqli_real_escape_string($conn, $runsheet_date_formatted);

            foreach ($item['items'] as $entry) {

             
                $itemName = mysqli_real_escape_string($conn, $entry['item_name'] ?? '');
                $itemValue = mysqli_real_escape_string($conn, $entry['item_value'] ?? '0');

                $sqlInsertItem = "INSERT INTO invoice_items (invoice_id, customer_invoice_name, customer_invoice_no, note_text, item_row_id, item_name, item_value, runsheet_number, runsheet_date, row_position) VALUES (?, ?, ?, ?, ?,?,?,?,?,?)";
                $stmtInsert = $conn->prepare($sqlInsertItem);
                $stmtInsert->bind_param("issssssssi", $invoiceId, $customerInvoiceName, $customerInvoiceNo, $noteText, $itemRowId, $itemName, $itemValue, $runsheet_number, $runsheet_date, $row_position);
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
