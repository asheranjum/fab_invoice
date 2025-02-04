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
       

        .footer-text ul {
          list-style-type: none; 
          text-align: center;
        }

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
        .service-items {
          position: relative;
           top: 60%;
           left: 65%;
       }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        
        <img src="assets/images/head.png"  style="width:100%" />
       
           <h2 style=" margin-left:30px; margin-top:-100px; color:white">TAX INVOICE</h2>
           <div style=" margin-top:0px; margin-left:30px; color:white"> <span class="label">Invoice No:</span> ' . $invoiceNo . '</div>
           <div style=" margin-top:50px; "> </div>
           <h2 style=" margin-left:5px; color:#001f80">Bill To </h2>

        <table class="bill-to">
            <tr>
                <td><span class="label">Date:</span> ' . $date . '</td>
                <td style="text-align:right"><span class="label">FAB TRANSPORT SERVICES PTY LTD</span></td>
            </tr>

            <tr>
                <td><span class="label">Company Name:</span> ' . $company . '</td>
                <td style="text-align:right"><span class="label">PHONE:0403729966</span></td>
            </tr>
            
            <tr>
                <td><span class="label">Address:</span> ' . $address . '</td>
                <td style="text-align:right"><span class="label">ABN:123 121 211 222 222</span></td>
            </tr>

            <tr>
                <td><span class="label">Phone:</span> ' . $phone . '</td>
                <td style="text-align:right"><span class="label">ADDRESS: 5 LOUIS STREET DOVETON 3177 VIC</span></td>
            </tr>

             <tr>
                <td><span class="label">ABN:</span>' . $abn . '</td>
                 <td style="text-align:right">
                  <div class="service-items">
                  <h3>For:</h3>
                 <ol>
                 <li>Assembly</li>
                 <li>Delivery</li>
                 <li>Repairs</li>
                </ol>
             </div>
          </td>

            <tr>
                <td colspan="2"><span class="label">RunSheet No:</span> ' . $runSheetNo . '</td>
            </tr>

        </table>

        <!-- Description Table -->
        <table class="details">
            <thead>
                <tr>
                    <th>Customer Invoice No</th>
                    <th>Items Description</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody> <tr>
                            <th colspan="3">
                                <div style=" gap: 50px; display: flex;">
                                    <strong>Runsheet No:    <span id="runsheet_no"></span> </strong>
                                    <strong>Runsheet Date:  <span id="runsheet_date"></span> </strong>
                                </div>
                            </th>
                        </tr>';
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
    $allOptions = [
        'DELIV+' => 'DELIV+',
        'DISAS+' => 'DISAS+',
        'ASSEM+' => 'ASSEM+',
        'RUB+' => 'RUB+',
        'UPST+' => 'UPST+',
        'DOWNST+' => 'DOWNST+',
        'PREM+' => 'PREM+',
        'BRTrans+' => 'BRTrans+',
        'Ins+' => 'Ins+',
        'H/Dliv+' => 'H/Dliv+',
        'Vol+' => 'Vol+',
        'WaterCon+' => 'WaterCon+',
        'Door/R+' => 'Door/R+',

    ];

    // Dynamically add P/UP options from 1 to 6
    // for ($i = 1; $i <= 6; $i++) {
    //     $allOptions["P/UP($i)"] = "P/UP($i)";
    // }

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



    // Loop through all options
    foreach ($allOptions as $key => $label) {
        $image = isset($items[$key]) ? 'assets/images/check.png' : 'assets/images/uncheck.png';
        $value = isset($items[$key]) ? htmlspecialchars($items[$key]) : '-';

        // For P/UP, display the selected value directly
        $html .= '
    <td>
        <div style="display:flex; align-items:center;">
            <img src="' . $image . '" width="20" height="20" style="padding-top:5px; padding-bottom:5px;" />
            <div style="font-size:12px; margin-left:5px;">' . htmlspecialchars($label) . '</div>
        </div>
    </td>';
    }

    $html .= '
        </tr>
        <tr>';

    // Add a second row to display the values for each option
    foreach ($allOptions as $key => $label) {
        $value = isset($items[$key]) ? htmlspecialchars($items[$key]) : '0.00';

        $html .= '<td><span style="font-size:12px;">$' . $value . '</span></td>';
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
                        <th style="background:#f89c1c;  color:#011f7f;">Total</td>
                        <td style="color:#011f7f; font-weight:bold;">$'. $sub_total .'</td>
                    </tr>
                
                    <tr>
                     <th style="color:#011f7f;"> Tax Rate </td>
                        <td style="color:#011f7f; font-weight:bold;">$'. $tax_rate .'</td>
                    </tr>

                    <tr>
                        <th style="background:#f89c1c;  color:#011f7f;"> Total Cost </td>
                        <td style="color:#011f7f; font-weight:bold;">$'. $total_cost .'</td>
                    </tr>
                </table>

        <div class="footer">
            <div class="footer-text">
                <p>Make all checks payable to "FAB TRANSPORT SERVICES PTY LTD"
                        If you have any questions concerning about this invoice,
                        use the following contact information</p>
                    <ul>
                        <li>Contact Name: John</li>
                        <li>Phone: 8888 999 000</li>
                        <li>Email: example@gmail.com</li>
                    </ul>
                    <h4>Thank You For Your Business!</h4>
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
