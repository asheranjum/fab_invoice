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
$sqlInvoice = "SELECT * FROM invoices WHERE id = ?";
$stmt = $conn->prepare($sqlInvoice);
$stmt->bind_param("i", $invoiceId);
$stmt->execute();
$resultInvoice = $stmt->get_result();

$invoice = $resultInvoice->fetch_assoc();

if (!$invoice) {
    die('Invoice not found');
}

// Fetch Invoice Items
$sqlItems = "SELECT * FROM invoice_items WHERE invoice_id = ?  ORDER BY created_at ASC";
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
    $note = $row['note_text'];
    $customInvoiceNo = $row['customer_invoice_no'];
    $customInvoiceName = $row['customer_invoice_name'];

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
            'customInvoiceName' => $customInvoiceName,
            'note_text' => $note,
            'items' => []
        ];
    }

    // Add the item with its price
    $groupedItems[$runsheetNumber]['items'][$itemRowId]['items'][$itemName] = $itemValue;
}

// print_r($groupedItems);
// die();

// Dynamic Values
$date =  date("d-m-Y", strtotime($invoice['date']));
$invoiceNo = $invoice['invoice_number'];
$company = $invoice['company_name'];
$invoice_type = $invoice['invoice_type'];
$address = $invoice['address'];
$phone = $invoice['phone'];
$abn = $invoice['abn'];

$employer_company = $invoice['employer_company'];
$employer_abn = $invoice['employer_abn'];
$employer_address = $invoice['employer_address'];
$employer_phone = $invoice['employer_phone'];

