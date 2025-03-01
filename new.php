<?php
require 'config/database.php';
require 'vendor/autoload.php';

use Mpdf\Mpdf;

// Get `invoice_id` from the query string
$invoiceId = isset($_GET['invoice_id']) ? (int)$_GET['invoice_id'] : 0;

if ($invoiceId <= 0) {
    die('Invalid Invoice ID');
}

// Fetch Invoice Data
$sqlInvoice = "SELECT * FROM invoice WHERE invoice_id = ?";
$stmt = $conn->prepare($sqlInvoice);
$stmt->bind_param("i", $invoiceId);
$stmt->execute();
$resultInvoice = $stmt->get_result();
$invoice = $resultInvoice->fetch_assoc();

if (!$invoice) {
    die('Invoice not found');
}

// Fetch Invoice Items
$sqlItems = "SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY runsheet_number, item_row_id";
$stmt = $conn->prepare($sqlItems);
$stmt->bind_param("i", $invoiceId);
$stmt->execute();
$resultItems = $stmt->get_result();

$groupedItems = [];
while ($row = $resultItems->fetch_assoc()) {
    $runsheetNumber = $row['runsheet_number'];
    $runsheetDate = $row['runsheet_date'];
    $itemRowId = $row['item_row_id'];
    $itemName = $row['item_name'];
    $itemValue = $row['item_value'];
    $customInvoiceNo = $row['customer_invoice_no'];

    // Initialize runsheet group
    if (!isset($groupedItems[$runsheetNumber])) {
        $groupedItems[$runsheetNumber] = [
            'runsheet_date' => $runsheetDate,
            'items' => []
        ];
    }

    // Initialize item row
    if (!isset($groupedItems[$runsheetNumber]['items'][$itemRowId])) {
        $groupedItems[$runsheetNumber]['items'][$itemRowId] = [
            'custom_invoice_no' => $customInvoiceNo,
            'items' => []
        ];
    }

    // Add the item with its price
    $groupedItems[$runsheetNumber]['items'][$itemRowId]['items'][$itemName] = $itemValue;
}

// Invoice Details
$date = $invoice['date'];
$invoiceNo = $invoice['invoice'];
$company = $invoice['company'];
$address = $invoice['address'];
$phone = $invoice['phone'];
$abn = $invoice['abn'];

$sub_total = $invoice['sub_total'];
$tax_rate = $invoice['tax_rate'];
$total_cost = $invoice['total_cost'];

$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .invoice-container { width: 100%; padding: 20px; }
        .bill-to, .details, .summary { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .bill-to td, .details th, .details td, .summary th, .summary td { padding: 10px; text-align: center; border: 1px solid #ddd; }
        .details th { background-color: #f89c1c; color:#263278; }
        .summary { text-align:right; width: 350px; margin-left: auto; font-size:14px; }
        .footer { font-size: 14px; color: #f89c1c; font-weight: bold; background-color: #011f7f; text-align: center; padding: 10px; }
        .footer-text ul { list-style-type: none; text-align: center; }
        .runsheet-header { background: #f89c1c; color: #011f7f; font-weight: bold; padding: 10px; }
        .service-items { position: relative; top: 60%; left: 65%; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <img src="assets/images/head.png" style="width:100%" />
        <h2 style="margin-left:30px; margin-top:-100px; color:white">TAX INVOICE</h2>
        <div style="margin-left:30px; color:white"><span class="label">Invoice No:</span> ' . $invoiceNo . '</div>

        <table class="bill-to">
            <tr>
                <td><span class="label">Date:</span> ' . $date . '</td>
                <td style="text-align:right"><span class="label">FAB TRANSPORT SERVICES PTY LTD</span></td>
            </tr>
            <tr>
                <td><span class="label">Company Name:</span> ' . $company . '</td>
                <td style="text-align:right"><span class="label">PHONE: 0403729966</span></td>
            </tr>
            <tr>
                <td><span class="label">Address:</span> ' . $address . '</td>
                <td style="text-align:right"><span class="label">ABN: 123 121 211 222 222</span></td>
            </tr>
        </table>

        <table class="details">
            <thead>
                <tr>
                    <th>Customer Invoice No</th>
                    <th>Items Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>';
       
foreach ($groupedItems as $runsheetNumber => $runsheetData) {
    $runsheetDate = $runsheetData['runsheet_date'];

    // Add Runsheet Header
    $html .= '
    <tr class="runsheet-header">
        <td colspan="3">Runsheet No: ' . $runsheetNumber . ' | Runsheet Date: ' . $runsheetDate . '</td>
    </tr>';

    foreach ($runsheetData['items'] as $itemRowId => $data) {
        $customInvoiceNo = $data['custom_invoice_no'];
        $items = $data['items'];

        $html .= '
        <tr>
            <td>' . htmlspecialchars($customInvoiceNo) . '</td>
            <td>
                <table class="checkbox-table">
                    <tr>';

                    $allOptions = [
                        'DELIV+' => 'DELIV+',
                        'DISAS+' => 'DISAS+',
                        'ASSEM+' => 'ASSEM+',
                        'RUB+' => 'RUB+',
                        'UPST+' => 'UPST+',
                        'DOWNST+' => 'DOWNST+',
                        'PREM+' => 'PREM+',
                        'BRTRANS+' => 'BRTRANS+',
                        'INST+' => 'INST+',
                        'H/DLIV+' => 'H/DLIV+',
                        'VOL+' => 'VOL+',
                        'WATERCON+' => 'WATERCON+',
                        'DOOR/R+' => 'DOOR/R+',
                
                    ];

                     // Check for any key matching P/UP(x)
    $selectedPUP = null; // To store the matched P/UP key
    foreach ($items as $key => $value) {
        if (preg_match('/^P\/UP\(\d+\)$/', $key)) {
            $selectedPUP = $key; // Match the first found P/UP key
            break;
        }
    }

    // If a P/UP key is found, add it dynamically
    if ($selectedPUP) {
        $allOptions[$selectedPUP] = $selectedPUP;
    }
    else
    {
        $pupKey = 'P/UP';
        $allOptions[$pupKey] = 'P/UP';
    }
    
        foreach ($allOptions as $key => $label) {
            $checked = isset($items[$key]);
            $image = $checked ? 'assets/images/check.png' : 'assets/images/uncheck.png';
            $value = $checked ? '$' . number_format($items[$key], 2) : '-';

            $html .= '
            <td>
                <div style="display:flex; align-items:center;">
                    <img src="' . $image . '" width="20" height="20" />
                    <div style="margin-left:5px;">' . htmlspecialchars($label) . '</div>
                </div>
                <div style="text-align:center;">' . $value . '</div>
            </td>';
        }

        $html .= '
                    </tr>
                </table>
            </td>
            <td>$' . number_format(array_sum($items), 2) . '</td>
        </tr>';
    }
}

$html .= '
            </tbody>
        </table>

        <table class="summary">
            <tr><th>Total</th><td>$' . number_format($sub_total, 2) . '</td></tr>
            <tr><th>Tax Rate</th><td>$' . number_format($tax_rate, 2) . '</td></tr>
            <tr><th>Total Cost</th><td>$' . number_format($total_cost, 2) . '</td></tr>
        </table>
    </div>
</body>
</html>';

$mpdf = new Mpdf();
$mpdf->WriteHTML($html);
$mpdf->Output('invoice.pdf', 'I');
