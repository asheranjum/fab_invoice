<?php
require 'config/database.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input) {
        $invoice_type = mysqli_real_escape_string($conn, $input['invoice_type'] ?? '');
        $inv_date = mysqli_real_escape_string($conn, $input['date'] ?? '');


        $inv_date_raw = $input['date'] ?? '';
        $inv_date_formatted = '';

        if (!empty($inv_date_raw)) {
            // Convert only if date is in valid format
            $timestamp = strtotime($inv_date_raw);
            if ($timestamp !== false) {
                $inv_date_formatted = date('d-m-Y', $timestamp);
            }
        }

        $inv_date = mysqli_real_escape_string($conn, $inv_date_formatted);

        $inv_invoice = mysqli_real_escape_string($conn, $input['invoice'] ?? '');
        $employer_abn = mysqli_real_escape_string($conn, $input['employer_abn'] ?? '');
        $employer_address = mysqli_real_escape_string($conn, $input['employer_address'] ?? '');
        $employer_company = mysqli_real_escape_string($conn, $input['employer_company'] ?? '');
        $employer_phone = mysqli_real_escape_string($conn, $input['employer_phone'] ?? '');
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
        $items = $input['items'] ?? [];

        $conn->begin_transaction();

        try {
            // Insert new invoice
            $sqlInvoice = "INSERT INTO invoices (date, invoice_number, invoice_type, employer_company, employer_abn , employer_address, employer_phone , company_name, address, phone, postal_code, abn, runsheet_number, sub_total, tax_rate, other_cost, total_cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sqlInvoice);
            if (!$stmt) {
                throw new Exception("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }
            $stmt->bind_param('sssssssssssssdddd', $inv_date, $inv_invoice, $invoice_type, $employer_company, $employer_abn, $employer_address, $employer_phone, $inv_company, $inv_address, $inv_phone, $inv_postal_code, $inv_abn, $inv_runsheet, $sub_total, $tax_rate, $other_cost, $total_cost);
            $stmt->execute();
            $invoiceId = $stmt->insert_id;
            $stmt->close();
       

            // Insert items
            $sqlItem = "INSERT INTO invoice_items (invoice_id, customer_invoice_name, customer_invoice_no,  item_row_id, item_name, item_value, note_text, runsheet_number, runsheet_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmtItem = $conn->prepare($sqlItem);
            if (!$stmtItem) {
                throw new Exception("Prepare failed: (" . $conn->errno . ") " . $conn->error);
            }
            foreach ($items as $item) {
                $customerInvoiceNo = mysqli_real_escape_string($conn, $item['customer_inv_no'] ?? '');
                $customerInvoiceName = mysqli_real_escape_string($conn, $item['customer_inv_name'] ?? '');
                $itemRowId = mysqli_real_escape_string($conn, $item['item_row_id'] ?? '');
                $itemName = mysqli_real_escape_string($conn, $item['item_name'] ?? '');
                $itemValue = mysqli_real_escape_string($conn, $item['item_value'] ?? '0');
                $runsheetNumber = mysqli_real_escape_string($conn, $item['runsheet_number'] ?? '');
                $noteText = mysqli_real_escape_string($conn, $item['note_text_value'] ?? '');
                // $runsheetDate = mysqli_real_escape_string($conn, $item['runsheet_date'] ?? '');

                $runsheet_date_raw = $item['runsheet_date'] ?? '';
                $runsheet_date_formatted = '';
    
                if (!empty($runsheet_date_raw)) {
                    // Convert only if date is in valid format
                    $timestamp = strtotime($runsheet_date_raw);
                    if ($timestamp !== false) {
                        $runsheet_date_formatted = date('d-m-Y', $timestamp);
                    }
                }
    
                $runsheetDate = mysqli_real_escape_string($conn, $runsheet_date_formatted);

                $stmtItem->bind_param('issssssss', $invoiceId, $customerInvoiceName, $customerInvoiceNo,  $itemRowId, $itemName, $itemValue, $noteText, $runsheetNumber, $runsheetDate);
                $stmtItem->execute();
            }
            $stmtItem->close();

            $conn->commit();
            $response = ['success' => true, 'message' => 'Invoice saved successfully'];
        } catch (Exception $e) {
            $conn->rollback();
            $response = ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}

echo json_encode($response);
$conn->close();
?>