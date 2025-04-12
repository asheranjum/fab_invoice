<?php
require 'session.php';
require 'config/database.php';

$invoiceId = isset($_GET['invoice_id']) ? intval($_GET['invoice_id']) : null;
$invoiceData = null;

if ($invoiceId) {
    $invoiceData = file_get_contents("http://localhost/fab_invoice/edit_invoice.php?invoice_id=$invoiceId");
    $invoiceData = json_decode($invoiceData, true);
    if (!$invoiceData['success']) {
        die("Invoice not found.");
    }
    $invoiceData = $invoiceData['invoice'];
}

$newInvoice = $invoiceData['invoice'] ?? '';

$groupedItems = [];

if (isset($invoiceData['items']) && is_array($invoiceData['items'])) {
    foreach ($invoiceData['items'] as $item) {
        $runsheetNumber = $item['runsheet_number'];
        $runsheetDate = $item['runsheet_date'];
        $itemRowId = $item['item_row_id'];
        $itemId = $item['id'];
        $itemName = $item['item_name'];
        $itemValue = $item['item_value'];
        $customInvoiceNo = $item['customer_invoice_no'];
        $customInvoiceName = $item['customer_invoice_name'];
        $createdAt = isset($item['created_at']) ? $item['created_at'] : null;

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
                'custom_invoice_name' => $customInvoiceName,
                'item_row_id' => $itemRowId,
                'items' => []
            ];
        }

        // Add the item with value, created_at timestamp, and item_id
        $groupedItems[$runsheetNumber]['items'][$itemRowId]['items'][$itemName] = [
            'value' => $itemValue,
            'created_at' => $createdAt,
            'item_id' => $itemId
        ];
    }
}

