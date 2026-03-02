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
$sqlItems = "SELECT * FROM invoice_items WHERE invoice_id = ?  ORDER BY row_position ASC";
$stmt = $conn->prepare($sqlItems);
$stmt->bind_param("i", $invoiceId);
$stmt->execute();
$resultItems = $stmt->get_result();

$groupedItems = [];


while ($row = $resultItems->fetch_assoc()) {
    $runsheetNumber = $row['runsheet_number'];
    $runsheetKey = $row['runsheet_number'] . '_' . $row['runsheet_date'];
    $runsheetDate = $row['runsheet_date'];
    $itemRowId = $row['row_position'];
    $itemName = $row['item_name'];
    $itemValue = $row['item_value'];
    $note = $row['note_text'];
    $customInvoiceNo = $row['customer_invoice_no'];
    $customInvoiceName = $row['customer_invoice_name'];

    // Initialize runsheet group
    if (!isset($groupedItems[$runsheetKey])) {
        $groupedItems[$runsheetKey] = [
            'runsheet_date' => $runsheetDate,
            'runsheet_number' => $runsheetNumber,
            'items' => []
        ];
    }

    // Initialize item row
    if (!isset($groupedItems[$runsheetKey]['items'][$itemRowId])) {
        $groupedItems[$runsheetKey]['items'][$itemRowId] = [
            'custom_invoice_no' => $customInvoiceNo,
            'customInvoiceName' => $customInvoiceName,
            'note_text' => $note,
            'items' => []
        ];
    }

    // Add the item with its price
    $groupedItems[$runsheetKey]['items'][$itemRowId]['items'][$itemName] = $itemValue;
}


$totalItems = 0;

foreach ($groupedItems as $runsheet) {
    if (isset($runsheet['items']) && is_array($runsheet['items'])) {
        $totalItems += count($runsheet['items']);
    }
}


// print_r($groupedItems);
// die();

// Dynamic Values
$date =  date("d-m-Y", strtotime($invoice['date']));
$invoiceNo = $invoice['invoice_number'];
$company = $invoice['company_name'];
$trading = $invoice['trading_as'];
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
            padding: 0px;
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
            border: 1px solid #011f7f;
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
         border: 1px solid #011f7f;
         padding: 10px;
         text-align: left;
        }
        
        .service-items {
          position: relative;
           top: 60%;
           left: 65%;
       }
         
         .runsheet-header { 
            background: #f89c1c; 
            color: #011f7f;
            font-weight: bold; 
            padding: 10px; 
         }
         
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        
        <img src="assets/images/head.png"  style="width:100%" />
       
           <h1 style=" margin-left:30px; margin-top:-100px; color:white">TAX INVOICE</h1>
           <p style=" margin-top:63px; margin-left:5px; color:#001f80; font-weight:bold; font-size:16px " ><span class="label">AMOUNT DUE:</span>$' . $total_cost . '</p>
           
          <table class="bill-to">
          
           <tr>  
                <td><h2 class="label" style="font-weight:bold">Bill To</h2></td>
                <td style="text-align:right; font-weight:bold; font-size:15px"><span class="label">INVOICE NUMBER:</span> ' . $invoiceNo . '</td>
           </tr>
          
            <tr>
                <td><span class="label" style="font-weight:bold">COMPANY NAME:</span> ' . $company . '</td>
                <td style="text-align:right; font-weight:bold; font-size:14.5px"><span class="label">INVOICE DATE:</span> ' . $date . '</td> 
            </tr>
            
            <tr>
                <td><span class="label" style="font-weight:bold">TRADING AS:</span>' . $trading . '</td>
                <td style="text-align:right"><span class="label" style="font-weight:bold;">COMPANY NAME:</span>' . $employer_company . '</td>
            </tr>
            
            <tr>
                <td><span class="label" style="font-weight:bold">ABN:</span>' . $abn . '</td>
                <td style="text-align:right"><span class="label" style="font-weight:bold;">ABN:</span>' . $employer_abn . '  </td>
            </tr>

            <tr>
                <td><span class="label" style="font-weight:bold">PHONE:</span>' . $phone . '</td>
                <td style="text-align:right"><span class="label" style="font-weight:bold;">PHONE:</span>' . $employer_phone . '</td>
            </tr>

             <tr>
                <td><span class="label" style="font-weight:bold">ADDRESS:</span> ' . $address . '</td>
                <td style="text-align:right"><span class="label" style="font-weight:bold;">ADDRESS:</span>' . $employer_address . '</td>
            </tr>

           <tr>
         <td></td>
                 
        </tr>
        </table>

        <!-- Description Table -->

        <table class="details" >
            <thead>
                <tr>
                    <th>CUSTOMERS INFO </th>
                    <th>DESCRIPTION & CHARGES</th>
                    <th>AMOUNT</th>
                </tr>
            </thead>
            <tbody > ';

