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
$sqlItems = "SELECT * FROM invoice_items WHERE invoice_id = ?";
$stmt = $conn->prepare($sqlItems);
$stmt->bind_param("i", $invoiceId);
$stmt->execute();
$resultItems = $stmt->get_result();

$groupedItems = [];


while ($row = $resultItems->fetch_assoc()) {
    $itemRowId = $row['item_row_id']; // e.g., 43~1
    $itemName = $row['item_name'];    // e.g., DELIV
    $itemValue = $row['item_value']; // e.g., 30.00
    $customInvoiceNo = $row['customer_invoice_no']; // e.g., "INV001" (assuming it's in the table)

    // Initialize the group if not already present
    if (!isset($groupedItems[$itemRowId])) {
        $groupedItems[$itemRowId] = [
            'custom_invoice_no' => $customInvoiceNo, // Add custom invoice number
            'items' => [], // Initialize items array
        ];
    }

    // Add the item and value to the group
    $groupedItems[$itemRowId]['items'][$itemName] = $itemValue;
}



// Dynamic Values
$date = $invoice['date'];
$invoiceNo = $invoice['invoice'];
$company = $invoice['company'];
$address = $invoice['address'];
$phone = $invoice['phone'];
$abn = $invoice['abn'];
$postalCode = $invoice['postal_code'];
$runSheetNo = $invoice['runsheet'];

$sub_total = $invoice['sub_total'];
$tax_rate = $invoice['tax_rate'];
$other_cost = $invoice['other_cost'];
$total_cost = $invoice['total_cost'];



$html = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .invoice-container {
            width: 100%;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .bill-to {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            color: #011f7f;
        }
        .bill-to td {
            padding: 5px;
            font-size: 14px;
            
        }
        .bill-to .label {
            font-weight: bold;
            color: #011f7f;
        }
        .details, .summary {
            width: 100%;
            border-collapse: collapse;
        }
        .details th, .details td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            color:#263278;
        }
       
        .details th {
            background-color: #f89c1c;
            color:#263278;
        }
        .checkbox-table {
            width: 100%;
            text-align: center;
            border-collapse: collapse;
        }

       .checkbox-table  th, .checkbox-table  td {
            border: 0px solid #ddd;
            padding: 8px;
            text-align: center;
            color:#263278;
        }

        .checkbox-table img {
            margin-bottom: 3px;
        }
        .footer {
            font-size: 14px;
            color: #f89c1c;
            font-weight: bold;
            background-color: #011f7f;
            text-align: center;
            padding: 10px;
        }
        .footer-text { }

        .total-sumay
        {
            background-color: white;
            padding: 10px;
        }

        .summary {
            margin-top: 20px;
            margin-bottom: 20px;
            width:350px;
            text-align:center;
            margin-left:510px;
            font-size:14px;
        }
        // .summary th{
        //    background:#f89c1c;
        //    color:#011f7f;
        // }

        .summary th {
            text-align: right;
        }
        .summary td {
            text-align: right;
        }
            .summary th, .summary td {
            
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        
        <img src="assets/images/head.png"  style="width:100%" />
       
           <h2 style=" margin-left:30px; margin-top:-100px; color:white">Invoice </h2>
           <div style=" margin-top:0px; margin-left:30px; color:white"> <span class="label">Invoice #:</span> ' . $invoiceNo . '</div>
           <div style=" margin-top:50px; "> </div>

           <h4 style=" margin-left:5px; color:#001f80">Bill To </h4>

        <table class="bill-to">
            <tr>
                <td><span class="label">Date:</span> ' . $date . '</td>
                <td style="text-align:right"><span class="label">Invoice No:</span> ' . $invoiceNo . '</td>
            </tr>
            <tr>
                <td><span class="label">Company:</span> ' . $company . '</td>
                <td style="text-align:right"><span class="label">Phone No:</span> ' . $phone . '</td>
            </tr>
            <tr>
                <td><span class="label">Address:</span> ' . $address . '</td>
                <td style="text-align:right"><span class="label">ABN:</span> ' . $abn . ' &nbsp;&nbsp;&nbsp; <span class="label">Postal Code:</span> ' . $postalCode . '</td>
            </tr>
            <tr>
                <td colspan="2"><span class="label">RunSheet No:</span> ' . $runSheetNo . '</td>
            </tr>
        </table>

        

        <!-- Description Table -->
        <table class="details">
            <thead>
                <tr>
                    <th>Customer INV No</th>
                    <th>Items Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>';
// Dynamically generating rows from groupedItems
foreach ($groupedItems as $itemRowId => $data) {
    $customInvoiceNo = $data['custom_invoice_no']; // Fetch custom invoice number
    $items = $data['items']; // Fetch items

    $html .= '
        <tr>
            <td>' . htmlspecialchars($customInvoiceNo) . '</td> <!-- Display custom invoice number -->
            <td>
                <table class="checkbox-table">
                    <tr>';

    // Generate each item name and its value
    foreach ($items as $name => $value) {
        $html .= '
            <td>
                <div style="display:flex">
                    <img src="assets/images/check.png" width="20" height="20" style="padding-top:5px; padding-bottom:5px;" />
                    <div style="font-size:12px;">' . htmlspecialchars($name) . '</div>
                </div>
            </td>';
    }

    $html .= '
                    </tr>
                    <tr>';

    // Generate corresponding values for each item
    foreach ($items as $value) {
        $html .= '<td><span style="font-size:12px;">$' . htmlspecialchars($value) . '</span></td>';
    }

    $html .= '
                    </tr>
                </table>
            </td>
            <td>$' . array_sum($items) . '</td>
        </tr>';
}

$html .= '
            </tbody>
        </table>
       

            <table class="summary" >

                    <tr>
                        <th style="background:#f89c1c;  color:#011f7f;"> Sub Total</td>
                        <td> ' . $sub_total . '</td>
                    </tr>
                
                    <tr>
                        <th> Tax Rate  </td>
                        <td> ' . $tax_rate . '</td>
                    </tr>
                    <tr>
                        <th> Other Costs </td>
                        <td> ' . $other_cost . '</td>
                    </tr>
                
                    <tr>
                        <th style="background:#f89c1c;  color:#011f7f;"> Total Cost </td>
                        <td> ' . $total_cost . '</td>
                    </tr>
                    
                </table>

        <div class="footer">
            <div class="footer-text">

                <p>Make All Cheques Payable To Fab Transport Services Pty Ltd</p>
                <p>If You Have Any Questions Concerning This Invoice, Contact SAM</p>
                <p>Phone: 0403729966 | Email: info@fabtransport.com.au</p>
                <p>Thank You For Your Business!</p>
            </div>

             

        </div>
    </div>
</body>
</html>
';

try {
    $mpdf = new Mpdf([
        'margin_top' => 0,
        'margin_bottom' => 0,
        'margin_left' => 0,
        'margin_right' => 0,
    ]);
    $mpdf->WriteHTML($html);
    $mpdf->Output('invoice.pdf', 'I');
} catch (Exception $e) {
    echo $e->getMessage();
}