// print_r(json_encode($groupedItems));
// die();
// Close the connection
mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Invoice</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style-2.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>


    <!-- Edit Runsheet Modal -->
    <div class="modal fade" id="runsheetModal" tabindex="-1" aria-labelledby="runsheetModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="runsheetModalLabel">Edit Runsheet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="runsheetForm">
                        <div class="mb-3">
                            <label for="runsheetNumber" class="form-label">Runsheet Number</label>
                            <input type="number" class="form-control" id="runsheetNumber" name="runsheetNumber" required>
                        </div>
                        <div class="mb-3">
                            <label for="runsheetDate" class="form-label">Runsheet Date</label>
                            <input type="date" class="form-control" id="runsheetDate" name="runsheetDate" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveRunsheet">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Runsheet Modal -->
    <div class="modal fade" id="editRunsheetModal" tabindex="-1" aria-labelledby="editRunsheetModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRunsheetModalLabel">Edit Runsheet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editRunsheetForm">
                        <div class="mb-3">
                            <label for="editRunsheetNumber" class="form-label">Runsheet Number</label>
                            <input type="text" class="form-control" id="editRunsheetNumber" name="editRunsheetNumber" required>
                        </div>
                        <div class="mb-3">
                            <label for="editRunsheetDate" class="form-label">Runsheet Date</label>
                            <input type="date" class="form-control" id="editRunsheetDate" name="editRunsheetDate" required>
                        </div>
                        <input type="hidden" id="editRunsheetId">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveEditRunsheet">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Runsheet Modal -->
    <div class="modal fade" id="addRunsheetModal" tabindex="-1" aria-labelledby="addRunsheetModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRunsheetModalLabel">Add Runsheet</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addRunsheetForm">
                        <div class="mb-3">
                            <label for="addRunsheetNumber" class="form-label">Runsheet Number</label>
                            <input type="number" class="form-control" id="addRunsheetNumber" name="addRunsheetNumber" required>
                        </div>
                        <div class="mb-3">
                            <label for="addRunsheetDate" class="form-label">Runsheet Date</label>
                            <input type="date" class="form-control" id="addRunsheetDate" name="addRunsheetDate" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="addRunsheet">Add Runsheet</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container invoice-container mb-5">

        <div class="row">
            <div class="col-md-12">
                <div class="header-img">
                    <img src="assets/images/head.png" alt="invoice-header" style="width: 100%; ">
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-8">

                <form id="invoiceForm" class="form-group p-1">

                    <div class="top-nav">
                        <div class="topbtngr btn-group" role="group">
                            <button type="button" class="btn btn-dark add-runsheet-button">Add Runsheet</button>
                            <button type="button" class="btn btn-dark  add-bulk-button">Add Row</button>
                            <button type="button" class="btn btn-dark remove-bulk-button">Remove Row</button>
                        </div>
                        <div class=" btn-group">
                            <button type="submit" class="btn btn-success export-button">Update Invoice</button>
                            <!-- <button type="submit" class="btn btn-success export-button">Export Invoice</button> -->
                        </div>
                    </div>


                    <div class="mb-2 d-flex align-items-center">
                        <input type="hidden" name="invoice_id" value="<?php echo $invoiceId ?? ''; ?>">
                        <label for="date" class="form-label mb-0 me-2">DATE:</label>
                        <input type="date" name="date" id="invoice_date" class="form-control form-control-sm custom-width me-3" style="font-size: 18px;" value="<?php echo $invoiceData['date'] ?? ''; ?>">
                        <div class="invalid-feedback">Invoice date is required.</div>
                        <label for="invoice" class="form-label mb-0 me-2">INVOICE NO</label>
                        <input type="text" id="invoice" name="invoice" style="border: none; font-size: 18px;" value="<?php echo htmlspecialchars($newInvoice); ?>" readonly>
                    </div>

                    <h3 class="mt-1 mb-2 heading" style="display: inline-block; margin-right: 10px;">Bill To:</h3>
                    <?php $selectedType = $invoiceData['invoice_type'] ?? ''; // Get selected value from DB or form 
                    ?>
                    <select name="invoice_type" style="display: inline-block; position: relative; bottom: 5px; padding: 4px 8px; font-size: 16px; left: 420px;">
                        <option value="" disabled <?= $selectedType == '' ? 'selected' : '' ?>>Select Option</option>
                        <option value="Bedding" <?= $selectedType == 'Bedding' ? 'selected' : '' ?>>Bedding</option>
                        <option value="Furniture" <?= $selectedType == 'Furniture' ? 'selected' : '' ?>>Furniture</option>
                    </select>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="Company" class="form-label mb-0 me-3">COMPANY NAME:</label>
                        <input type="text" name="company" id="company_name" class="form-control w-50" placeholder="Type Company Name" value="<?php echo $invoiceData['company_name'] ?? ''; ?>">
                        <div class="invalid-feedback">Company name is required.</div>
                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="address" class="form-label mb-0 me-4">ADDRESS:</label>
                        <input type="text" name="address" id="company_address" class="form-control custom-width-2" placeholder="Enter Address Here" value="<?php echo $invoiceData['address'] ?? ''; ?>">
                        <div class="invalid-feedback">Address is required.</div>

                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="abn" class="form-label mb-0 me-5">ABN:</label>
                        <input type="text" name="abn" id="company_abn" class="form-control custom-width-1" placeholder="Insert ABN Number" value="<?php echo $invoiceData['abn'] ?? ''; ?>">
                        <div class="invalid-feedback">ABN is required.</div>
                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="phone" class="form-label mb-0 me-2">PHONE NO:</label>
                        <input type="text" name="phone" id="phone" class="form-control custom-width-1 me-3" placeholder="Insert Phone Number" value="<?php echo $invoiceData['phone'] ?? ''; ?>">
                        <div class="invalid-feedback">Phone is required.</div>
                    </div>


                </form>
            </div>


            <div class="col-md-4 position-relative">

                <div class="info">
                    <h6>FAB TRANSPORT SERVICES PTY LTD</h6>
                    <h6>PHONE: 0403729966</h6>
                    <h6>ABN: 123 121 211 222 222</h6>
                    <h6>ADDRESS: 5 LOUIS STREET 3177 VIC</h6>
                </div>

                <div class="service-items">
                    <h4>For:</h4>
                    <h6>1. ASSEMBLY</h6>
                    <h6>2. DELIVERY</h6>
                    <h6>3. REPAIRS</h6>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <table class="table table-bordered table-container">
                    <thead>
                        <tr>
                            <th>CUSTOMER'S INFO</th>
                            <th>DESCRIPTION & CHARGES</th>
                            <th>AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">

                        <tr style="display: none;">
                            <th colspan="3">
                                <div style=" gap: 50px; display: flex;">
                                    <strong>Runsheet No: <span id="runsheet_no"></span> </strong>
                                    <strong>Runsheet Date: <span id="runsheet_date"></span> </strong>
                                </div>
                            </th>
                        </tr>

                        <tr id="tabletr" class="tabletr" style="display: none;">

                            <td style="width: 180px;">
                                <input type="text" name="customer_inv_no[]" class="form-control customer-inv-no" placeholder="Enter Invoice No">
                                <input type="text" name="customer_inv_name[]" id="customer-inv-name" class="form-control customer-inv-name mt-2" placeholder="Enter Invoice Name">
                            </td>

                            <td>
                                <div class="d-flex">
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="deliv-0" name="item[0][deliv]">
                                        <label for="deliv-0" class="form-check-label">DELIV+</label>
                                        <input type="text" name="item[0][deliv_value]" class="form-control mt-1 numeric-only" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="disas-0" name="item[0][disas]">
                                        <label for="disas-0" class="form-check-label">DISAS+</label>
                                        <input type="text" name="item[0][disas_value]" class="form-control mt-1 numeric-only" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="assem-0" name="item[0][assem]">
                                        <label for="assem-0" class="form-check-label">ASSEM+</label>
                                        <input type="text" name="item[0][assem_value]" class="form-control mt-1 numeric-only" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="rub-0" name="item[0][rub]">
                                        <label for="rub-0" class="form-check-label">RUB+</label>
                                        <input type="text" name="item[0][rub_value]" class="form-control mt-1 numeric-only" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="upst-0" name="item[0][upst]">
                                        <label for="upst-0" class="form-check-label">UPST+</label>
                                        <input type="text" name="item[0][upst_value]" class="form-control mt-1 numeric-only" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="downst-0" name="item[0][downst]">
                                        <label for="downst-0" class="form-check-label">DOWNST+</label>
                                        <input type="text" name="item[0][downst_value]" class="form-control mt-1 numeric-only" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="prem-0" name="item[0][prem]">
                                        <label for="prem-0" class="form-check-label">PREM+</label>
                                        <input type="text" name="item[0][prem_value]" class="form-control mt-1 numeric-only" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="brtrans-0" name="item[0][brtrans]">
                                        <label for="brtrans-0" class="form-check-label">BRTRANS+</label>
                                        <input type="text" name="item[0][brtrans_value]" class="form-control mt-1 numeric-only" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="h_dliv-0" name="item[0][h_dliv]">
                                        <label for="h_dliv-0" class="form-check-label">H/DLIV+</label>
                                        <input type="text" name="item[0][h_dliv_value]" class="form-control mt-1 numeric-only" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="vol-0" name="item[0][vol]">
                                        <label for="vol-0" class="form-check-label">VOL+</label>
                                        <input type="text" name="item[0][vol_value]" class="form-control mt-1 numeric-only" disabled placeholder="">
                                    </div>

                                    <div class="form-check ">
                                        <select id="pup-0" name="item[0][pup]" class="form-contro">
                                            <option value="">P/UP</option>
                                            <option value="1">P/UP(1)</option>
                                            <option value="2">P/UP(2)</option>
                                            <option value="3">P/UP(3)</option>
                                            <option value="4">P/UP(4)</option>
                                            <option value="5">P/UP(5)</option>
                                            <option value="6">P/UP(6)</option>
                                            <option value="7">P/UP(7)</option>
                                            <option value="8">P/UP(8)</option>
                                            <option value="9">P/UP(9)</option>
                                            <option value="10">P/UP(10)</option>
                                        </select>

                                        <input type="text" name="item[0][pup_value]" class="form-control mt-1" disabled placeholder="">
                                    </div>
                                </div>

                            </td>
                            <td style="width: 180px;">
                                <input type="text" class="form-control amount-field" name="amount[]" readonly placeholder="$0.00">
                            </td>
                        </tr>


                        <?php foreach ($groupedItems as $runsheetNumber => $runsheetData): ?>

                            <tr id="runsheet-">
                                <th colspan="3" id='runsheet-data'>
                                    <div style="gap: 50px; display: flex;">

                                        <strong>Runsheet No: <span id="runsheet_no"><?= htmlspecialchars($runsheetNumber) ?></span> </strong>
                                        <strong>Runsheet Date: <span id="runsheet_date"><?= htmlspecialchars($runsheetData['runsheet_date']) ?></span> </strong>

                                        <button type="button" class="btn btn-warning btn-sm edit-runsheet-button"
                                            data-run-number="<?= htmlspecialchars($runsheetNumber) ?>"
                                            data-run-date="<?= htmlspecialchars($runsheetData['runsheet_date']) ?>">
                                            Edit
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm delete-runsheet-items"
                                            data-run-number="<?= htmlspecialchars($runsheetNumber) ?>"
                                            data-run-date="<?= htmlspecialchars($runsheetData['runsheet_date']) ?>">
                                            Delete
                                        </button>
                                    </div>
                                </th>
                            </tr>

                            <?php foreach ($runsheetData['items'] as $itemRowId => $data):

                                $itemId = $data['items'][$itemName]['item_id'] ?? null;
                            ?>

                                <tr id="table_exitisng" data-item-row-id="<?= $data['item_row_id'] ?>" data-runsheet-number="<?= htmlspecialchars($runsheetNumber) ?>" data-runsheet-date="<?= htmlspecialchars($runsheetData['runsheet_date']) ?>">
                                    <td>
                                        <input type="text" name="customer_invoice_no[]" placeholder="Enter Invoice No" class="form-control customer-inv-no" value="<?= htmlspecialchars($data['custom_invoice_no'] ?? '') ?>">
                                        <input type="text" name="customer_invoice_name[]" id="customer-inv-name" placeholder="Enter Invoice Name" class="form-control customer-inv-name" value="<?= htmlspecialchars($data['custom_invoice_name'] ?? '') ?>">
                                    </td>
                                    <td style="padding:0px">
                                        <table class="checkbox-table">
                                            <tr>
                                                <?php
                                                // Define all options
                                                $allOptions = [
                                                    'DELIV+' => 'DELIV+',
                                                    'DISAS+' => 'DISAS+',
                                                    'ASSEM+' => 'ASSEM+',
                                                    'RUB+' => 'RUB+',
                                                    'UPST+' => 'UPST+',
                                                    'DOWNST+' => 'DOWNST+',
                                                    'PREM+' => 'PREM+',
                                                    'BRTRANS+' => 'BRTRANS+',
                                                    'H/DLIV+' => 'H/DLIV+',
                                                    'VOL+' => 'VOL+',
                                                ];
                                                $pupOptions = [
                                                    '1' => 'P/UP(1)',
                                                    '2' => 'P/UP(2)',
                                                    '3' => 'P/UP(3)',
                                                    '4' => 'P/UP(4)',
                                                    '5' => 'P/UP(5)',
                                                    '6' => 'P/UP(6)',
                                                    '7' => 'P/UP(7)',
                                                    '8' => 'P/UP(8)',
                                                    '9' => 'P/UP(9)',
                                                    '10' => 'P/UP(10)'
                                                ];
                                                // Check for any key matching P/UP(x)
                                                $selectedPUP = null; // To store the matched P/UP key

                                                foreach ($data['items'] as $key => $item) {
                                                    if (strpos($key, 'P/UP') === 0) {
                                                        $selectedPUP = $key;
                                                        break;
                                                    }
                                                }

                                                // If a P/UP key is found, add it dynamically
                                                if ($selectedPUP) {
                                                    $allOptions[$selectedPUP] = $selectedPUP;
                                                } else {
                                                    $pupKey = 'P/UP';
                                                    $allOptions[$pupKey] = 'P/UP';
                                                }



                                                ?>

                                                <?php $totalValue = 0;
                                                foreach ($allOptions as $key => $label):
                                                    $value = isset($data['items'][$key]) ? (float)$data['items'][$key]['value'] : 0.00;
                                                    $totalValue += $value;
                                                ?>

                                                    <td>

                                                        <?php if (strpos($key, 'P/UP') === 0): ?>
                                                            <!-- Select Dropdown for P/UP options -->
                                                            <input type="hidden" name="item_id" value="<?= isset($data['items'][$key]['item_id']) ? $data['items'][$key]['item_id'] : '' ?>">
                                                            <select id="pup-<?= $key ?>" name="item[<?= $itemId ?>][pup]" class="form-contro" style="width:85px;">
                                                                <?php foreach ($pupOptions as $id => $label): ?>
                                                                    <option value="<?= $id ?>" <?= ($selectedPUP === "P/UP($id)") ? 'selected' : '' ?>>
                                                                        <?= $label ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>

                                                            <input type="text" name="item[<?= $itemId ?>][pup_value]" value="<?= isset($data['items'][$selectedPUP]) ? number_format((float)$data['items'][$selectedPUP]['value'], 2) : '' ?>" class="form-control mt-1">
                                                        <?php else: ?>

                                                            <div class="form-check">
                                                                <input type="hidden" name="item_id" value="<?= isset($data['items'][$key]['item_id']) ? $data['items'][$key]['item_id'] : '' ?>">
                                                                <input type="hidden" name="item[<?= $itemRowId ?>][item_id]" value="<?= $itemRowId ?>">
                                                                <input id="<?= $label . '-' . $itemRowId ?>" type="checkbox" class="form-check-input form-checkboxes" name="item[<?= $itemId ?>][<?= strtolower($label) ?>]" <?= isset($data['items'][$key]) ? 'checked' : '' ?>>
                                                                <label for="<?= $label . '-' . $itemRowId ?>" class="form-check-label"><?= htmlspecialchars($label) ?></label>
                                                                <input type="text" name="item[<?= $itemRowId ?>][<?= strtolower($label) ?>_value]" class="form-control mt-1" value="<?= isset($data['items'][$key]) ? number_format((float)$data['items'][$key]['value'], 2) : '' ?>">
                                                            </div>

                                                        <?php endif; ?>
                                                    </td>

                                                <?php endforeach; ?>
                                            </tr>
                                        </table>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control amount-field" name="amount[]" value="<?= number_format($totalValue, 2) ?>" readonly>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>




        <div class="row mt-1 footer-bg">
            <div class="col-md-8">
                <div class="footer mt-3">
                    <p>MAKE ALL CHECKS PAYABLE TO "FAB TRANSPORT SERVICES PTY LTD"
                        IF YOU HAVE ANY QUESTIONS CONCERNING THIS INVOICE,
                        USE THE FOLLOWING CONTACT INFORMATION</p>
                    <ul>
                        <li>CONTACT NAME: SAM</li>
                        <li>PHONE: 8888 999 000</li>
                        <li>EMAIL: example@gmail.com</li>
                    </ul>
                    <h4>THANK YOU FOR YOUR BUSINESS!</h4>
                </div>
            </div>

            <div class="col-md-4 mt-3 ">
                <table class="table-container-2 mb-3 ml-5">

                    <div class="form-group table-2-width">
                        <tr>
                            <td><strong><label for="sub_total" class="bold-label">TOTAL</label></strong></td>
                            <td><strong> <input type="text" id="sub_total" name="sub_total" class="form-control" readonly placeholder="$0.00"></strong></td>
                        </tr>
                    </div>

                    <div class="form-group table-2-width">
                        <tr>
                            <td><label for="tax_rate">TAX RATE</label></td>
                            <td><input type="text" id="tax_rate" name="tax_rate" class="form-control" value="<?php echo $invoiceData['tax_rate'] ?? ''; ?>" placeholder="$0.00"></td>
                        </tr>
                    </div>

                    <div class="form-group table-2-width">
                        <tr>
                            <td><strong><label for="total_cost" class="bold-label">TOTAL COST</label></strong></td>
                            <td><strong> <input type="text" id="total_cost" name="total_cost" class="form-control" readonly placeholder="$0.00"></strong></td>
                        </tr>
                    </div>
                </table>
            </div>
        </div>
    </div>

    <script>
        function getMaxItemRowId() {
            let maxId = 0;
            $(".table-container tbody tr#tabletr, .table-container tbody tr#table_exitisng").each(function() {
                const itemRowId = parseInt($(this).attr("data-item-row-id"));
                if (itemRowId > maxId) {
                    maxId = itemRowId;
                }
            });
            return maxId;
        }
        $(document).ready(function() {

            // $('#runsheet_no').append(<?php echo $invoiceId; ?> + '1001')

            // var today = new Date();
            // var formattedDate = today.toLocaleDateString(); // Format the date (e.g., "3/7/2025" in US format)

            // Append the date to the element
            // $('#runsheet_date').append(formattedDate);

            const maxRows = 25;
            let currentRunsheet = null; // Store current runsheet data
            let runsheetIndex = 0; // Unique identifier for runsheets
            //     $(".add-runsheet-button").click(function() {
            //         // Get Runsheet details using prompt()
            //         let runsheetNumber = prompt("Enter Runsheet Number:");
            //         if (runsheetNumber === null || runsheetNumber.trim() === "") return; // Exit if empty or canceled

            //         let runsheetDate = prompt("Enter Runsheet Date (YYYY-MM-DD):");
            //         if (runsheetDate === null || runsheetDate.trim() === "") return; // Exit if empty or canceled
            //         runsheetIndex++; // Increment unique runsheet index
            //         // Validate date format (basic check)
            //         if (!/^\d{4}-\d{2}-\d{2}$/.test(runsheetDate)) {
            //             alert("Invalid date format. Please use YYYY-MM-DD.");
            //             return;
            //         }

            //         // Store current runsheet details for new rows
            //         currentRunsheet = {
            //             number: runsheetNumber,
            //             date: runsheetDate
            //         };

            //         // Runsheet HTML template
            //         let runsheetRow = `

            //         <tr>
            //             <th colspan="3"  id="runsheet-${runsheetIndex}">
            //                 <div style=" gap: 50px; display: flex;">
            //                     <strong>Runsheet No: ${runsheetNumber}</strong>
            //                     <strong>Runsheet Date: ${runsheetDate}</strong>
            //                     <strong><button class="btn btn-danger btn-sm remove-runsheet" data-id="runsheet-${runsheetIndex}">Remove</button></strong>
            //                 </div>
            //             </th>
            //         </tr>
            //    `;
            //         // Append runsheet below the last item row
            //         $(".table-container #tbody").append(runsheetRow);
            //     });

            // Show Add Runsheet Modal
            $(".add-runsheet-button").click(function() {
                $("#addRunsheetNumber").val("");
                $("#addRunsheetDate").val("");
                $("#addRunsheetModal").modal("show");
            });

            $("#addRunsheet").click(function() {
                const runsheetNumber = $("#addRunsheetNumber").val();
                const runsheetDate = $("#addRunsheetDate").val();

                // Validate inputs
                if (!runsheetNumber || !runsheetDate) {
                    alert("Please fill in both Runsheet Number and Runsheet Date.");
                    return;
                }

                // Validate date format (basic check)
                if (!/^\d{4}-\d{2}-\d{2}$/.test(runsheetDate)) {
                    alert("Invalid date format. Please use YYYY-MM-DD.");
                    return;
                }

                currentRunsheet = {
                    number: runsheetNumber,
                    date: runsheetDate
                };

                // Create new runsheet row
                const runsheetIndex = new Date().getTime(); // Unique index based on timestamp
                const runsheetRow = `
            <tr id="runsheet-${runsheetIndex}">
                <th colspan="3" id="runsheet-${runsheetIndex}">
                    <div style="gap: 50px; display: flex;">
                         <strong>Runsheet No: <span id="runsheet_no">${runsheetNumber}</span> </strong>
                        <strong>Runsheet Date: <span id="runsheet_date">${runsheetDate}</span> </strong>
                        <strong><button class="btn btn-danger btn-sm edit-onpage-runsheet-button" data-id="runsheet-${runsheetIndex}" data-run-number="${runsheetNumber}>"
                                            data-run-date="${runsheetDate}">Edit</button></strong>
                        <strong><button class="btn btn-danger btn-sm remove-runsheet" data-id="runsheet-${runsheetIndex}">Remove</button></strong>
                    </div>
                </th>
            </tr>
        `;

                // Append runsheet below the last item row
                $(".table-container #tbody").append(runsheetRow);

                // Close modal
                $("#addRunsheetModal").modal("hide");
            });

            $(document).on("click", ".remove-runsheet", function() {
                let runsheetId = $(this).attr("data-id"); // Get the ID of the runsheet row
                $("#" + runsheetId).remove(); // Remove the respective runsheet row
            });


            $(document).on("click", ".edit-onpage-runsheet-button", function() {
                const button = $(this);
                const runsheetId = button.data("id");
                const runsheetNumber = button.data("run-number");
                const runsheetDate = button.data("run-date");

                $("#editRunsheetNumber").val(runsheetNumber);
                $("#editRunsheetDate").val(runsheetDate);
                $("#editRunsheetId").val(runsheetId);

                $("#editRunsheetModal").modal("show");
            });

            $("#saveEditRunsheet").click(function() {
                const runsheetNumber = $("#editRunsheetNumber").val();
                const runsheetDate = $("#editRunsheetDate").val();
                const runsheetId = $("#editRunsheetId").val();

                // Validate inputs
                if (!runsheetNumber || !runsheetDate) {
                    alert("Please fill in both Runsheet Number and Runsheet Date.");
                    return;
                }

                // Validate date format (basic check)
                if (!/^\d{4}-\d{2}-\d{2}$/.test(runsheetDate)) {
                    alert("Invalid date format. Please use YYYY-MM-DD.");
                    return;
                }

                const runsheetRow = $(`#${runsheetId}`);
                runsheetRow.find(".runsheet-no").text(runsheetNumber);
                runsheetRow.find(".runsheet-date").text(runsheetDate);
                runsheetRow.attr("data-run-number", runsheetNumber);
                runsheetRow.attr("data-run-date", runsheetDate);

                $("#editRunsheetModal").modal("hide");
            });

            function showAddRunsheetModal() {
                $("#addRunsheetNumber").val("");
                $("#addRunsheetDate").val("");
                $("#addRunsheetModal").modal("show");
            }


            function calculateRowAmount(row) {
                let amount = 0;

                $(row).find(".form-checkboxes").each(function() {
                    const inputField = $(this).closest(".form-check").find("input[type='text']");
                    const value = parseFloat(inputField.val()) || 0;

                    if (this.checked) amount += value;
                });

                const selectField = $(row).find(".form-contro");
                const selectValue = parseFloat(selectField.siblings("input[type='text']").val()) || 0;
                amount += selectValue;

                $(row).find(".amount-field").val(amount.toFixed(2));
                calculateSubTotal();
            }

            function calculateSubTotal() {
                let subTotal = 0;

                $(".amount-field").each(function() {
                    subTotal += parseFloat($(this).val()) || 0;
                });

                const taxRate = parseFloat($("#tax_rate").val()) || 0;
                const otherCost = parseFloat($("#other_cost").val()) || 0;
                const total = subTotal + taxRate + otherCost;

                $("#sub_total").val(subTotal.toFixed(2));
                $("#tax_rate").val(taxRate.toFixed(2));
                $("#total_cost").val(total.toFixed(2));
            }

            function attachRowListeners(row) {
                $(row).find(".form-checkboxes").off("change").on("change", function() {
                    const inputField = $(this).closest(".form-check").find("input[type='text']");
                    inputField.prop("disabled", !this.checked).val("");
                    calculateRowAmount(row);
                });

                $(row).find(".form-contro").off("change").on("change", function() {
                    const inputField = $(this).siblings("input[type='text']");
                    if ($(this).val() !== "") {
                        inputField.prop("disabled", false);
                    } else {
                        // inputField.prop("disabled", true).val("");
                    }
                    calculateRowAmount($(this).closest("tr"));
                });

                $(row).find(".form-check input[type='text']").off("input").on("input", function() {
                    calculateRowAmount(row);
                });

                $(row).find("input[type='text']").off("keypress").on("keypress", function(e) {
                    if (!/^[0-9.]+$/.test(e.key) && e.key !== "Backspace") {
                        e.preventDefault();
                    }
                });

                $(row).find("#customer-inv-name").off("keypress").on("keypress", function(e) {
                    // Allow all alphanumeric characters and special characters (including space)
                    if (!/^[\w\s.,;!?()\-"'&@#$%^*+=<>_/|\\`~]+$/.test(e.key) && e.key !== "Backspace") {
                        e.preventDefault();
                    }
                });


                // $(row).find(".form-check input[type='text']").prop("disabled", true);

                $(row).find(".form-check input[type='text']").each(function() {
                    if ($(this).val().trim() === '') {
                        $(this).prop("disabled", true); // Disable the input if the value is empty
                    } else {
                        $(this).prop("disabled", false); // Enable the input if the value is not empty
                    }
                });
                $(row).find(".form-contro").siblings("input[type='text']").prop("disabled", true);
            }




            function addRows(count) {
                const rows = $(".table-container #tbody tr#tabletr");
                let currentRows = rows.length;
                let newRows = Math.min(count, maxRows - currentRows);

                if (newRows <= 0) {
                    alert("You cannot add more than " + maxRows + " rows.");
                    return;
                }
                let maxItemRowId = getMaxItemRowId();
                for (let i = 0; i < newRows; i++) {
                    const lastRow = $(".table-container #tbody tr#tabletr").last();
                    console.log('lastRow', lastRow);
                    if (lastRow.length === 0) {
                        alert("No existing rows found to clone.");
                        return;
                    }

                    var lastRunsheetNumber = lastRow.attr("data-runsheet-number") || "";
                    var lastRunsheetDate = lastRow.attr("data-runsheet-date") || "";

                    if (lastRunsheetNumber == '' && lastRunsheetDate == '') {
                        const table_exitisng = $(".table-container #tbody tr#table_exitisng").last();

                        lastRunsheetNumber = table_exitisng.attr("data-runsheet-number") || "";
                        lastRunsheetDate = table_exitisng.attr("data-runsheet-date") || "";
                    }


                    const newRow = lastRow.clone();
                    newRow.removeAttr("style");
                    // maxItemRowId++;  
                    const rowIndex = $(".table-container #tbody tr#tabletr").length;
                    // newRow.attr("data-item-row-id", maxItemRowId);
                    newRow.find("input, select").each(function() {
                        if (this.type === "checkbox") {
                            this.checked = false;
                        } else if (this.type === "text" || this.type === "number") {
                            $(this).val(""); // Clear text or number inputs
                            $(this).prop("disabled", false); // Enable text inputs
                        }
                    });

                    newRow.find(".form-contro").prop("disabled", false);

                    newRow.find(".form-check").each(function() {
                        const labelText = $(this).find("label").text().trim();
                        const baseName = labelText.toLowerCase();
                        const uniqueId = `${baseName}-${rowIndex}`;

                        const checkbox = $(this).find("input[type='checkbox']");
                        checkbox.attr({
                            id: uniqueId,
                            name: `item[${rowIndex}][${baseName}]`
                        });

                        $(this).find("label").attr("for", uniqueId);

                        const inputField = $(this).find("input[type='text']");
                        inputField.attr("name", `item[${rowIndex}][${baseName}_value]`);
                    });

                    newRow.find(".form-contro").attr("name", `item[${rowIndex}][pup]`);
                    newRow.find(".form-contro").siblings("input[type='text']").attr("name", `item[${rowIndex}][pup_value]`).prop("disabled", true);
                    newRow.find(".amount-field").attr("name", `amount[${rowIndex}]`).val("");

                    // // ✅ Set Runsheet Data for the New Row
                    // newRow.attr("data-runsheet-number", lastRunsheetNumber);
                    // newRow.attr("data-runsheet-date", lastRunsheetDate);

                    // ✅ Use latest runsheet data if available
                    if (currentRunsheet) {
                        newRow.attr("data-runsheet-number", currentRunsheet.number);
                        newRow.attr("data-runsheet-date", currentRunsheet.date);
                    } else {
                        // Default to empty if no runsheet has been added
                        newRow.attr("data-runsheet-number", lastRunsheetNumber);
                        newRow.attr("data-runsheet-date", lastRunsheetDate);
                    }


                    $(".table-container #tbody").append(newRow);
                    attachRowListeners(newRow);
                }
            }

            function removeRows(count) {
                const rows = $(".table-container #tbody tr#tabletr");
                let currentRows = rows.length;

                if (currentRows <= 1) {
                    alert("You cannot remove the last row.");
                    return;
                }

                let removeCount = Math.min(count, currentRows - 1);
                if (removeCount > 0) {
                    if (confirm(`Are you sure you want to remove ${removeCount} row(s)?`)) {
                        for (let i = 0; i < removeCount; i++) {
                            $(".table-container #tbody tr#tabletr").last().remove();
                        }
                        calculateSubTotal();
                    }
                }
            }

            $(".add-button").click(function() {
                addRows(1);
            });

            $(".add-bulk-button").click(function() {

                // Check if at least one runsheet row exists
                if ($(".table-container tbody tr[id^='runsheet-']").length === 0) {
                    alert("Please add at least one Runsheet before adding rows.");
                    return;
                }

                let count = prompt("How many rows do you want to add? (1-25)", "1");
                count = parseInt(count, 10);
                if (!isNaN(count) && count > 0 && count <= 25) {
                    addRows(count);
                } else {
                    alert("Please enter a valid number between 1 and 25.");
                }
            });

            $(".remove-button").click(function() {
                removeRows(1);
            });

            $(".remove-bulk-button").click(function() {
                let count = prompt("How many rows do you want to remove?", "1");
                count = parseInt(count, 10);
                if (!isNaN(count) && count > 0) {
                    removeRows(count);
                } else {
                    alert("Please enter a valid number.");
                }
            });

            attachRowListeners($(".table-container #tbody tr#tabletr"));
            attachRowListeners($(".table-container #tbody tr#table_exitisng"));
            $("#tax_rate, #other_cost").on("input", calculateSubTotal);
            calculateSubTotal();
        });




        $("#invoiceForm").on("submit", function(e) {
            e.preventDefault();
            let isValid = true;

            const requiredFields = [{
                    id: "#invoice_date",
                    message: "Invoice date is required."
                },
                {
                    id: "#company_name",
                    message: "Company name is required."
                },
                {
                    id: "#company_address",
                    message: "Address is required."
                },
                {
                    id: "#company_abn",
                    message: "ABN is required."
                },
                {
                    id: "#phone",
                    message: "Phone is required."
                }
            ];

            // Validate each required field
            requiredFields.forEach(field => {
                const input = $(field.id);
                const value = input.val().trim();

                if (!value) {
                    input.addClass("is-invalid");
                    isValid = false;
                } else {
                    input.removeClass("is-invalid");
                }
            });

            // Check if runsheet exists
            if ($(".table-container tbody tr[id^='runsheet-']").length === 0) {
                alert("Please add at least one Runsheet before submitting.");
                isValid = false;
            }


            // Check if at least one valid item row is added
            let hasValidItem = false;

            $(".table-container tbody tr#tabletr, .table-container tbody tr#table_exitisng").each(function() {
                const row = $(this);
                const invNo = row.find(".customer-inv-no").val().trim();
                const invName = row.find(".customer-inv-name").val().trim();
                let itemIsValid = false;

                // Check if at least one checkbox is checked AND has a value > 0
                row.find(".form-check").each(function() {
                    const checkbox = $(this).find("input[type='checkbox']");
                    const valueInput = $(this).find("input[type='text']");
                    const value = parseFloat(valueInput.val()) || 0;

                    if (checkbox.prop("checked") && value > 0) {
                        itemIsValid = true;
                    }
                });

                // Also check P/UP (select + value)
                const pupSelect = row.find(".form-contro");
                const pupValue = parseFloat(pupSelect.siblings("input[type='text']").val()) || 0;
                if (pupSelect.val() && pupValue > 0) {
                    itemIsValid = true;
                }

                if (invNo !== "" && invName !== "" && itemIsValid) {
                    hasValidItem = true;
                    return false; // break .each()
                }
            });

            if (!hasValidItem) {
                alert("Please add at least one valid item row with Invoice No, Name, and at least one selected item with value.");
                isValid = false;
            }

            // Check for duplicate customer invoice numbers
            let invoiceNumbers = new Set();
            let duplicateInvoiceNoFound = false;

            $(".table-container tbody tr#tabletr, .table-container tbody tr#table_exitisng").each(function() {
                const invNo = $(this).find(".customer-inv-no").val().trim();

                if (invNo !== "") {
                    if (invoiceNumbers.has(invNo)) {
                        duplicateInvoiceNoFound = true;
                        return false; // break loop
                    }
                    invoiceNumbers.add(invNo);
                }
            });

            if (duplicateInvoiceNoFound) {
                alert("Duplicate Customer Invoice Numbers found. Each row must have a unique Invoice No.");
                isValid = false;
            }

            if (!isValid) return;
            // $('select[name="invoice_type"]').val("<?= $invoiceData['invoice_type'] ?? '' ?>");
            const formData = {
                invoice_type: $('select[name="invoice_type"]').val(),
                invoice_id: <?php echo $invoiceId ?? ''; ?>,
                date: $("input[name='date']").val().trim(),
                invoice: $("input[name='invoice']").val().trim(),
                company: $("input[name='company']").val().trim(),
                address: $("input[name='address']").val().trim(),
                phone: $("input[name='phone']").val().trim(),
                abn: $("input[name='abn']").val().trim(),
                sub_total: $("#sub_total").val().trim(),
                tax_rate: $("#tax_rate").val().trim(),
                total_cost: $("#total_cost").val().trim(),
                existing_items: [],
                new_items: [],
                unchecked_items: [] // New array for unchecked items
            };

            let currentRunsheetNumber = $("#runsheet_no").text().trim();
            let currentRunsheetDate = $("#runsheet_date").text().trim();
            let maxItemRowId = getMaxItemRowId();
            /** --------------------
             * ✅ Collect Existing Items
             * -------------------- **/
            $(".table-container #tbody tr#table_exitisng").each(function() {
                const row = $(this);
                const itemRowId = row.attr("data-item-row-id");
                if (itemRowId > maxItemRowId) {
                    maxItemRowId = itemRowId;
                }
                const customerInvoiceNo = row.find(".customer-inv-no").val().trim() || "";
                const customerInvoiceName = row.find(".customer-inv-name").val().trim() || "";

                const amount = row.find(".amount-field").val().trim() || "0";
                let hasCheckedItem = false;
                let updatedItems = [];


                row.find(".form-check").each(function() {
                    const checkbox = $(this).find("input[type='checkbox']");
                    const inputField = $(this).find("input[type='text']");
                    const itemName = $(this).find("label").text().trim();
                    const itemId = $(this).find("input[type='hidden']").val();

                    if (checkbox.prop("checked")) {
                        hasCheckedItem = true;
                        updatedItems.push({
                            item_name: itemName,
                            item_value: inputField.val().trim() || "0",
                            item_id: itemId
                        });
                    } else if (itemId) {
                        formData.unchecked_items.push(itemId);
                    }
                });

                row.find("select").each(function() {
                    const select = $(this);
                    const selectedValue = select.val();
                    const itemId = select.siblings("input[type='hidden']").val();

                    if (selectedValue && selectedValue !== "0") {
                        hasCheckedItem = true;
                        updatedItems.push({
                            item_name: select.find("option:selected").text().trim(),
                            item_value: select.siblings("input[type='text']").val().trim() || "0",
                            item_id: itemId
                        });
                    } else if (itemId) {
                        formData.unchecked_items.push(itemId);
                    }
                });

                formData.existing_items.push({
                    item_row_id: itemRowId,
                    customer_inv_no: customerInvoiceNo,
                    customer_inv_name: customerInvoiceName,
                    items: updatedItems,
                    amount: amount,
                    runsheet_number: row.attr("data-runsheet-number") || currentRunsheetNumber,
                    runsheet_date: row.attr("data-runsheet-date") || currentRunsheetDate
                });
            });

            /** --------------------
             * ✅ Collect New Items
             * -------------------- **/
            $(".table-container #tbody tr#tabletr").each(function(index) {

                console.log(index);

                const row = $(this);
                const customerInvoiceNo = row.find(".customer-inv-no").val().trim() || "";
                const customerInvoiceName = row.find(".customer-inv-name").val().trim() || "";
                const amount = row.find(".amount-field").val().trim() || "0";
                let hasCheckedItem = false;
                let newItem = [];

                row.find(".form-check").each(function() {
                    const checkbox = $(this).find("input[type='checkbox']");
                    const inputField = $(this).find("input[type='text']");
                    const itemName = $(this).find("label").text().trim();

                    if (checkbox.prop("checked")) {
                        hasCheckedItem = true;
                        newItem.push({
                            item_name: itemName,
                            item_value: inputField.val().trim() || "0"
                        });
                    }
                });

                row.find("select").each(function() {
                    const select = $(this);
                    const selectedValue = select.val();

                    if (selectedValue && selectedValue !== "0") {
                        hasCheckedItem = true;
                        newItem.push({
                            item_name: select.find("option:selected").text().trim(),
                            item_value: select.siblings("input[type='text']").val().trim() || "0"
                        });
                    }
                });

                maxItemRowId++;

                // console.log('maxItemRowId', maxItemRowId);
                if (hasCheckedItem && newItem.length > 0) {
                    formData.new_items.push({
                        item_row_id: `${maxItemRowId}`,
                        customer_inv_no: customerInvoiceNo,
                        customer_inv_name: customerInvoiceName,
                        items: newItem,
                        amount: amount,
                        runsheet_number: row.attr("data-runsheet-number") || 0,
                        runsheet_date: row.attr("data-runsheet-date") || 0
                    });
                }
            });

            /** --------------------
             * ✅ Validation: Ensure Required Fields Are Filled
             * -------------------- **/
            // if (!formData.invoice_id || !formData.date || !formData.invoice || !formData.company) {
            //     alert("Please fill in all required invoice details.");
            //     return;
            // }

            // if (
            //     formData.existing_items.some(item => !item.runsheet_number || !item.runsheet_date) ||
            //     formData.new_items.some(item => !item.runsheet_number || !item.runsheet_date)
            // ) {
            //     alert("Each item must have a Runsheet Number and Runsheet Date.");
            //     return;
            // }

            /** --------------------
             * ✅ Debugging: Log the Data Before Sending
             * -------------------- **/
            // console.log("Final Form Data:", formData);

            /** --------------------
             * ✅ Submit Data to API
             * -------------------- **/
            fetch("process_update_invoice.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(formData),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert("Invoice successfully updated!");
                        window.location.href = "index.php"; // Redirect after success
                    } else {
                        alert("Error: " + (data.message || "Unknown error"));
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    alert("An error occurred while submitting. Please try again.");
                });
        });



        $(document).ready(function() {

            // Edit Runsheet Modal
            let currentRunsheetNumber;
            let currentRunsheetDate;

            // Show Modal with existing runsheet data
            $(document).on("click", ".edit-runsheet-button", function() {
                currentRunsheetNumber = $(this).data("run-number");
                currentRunsheetDate = $(this).data("run-date");

                $("#runsheetNumber").val(currentRunsheetNumber);
                $("#runsheetDate").val(currentRunsheetDate);

                $("#runsheetModal").modal("show");
            });


            $(document).on("click", ".edit-onpage-runsheet-button", function() {

                let runsheetId = $(this).attr("data-id"); // Get the ID of the runsheet row
                currentRunsheetNumber = $(this).data("run-number");
                currentRunsheetDate = $(this).data("run-date");

                $("#runsheetNumber").val(currentRunsheetNumber);
                $("#runsheetDate").val(currentRunsheetDate);

                $("#updateOnpageRunsheet").modal("show");
                // updateRunsheetForm
            });
            // Show Modal with existing runsheet data



            $(document).on("click", ".delete-runsheet-items", function() {
                const button = $(this);
                const runsheetNumber = button.data("run-number");
                const runsheetDate = button.data("run-date");

                if (confirm('Are you sure you want to delete this runsheet and all linked items?')) {
                    $.ajax({
                        url: "delete_runsheet.php",
                        type: "POST",
                        contentType: "application/json",
                        data: JSON.stringify({
                            runsheet_number: runsheetNumber,
                            runsheet_date: runsheetDate
                        }),
                        success: function(response) {
                            if (response.success) {
                                alert("Runsheet and linked items deleted successfully!");

                                // Remove the runsheet and linked items from the DOM
                                $(`tr[data-runsheet-number='${runsheetNumber}'][data-runsheet-date='${runsheetDate}']`).remove();
                                $(`#runsheet-data`).remove();
                            } else {
                                alert("Error: " + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error:", error);
                            alert("An error occurred while deleting the runsheet. Please try again.");
                        }
                    });
                }
            });

            // Save Runsheet Changes
            $("#saveRunsheet").click(function() {
                const runsheetNumber = $("#runsheetNumber").val();
                const runsheetDate = $("#runsheetDate").val();

                const runsheetData = {
                    oldNumber: currentRunsheetNumber,
                    oldDate: currentRunsheetDate,
                    newNumber: runsheetNumber,
                    newDate: runsheetDate
                };

                $.ajax({
                    url: "edit_runsheet_api.php",
                    type: "POST",
                    contentType: "application/json",
                    data: JSON.stringify(runsheetData),
                    success: function(response) {
                        if (response.success) {
                            alert("Runsheet updated successfully!");
                            location.reload(); // Refresh page after successful update
                        } else {
                            alert("Error: " + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", error);
                        alert("An error occurred while updating the runsheet. Please try again.");
                    }
                });

                $("#runsheetModal").modal("hide");
            });

            // // Add Runsheet Button Click Handler
            // $(".add-runsheet-button").click(function() {
            //     $("#runsheetNumber").val("");
            //     $("#runsheetDate").val("");
            //     $("#runsheetModal").modal("show");
            // });

        });
    </script>


    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>