$postalCode = $invoice['postal_code'];
$runSheetNo = $invoice['runsheet_number'];

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
            margin-bottom: 10px;
        }
        .bill-to {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0px;
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
           .runsheet-header { background: #f89c1c; color: #011f7f; font-weight: bold; padding: 10px; }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        
        <img src="assets/images/head.png"  style="width:100%" />
       
           <h2 style=" margin-left:30px; margin-top:-100px; color:white">TAX INVOICE</h2>
           <div style=" margin-top:0px; margin-left:30px; color:white"> <span class="label">Invoice No:</span> ' . $invoiceNo . '</div>
           <div style=" margin-top:20px; "> </div>
           <h4 style=" margin-left:5px; color:#001f80" ><span class="label">INVOICE DATE:</span> ' . $date . '</h4>
           <h2 style=" margin-left:5px; color:#001f80">Bill To </h2>
        <table class="bill-to">
            <tr>
                <td><span class="label">COMPANY NAME:</span> ' . $company . '</td>
                <td style="text-align:right"><span class="label">'.$employer_company .'</span></td>
            </tr>
            

            <tr>
                <td><span class="label">ADDRESS:</span> ' . $address . '</td>
                <td style="text-align:right"><span class="label">PHONE:'.$employer_phone.'</span></td>
            </tr>
            
            <tr>
                <td><span class="label">ABN:</span>' . $abn . '</td>
                <td style="text-align:right"><span class="label">ABN:'.$employer_abn.'</span></td>
            </tr>

            <tr>
                <td><span class="label">PHONE:</span>' . $phone . '</td>
                <td style="text-align:right"><span class="label">ADDRESS: '.$employer_address.'</span></td>
            </tr>
            <tr>
                <td><span class="label">Invoice Type:</span>' . $invoice_type . '</td>
            </tr>

             <tr>
              <td><span class="label"></span> </td>
                 <td style="text-align:right">
                  <div class="service-items">
                  <h3>For:</h3>
                 <ol>
                 <li>ASSEMBLY</li>
                 <li>DELIVERY</li>
                 <li>REPAIRS</li>
                </ol>
             </div>
          </td>
        </tr>
        </table>

        <!-- Description Table -->

        <table class="details">
            <thead>
                <tr>
                    <th>CUSTOMERS INFO </th>
                    <th>DESCRIPTION & CHARGES</th>
                 
                    <th>AMOUNT</th>
                </tr>
            </thead>
            <tbody> ';
// Dynamically generating rows from groupedItems
foreach ($groupedItems as $runsheetNumber => $runsheetData) {
    $runsheetDate = $runsheetData['runsheet_date'];

    // Add Runsheet Header
    $html .= '
    <tr class="runsheet-header">
       <td colspan="3">RUNSHEET NO: ' . $runsheetNumber . ' | RUNSHEET DATE: ' . $runsheetDate . '</td>
    </tr>';

    foreach ($runsheetData['items'] as $itemRowId => $data) {
        $customInvoiceName2 = $data['customInvoiceName'];
        $customInvoiceNo = $data['custom_invoice_no'];
        $note_text = $data['note_text'];
        $items = $data['items'];
       
        $html .= '
        <tr >
            <td style="text-align: left; width: 15%;">' . htmlspecialchars($customInvoiceName2). '<br>' .  $customInvoiceNo  . ' </td>
            <td style="padding:0px; width: 73%;">
                <table class="checkbox-table">
                    <tr>';

        $allOptions = $items; // Only use items fetched from DB
        // Check for any key matching P/UP(x)
        $selectedPUP = null; // To store the matched P/UP key
        foreach ($items as $key => $value) {
            if (preg_match('/^P\/UP\(\d+\)$/', $key)) {
                $selectedPUP = $key; // Match the first found P/UP key
                break;
            }
        }

    
        foreach ($allOptions as $key => $label) {
            $checked = isset($items[$key]);
            $image = $checked ? 'assets/images/check.png' : 'assets/images/uncheck.png';
            $value = '-';
            if ($checked) {
                if (is_numeric($items[$key])) {
                    $value = '$' . number_format((float)$items[$key], 2);
                } else {
                    $value = $items[$key]; // show string as is
                }
            }
            $html .= '
            <td>
                <div style="display:flex; align-items:center;">
                    <img src="' . $image . '" width="20" height="20" style="padding-top:5px; padding-bottom:5px;" />
                    <div style="font-size:12px; margin-left:5px;">' . htmlspecialchars($key) . '</div>
                </div>
               
            </td> '
            ;
        }

        $html .= '

        <td style="text-align:left; font-size:12px;"><b>Text</b></td>
        </tr>
        <tr>';

        // Add a second row to display the values for each option
        foreach ($allOptions as $key => $label) {
            $value = isset($items[$key]) ? $items[$key] : '0.00';
            $displayValue = is_numeric($value) ? '$' . number_format((float)$value, 2) : htmlspecialchars($value);
            $html .= '<td><span style="font-size:12px;">' . $displayValue . '</span></td>';
        }
        

        $html .= '
        <td style="text-align:left; font-size:12px;">' . nl2br(htmlspecialchars($note_text)) . '</td>
                    </tr>
                </table>
            </td>
            <td style="width: 12%;">$' . number_format(array_sum($items), 2) . '</td>
        </tr>';
    }
}

$html .= '
            </tbody>
        </table>
            <table class="summary">
                    <tr>
                        <th style="background:#f89c1c;  color:#011f7f;">Total</td>
                        <td style="color:#011f7f; font-weight:bold;">$' . $sub_total . '</td>
                    </tr>
                
                    <tr>
                     <th style="color:#011f7f;"> Tax Rate </td>
                        <td style="color:#011f7f; font-weight:bold;">$' . $tax_rate . '</td>
                    </tr>

                    <tr>
                        <th style="background:#f89c1c;  color:#011f7f;"> Total Cost </td>
                        <td style="color:#011f7f; font-weight:bold;">$' . $total_cost . '</td>
                    </tr>
                </table>

        <div class="footer">
            <div class="footer-text">
                <p>Make all checks payable to "FAB TRANSPORT SERVICES PTY LTD"
                        If you have any questions concerning about this invoice,
                        use the following contact information</p>
                    <ul>
                        <li>Contact Name: SAM</li>
                        <li>Phone: 0403 729 966</li>
                        <li>Email: info@fabtransport.com.au</li>
                    </ul>
                    <h4>Thank You For Your Business!</h4>
            </div>
        </div>
    </div>
</body>
</html>
';

try {
    // Estimate content height based on item count
    $itemCount = count($groupedItems) * 20; // Approximate row height
    $baseHeight = 297; // A4 standard height in mm
    $maxHeight = $baseHeight + ($itemCount > 20 ? ($itemCount - 20) * 5 : 0); // Increase height dynamically

    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => [210, $maxHeight], // 210mm width, dynamic height
        'margin_top' => 0,
        'margin_bottom' => 0,
        'margin_left' => 0,
        'margin_right' => 0,
    ]);

    // Prevent page breaks
    $mpdf->SetAutoPageBreak(false);
    $mpdf->WriteHTML($html);
    $mpdf->Output('invoice.pdf', 'I');

    // echo $html;
} catch (Exception $e) {
    echo $e->getMessage();
}