// Dynamically generating rows from groupedItems
foreach ($groupedItems as $runsheetNumber => $runsheetData) {
    $runsheetDate = $runsheetData['runsheet_date'];
    $runsheetNumber1 = $runsheetData['runsheet_number'];

    // Add Runsheet Header
    $html .= '
    <tr class="runsheet-header">
       <td colspan="3">RUNSHEET NO: ' . $runsheetNumber1 . ' | RUNSHEET DATE: ' . $runsheetDate . '</td>
    </tr>';

    foreach ($runsheetData['items'] as $itemRowId => $data) {
        $customInvoiceName2 = $data['customInvoiceName'];
        $customInvoiceNo = $data['custom_invoice_no'];
        $note_text = $data['note_text'];
        $items = $data['items'];

        $html .= '
        <tr >
            <td style="  text-align: left; width: 20.5%; padding:0px 4px;">Account: ' . htmlspecialchars($customInvoiceName2) . ' <br> Invoice# ' . $customInvoiceNo . '</td>
            <td style="padding:0px; width: 73%; ">
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
            <td style="padding:2px 6px;">
                <div style="display:flex; align-items:center;  ">
                    <img src="' . $image . '" width="15" height="15" style="padding-top:0px; padding-bottom:0px; " />
                    <div style="font-size:12px; margin-left:5px; padding: 0px 0px 0px 0px; line-height: 1.1; ">' . htmlspecialchars($key) . '</div>
                </div>
               
            </td> ';
        }

        if (!empty(trim($note_text))) {
            $html .= '
        <td style=" text-align:left; font-size:12px; border-left:1px solid #011f7f; padding: 0px 0px 0px 5px;"><b>Note:</b> ' . nl2br(htmlspecialchars($note_text)) . '</td>';
        }

        $html .= '
        </tr>
        <tr ><span style="font-size:12px; ">';

        // Add a second row to display the values for each option
        foreach ($allOptions as $key => $label) {
            $value = isset($items[$key]) ? $items[$key] : '0.00';
            $displayValue = is_numeric($value) ? '$' . number_format((float)$value, 2) : htmlspecialchars($value);
            $html .= '<td style="padding: 0px 0px 2px 0px; line-height: 1.1; "><span style="font-size:12px; ">' . $displayValue . '</span></td>';
        }

        if (!empty(trim($note_text))) {
            $html .= '<td style=" text-align:left; font-size:12px; border-left:1px solid #011f7f; padding: 0px;"></td>';
        }

        $html .= '
                </tr>
            </table>
        </td>
        <td style="width: 6%;">$' . number_format(array_sum($items), 2) . '</td>
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
                      <th style="background:#f89c1c;  color:#011f7f;"> Total Including GST </td>
                      <td style="color:#011f7f; font-weight:bold;">$' . $total_cost . '</td>
                    </tr>
                </table>
    ';
 
    if($totalItems == 3 || $totalItems == 4 || $totalItems ==  16)
    {
            $html .= '<pagebreak />';
    }
    
    $html .= '
 
      <img src="assets/images/footer.png" />

       </div>
     </body>
   </html>

  ';

// echo $html;

// <div class="footer">
//     <div class="footer-text">
//         <p>Make All Cheques Payable to "FAB TRANSPORT SERVICES PTY LTD"
//             For Online Payments.</p>
//         <p>Account Name:FAB TRANSPORT, BSB:063 608, Account No:10844802.</p>
//         <p>If You Hav Any Concerning About This Invoice, Use The Following Contact Information.</p>
//         <ul>
//             <li>Email: info@fabtransport.com.au</li>
//         </ul>
//         <h3 style="margin-left:50px">Thank You For Your Business!</h3>
//     </div>
// </div>
     
try {
    // Estimate content height based on item count
    $itemCount = $totalItems * 20; // Approximate row height
    $baseHeight = 297; // A4 standard height in mm
    $maxHeight = $baseHeight + ($itemCount > 20 ? ($itemCount - 20) * 5 : 0); // Increase height dynamically

    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        // 'format' => [210, $maxHeight], // 210mm width, dynamic height
        'format' => 'A4', // 210mm width, dynamic height
        'margin_top' => 5,
        'margin_bottom' => 5,
        'margin_left' => 5,
        'margin_right' => 5,
    ]);

    $mpdf->WriteHTML($html);
    $mpdf->Output('invoice.pdf', 'I');

    echo $html;
} catch (Exception $e) {
    echo $e->getMessage();
}
