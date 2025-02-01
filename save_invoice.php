<?php
require 'config/database.php';
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if ($input) {
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
        $items = $input['items'] ?? [];

        // Save the invoice data in the database
        $sql = "INSERT INTO invoice (date, invoice, company, address, phone, postal_code, abn, runsheet, sub_total, tax_rate, other_cost, total_cost)
                VALUES ('$inv_date', '$inv_invoice', '$inv_company', '$inv_address', '$inv_phone', '$inv_postal_code', '$inv_abn', '$inv_runsheet', '$sub_total', '$tax_rate', '$other_cost', '$total_cost')";

        if (mysqli_query($conn, $sql)) {
            $invoiceId = mysqli_insert_id($conn);

            // Save items
            foreach ($items as $item) {
                $customerInvoiceNo = mysqli_real_escape_string($conn, $item['customer_inv_no'] ?? '');
                $itemRowId = mysqli_real_escape_string($conn, $item['item_row_id'] ?? ''); // Fetch item_row_id
                $itemName = mysqli_real_escape_string($conn, $item['item_name'] ?? '');
                $itemValue = mysqli_real_escape_string($conn, $item['item_value'] ?? '0');
                $runsheetNumber = mysqli_real_escape_string($conn, $item['runsheet_number'] ?? '');
                $runsheetDate = mysqli_real_escape_string($conn, $item['runsheet_date'] ?? '');

                $sqlItem = "INSERT INTO invoice_items (invoice_id, customer_invoice_no, item_name, item_value, runsheet_number, runsheet_date)
                VALUES ('$invoiceId', '$customerInvoiceNo', '$itemName', '$itemValue', '$runsheetNumber', '$runsheetDate')";
                mysqli_query($conn, $sqlItem);
            }

            $response = ['success' => true, 'message' => 'Invoice saved successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)];
        }
    }
}

echo json_encode($response);
mysqli_close($conn);
