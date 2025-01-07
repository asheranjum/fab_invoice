<?php
require 'vendor/autoload.php';

use Mpdf\Mpdf;

// Dynamic Values
$date = "12-Dec-2024";
$invoiceNo = "Fab#12123312";
$company = "Fabtransport Pvt Ltd";
$address = "ABC Street Xyz Road";
$phone = "000 111 999";
$abn = "21988772128";
$postalCode = "3232";
$runSheetNo = "212121331";

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

        .summary {
            margin-bottom: 20px;
        }
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
// Dynamically generating 10 rows with checkboxes and values
for ($i = 1; $i <= 4; $i++) {
    $html .= '
                <tr>
                    <td>Invoice #<br / >00000'.$i.'</td>
                    <td>
                        <table class="checkbox-table">
                          
                            <tr>
                                <td > <div style="display:flex"> <img src="assets/images/check.png" width="20" height="20"  style="padding-top:5px; padding-bottom:5px;" /> <div style="font-size:12px;"> DELIV</div> </div></td>
                                <td > <div style="display:flex"> <img src="assets/images/check.png" width="20" height="20"  style="padding-top:5px; padding-bottom:5px;" /> <div style="font-size:12px;"> DELIV</div> </div></td>
                                <td > <div style="display:flex"> <img src="assets/images/check.png" width="20" height="20"  style="padding-top:5px; padding-bottom:5px;" /> <div style="font-size:12px;"> DELIV</div> </div></td>
                                <td > <div style="display:flex"> <img src="assets/images/check.png" width="20" height="20"  style="padding-top:5px; padding-bottom:5px;" /> <div style="font-size:12px;"> DELIV</div> </div></td>
                                <td > <div style="display:flex"> <img src="assets/images/check.png" width="20" height="20"  style="padding-top:5px; padding-bottom:5px;" /> <div style="font-size:12px;"> DELIV</div> </div></td>
                                <td > <div style="display:flex"> <img src="assets/images/check.png" width="20" height="20"  style="padding-top:5px; padding-bottom:5px;" /> <div style="font-size:12px;"> DELIV</div> </div></td>
                                <td > <div style="display:flex"> <img src="assets/images/check.png" width="20" height="20"  style="padding-top:5px; padding-bottom:5px;" /> <div style="font-size:12px;"> DELIV</div> </div></td>
                                <td > <div style="display:flex"> <img src="assets/images/check.png" width="20" height="20"  style="padding-top:5px; padding-bottom:5px;" /> <div style="font-size:12px;"> DELIV</div> </div></td>
                                <td > <div style="display:flex"> <img src="assets/images/check.png" width="20" height="20"  style="padding-top:5px; padding-bottom:5px;" /> <div style="font-size:12px;"> DELIV</div> </div></td>
                                <td > <div style="display:flex"> <img src="assets/images/check.png" width="20" height="20"  style="padding-top:5px; padding-bottom:5px;" /> <div style="font-size:12px;"> DELIV</div> </div></td>
                                <td > <div style="display:flex"> <img src="assets/images/check.png" width="20" height="20"  style="padding-top:5px; padding-bottom:5px;" /> <div style="font-size:12px;"> DELIV</div> </div></td>
                                <td > <div style="display:flex"> <img src="assets/images/check.png" width="20" height="20"  style="padding-top:5px; padding-bottom:5px;" /> <div style="font-size:12px;"> DELIV</div> </div></td>
                              
                               
                            </tr>
                            <tr>
                                <td > <span style="font-size:12px;"> $20</span></td>
                                <td > <span style="font-size:12px;"> $20</span></td>
                                <td > <span style="font-size:12px;"> $20</span></td>
                                <td > <span style="font-size:12px;"> $20</span></td>
                                <td > <span style="font-size:12px;"> $20</span></td>
                                <td > <span style="font-size:12px;"> $20</span></td>
                                <td > <span style="font-size:12px;"> $20</span></td>
                                <td > <span style="font-size:12px;"> $20</span></td>
                                <td > <span style="font-size:12px;"> $20</span></td>
                                <td > <span style="font-size:12px;"> $20</span></td>
                                <td > <span style="font-size:12px;"> $20</span></td>
                                <td > <span style="font-size:12px;"> $20</span></td>
                            </tr>
                        </table>
                    </td>
                    <td>$' . (50 + ($i * 10)) . '.00</td>
                </tr>';
}
$html .= '
            </tbody>
        </table>
         <table class="summary">
            <tr>
                <th>Total:</th>
                <td>$' . (10 * 100) . '.00</td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
           <p>Make All Cheques Payable To Fab Transport Services Pty Ltd</p>
           <p>If You Have Any Questions Concerning This Invoice, Contact SAM</p>
           <p>Phone: 0403729966 | Email: info@fabtransport.com.au</p>
           <p>Thank You For Your Business!</p>
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
