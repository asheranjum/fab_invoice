<?php
require 'config/database.php';
header('Content-Type: application/json');

mysqli_set_charset($conn, 'utf8mb4');

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input) {
        $invoice_number   = $input['invoice'] ?? '';
        $invoice_type     = $input['invoice_type'] ?? '';
        $invoiceId        = (int)($input['invoice_id'] ?? 0);
        $inv_date         = $input['date'] ?? '';
        $inv_company      = $input['company'] ?? '';
        $employer_abn     = $input['employer_abn'] ?? '';
        $employer_address = $input['employer_address'] ?? '';
        $employer_company = $input['employer_company'] ?? '';
        $employer_phone   = $input['employer_phone'] ?? '';
        $inv_address      = $input['address'] ?? '';
        $inv_phone        = $input['phone'] ?? '';
        $inv_postal_code  = $input['postal_code'] ?? '';
        $inv_abn          = $input['abn'] ?? '';
        $sub_total        = $input['sub_total'] ?? '0';
        $tax_rate         = $input['tax_rate'] ?? '0';
        $total_cost       = $input['total_cost'] ?? '0';

        $items            = $input['existing_items'] ?? [];
        $newItems         = $input['new_items'] ?? [];
        $unchecked_items  = $input['unchecked_items'] ?? [];
    }

    // Update invoice
    $sqlUpdate = "
        UPDATE invoices SET
            date=?,
            invoice_number=?,
            invoice_type=?,
            employer_company=?,
            employer_abn=?,
            employer_address=?,
            employer_phone=?,
            company_name=?,
            address=?,
            phone=?,
            postal_code=?,
            abn=?,
            tax_rate=?,
            sub_total=?,
            total_cost=?
        WHERE id=?
    ";

    $stmt = $conn->prepare($sqlUpdate);
    $stmt->bind_param(
        "sssssssssssssssi",
        $inv_date,
        $invoice_number,
        $invoice_type,
        $employer_company,
        $employer_abn,
        $employer_address,
        $employer_phone,
        $inv_company,
        $inv_address,
        $inv_phone,
        $inv_postal_code,
        $inv_abn,
        $tax_rate,
        $sub_total,
        $total_cost,
        $invoiceId
    );

    if ($stmt->execute()) {

        foreach ($items as $item) {
            $itemRowId           = $item['item_row_id'] ?? '';
            $customerInvoiceNo   = $item['customer_inv_no'] ?? '';
            $customerInvoiceName = $item['customer_inv_name'] ?? '';
            $noteText            = $item['note_text_value'] ?? '';
            $row_position        = (int)($item['row_position'] ?? 0);
            $runsheet_number     = $item['runsheet_number'] ?? '';

            $runsheet_date = '';
            if (!empty($item['runsheet_date'])) {
                $ts = strtotime($item['runsheet_date']);
                if ($ts !== false) {
                    $runsheet_date = date('d-m-Y', $ts);
                }
            }

            foreach ($item['items'] as $entry) {
                $itemName      = $entry['item_name'] ?? '';
                $itemValue     = $entry['item_value'] ?? '0';
                $itemValueInt  = (int)$itemValue;
                $itemId        = (int)($entry['item_id'] ?? 0);

                if ($itemValueInt !== 0) {

                    $checkItem = "
                        SELECT COUNT(*) AS count
                        FROM invoice_items
                        WHERE id=? AND invoice_id=? AND row_position=? 
                              AND item_name=? AND runsheet_number=? AND runsheet_date=?
                    ";
                    $stmtCheck = $conn->prepare($checkItem);
                    $stmtCheck->bind_param(
                        "iiisss",
                        $itemId,
                        $invoiceId,
                        $row_position,
                        $itemName,
                        $runsheet_number,
                        $runsheet_date
                    );
                    $stmtCheck->execute();
                    $result = $stmtCheck->get_result()->fetch_assoc();

                    if ($result['count'] > 0) {
                        $sqlUpdateItem = "
                            UPDATE invoice_items
                            SET customer_invoice_name=?,
                                customer_invoice_no=?,
                                note_text=?,
                                item_value=?,
                                row_position=?
                            WHERE id=? AND invoice_id=? AND row_position=?
                                  AND item_name=? AND runsheet_number=? AND runsheet_date=?
                        ";
                        $stmtUpdate = $conn->prepare($sqlUpdateItem);
                        $stmtUpdate->bind_param(
                            "ssssiiissss",
                            $customerInvoiceName,
                            $customerInvoiceNo,
                            $noteText,
                            $itemValue,
                            $row_position,
                            $itemId,
                            $invoiceId,
                            $row_position,
                            $itemName,
                            $runsheet_number,
                            $runsheet_date
                        );
                        $stmtUpdate->execute();
                    } else {
                        $sqlInsertItem = "
                            INSERT INTO invoice_items
                            (invoice_id, customer_invoice_name, customer_invoice_no, note_text,
                             item_row_id, item_name, item_value, runsheet_number, runsheet_date, row_position)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ";
                        $stmtInsert = $conn->prepare($sqlInsertItem);
                        $stmtInsert->bind_param(
                            "issssssssi",
                            $invoiceId,
                            $customerInvoiceName,
                            $customerInvoiceNo,
                            $noteText,
                            $itemRowId,
                            $itemName,
                            $itemValue,
                            $runsheet_number,
                            $runsheet_date,
                            $row_position
                        );
                        $stmtInsert->execute();
                    }
                }
            }
        }

        if (!empty($unchecked_items)) {
            $ids = implode(',', array_map('intval', $unchecked_items));
            $conn->query("DELETE FROM invoice_items WHERE id IN ($ids)");
        }

        $response = ['success' => true, 'message' => 'Invoice updated successfully'];
    } else {
        $response = ['success' => false, 'message' => 'Database error'];
    }
}

echo json_encode($response);
mysqli_close($conn);
