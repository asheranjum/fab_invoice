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

$newInvoice = $invoiceData['invoice'];

// var_dump($invoiceData['items']);

$groupedItems = [];
foreach ($invoiceData['items'] as $item) {
    $runsheetNumber = $item['runsheet_number'];
    $runsheetDate = $item['runsheet_date'];
    $itemRowId = $item['item_row_id'];
    $itemName = $item['item_name'];
    $itemValue = $item['item_value'];
    $customInvoiceNo = $item['customer_invoice_no'];
    $customInvoiceName = $item['customer_invoice_name'];

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


// var_export($groupedItems);
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
                    <div class="topbtngrp">
                        <button type="submit" class="btn btn-success export-button">Update Invoice</button>
                        <button type="button" class="btn btn-secondary  add-bulk-button">Add Bulk Rows</button>
                        <button type="button" class="btn btn-warning remove-bulk-button">Remove Bulk Rows</button>
                        <button type="button" class="btn btn-info add-runsheet-button">Add Runsheet</button>

                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <input type="hidden" name="invoice_id" value="<?php echo $invoiceId ?? ''; ?>">
                        <label for="date" class="form-label mb-0 me-2">DATE:</label>
                        <input type="date" name="date" class="form-control form-control-sm custom-width me-3" style=" font-size: 18px;" value="<?php echo $invoiceData['date'] ?? ''; ?>">

                        <label for="invoice" class="form-label mb-0 me-2">INVOICE NO</label>

                        <input type="text" id="invoice" name="invoice" style="border: none; font-size: 18px;" value="<?php echo htmlspecialchars($newInvoice); ?>" readonly>

                    </div>

                    <h3 class="mt-1 mb-2 heading">Bill To:</h3>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="Company" class="form-label mb-0 me-3">COMPANY NAME:</label>
                        <input type="text" name="company" class="form-control w-50" placeholder="Type Company Name" value="<?php echo $invoiceData['company'] ?? ''; ?>">
                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="address" class="form-label mb-0 me-4">ADDREESS:</label>
                        <input type="text" name="address" class="form-control custom-width-2" placeholder="Enter Address Here" value="<?php echo $invoiceData['address'] ?? ''; ?>">
                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="abn" class="form-label mb-0 me-5">ABN:</label>
                        <input type="text" name="abn" class="form-control custom-width-1" placeholder="Insert ABN Number" value="<?php echo $invoiceData['abn'] ?? ''; ?>">
                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="phone" class="form-label mb-0 me-2">PHONE NO:</label>
                        <input type="text" name="phone" class="form-control custom-width-1 me-3" placeholder="Insert Phone Number" value="<?php echo $invoiceData['phone'] ?? ''; ?>">

                        <!-- <label for="postal-code" class="form-label mb-0 me-2">Postal Code:</label>
                        <input type="text" name="postal_code" class="form-control custom-width-3" placeholder="Postal Code" value=""> -->
                    </div>

                    <div class="mb-0 d-flex align-items-center">
                        <label for="runsheet" class="form-label mb-0 me-2">RUNSHEET NO:</label>
                        <input type="text" name="runsheet" class="form-control custom-width-1" placeholder="Enter RunSheet Number" value="<?php echo $invoiceData['runsheet'] ?? ''; ?>">
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
                            <th>ITEMS DESCRIPTION AND CHARGES</th>
                            <th>AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody id="tbody">

                        <tr>
                            <th colspan="3">
                                <div style=" gap: 50px; display: flex;">
                                    <strong>Runsheet No: <span id="runsheet_no"></span> </strong>
                                    <strong>Runsheet Date: <span id="runsheet_date"></span> </strong>
                                </div>
                            </th>
                        </tr>

                        <tr id="tabletr">

                            <td style="width: 180px;">
                                <input type="text" name="customer_inv_no[]" class="form-control customer-inv-no" placeholder="Enter Invoice No">
                                <input type="text" name="customer_inv_name[]" id="customer-inv-name" class="form-control customer-inv-name mt-2" placeholder="Enter Invoice Name">
                            </td>

                            <td>
                                <div class="d-flex">
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="deliv-0" name="item[0][deliv]">
                                        <label for="deliv-0" class="form-check-label">DELIV+</label>
                                        <input type="text" name="item[0][deliv_value]" class="form-control mt-1" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="disas-0" name="item[0][disas]">
                                        <label for="disas-0" class="form-check-label">DISAS+</label>
                                        <input type="text" name="item[0][disas_value]" class="form-control mt-1" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="assem-0" name="item[0][assem]">
                                        <label for="assem-0" class="form-check-label">ASSEM+</label>
                                        <input type="text" name="item[0][assem_value]" class="form-control mt-1" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="rub-0" name="item[0][rub]">
                                        <label for="rub-0" class="form-check-label">RUB+</label>
                                        <input type="text" name="item[0][rub_value]" class="form-control mt-1" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="upst-0" name="item[0][upst]">
                                        <label for="upst-0" class="form-check-label">UPST+</label>
                                        <input type="text" name="item[0][upst_value]" class="form-control mt-1" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="downst-0" name="item[0][downst]">
                                        <label for="downst-0" class="form-check-label">DOWNST+</label>
                                        <input type="text" name="item[0][downst_value]" class="form-control mt-1" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="prem-0" name="item[0][prem]">
                                        <label for="prem-0" class="form-check-label">PREM+</label>
                                        <input type="text" name="item[0][prem_value]" class="form-control mt-1" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="brtrans-0" name="item[0][brtrans]">
                                        <label for="brtrans-0" class="form-check-label">BRTRANS+</label>
                                        <input type="text" name="item[0][brtrans_value]" class="form-control mt-1" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="ins-0" name="item[0][ins]">
                                        <label for="ins-0" class="form-check-label">INST+</label>
                                        <input type="text" name="item[0][ins_value]" class="form-control mt-1" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="h_dliv-0" name="item[0][h_dliv]">
                                        <label for="h_dliv-0" class="form-check-label">H/DLIV+</label>
                                        <input type="text" name="item[0][h_dliv_value]" class="form-control mt-1" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="vol-0" name="item[0][vol]">
                                        <label for="vol-0" class="form-check-label">VOL+</label>
                                        <input type="text" name="item[0][vol_value]" class="form-control mt-1" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="water_con-0" name="item[0][water_con]">
                                        <label for="water_con-0" class="form-check-label">WATERCON+</label>
                                        <input type="text" name="item[0][water_con_value]" class="form-control mt-1" disabled placeholder="">
                                    </div>
                                    <div class="form-check ">
                                        <input type="checkbox" class="form-check-input form-checkboxes" id="door_remove-0" name="item[0][door_remove]">
                                        <label for="door_remove-0" class="form-check-label">DOOR/R+</label>
                                        <input type="text" name="item[0][door_remove_value]" class="form-control mt-1" disabled placeholder="">
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


                            <tr>
                                <th colspan="3">
                                    <div style=" gap: 50px; display: flex;">
                                        <strong>Runsheet No: <?= htmlspecialchars($runsheetNumber) ?> </strong>
                                        <strong>Runsheet Date: <?= htmlspecialchars($runsheetData['runsheet_date']) ?> </strong>
                                    </div>
                                </th>
                            </tr>

                            <?php foreach ($runsheetData['items'] as $itemRowId => $data): ?>
                                <tr>
                                    <td style="text-align: left;">
                                        Name: <?= isset($data['customer_invoice_name']) ? htmlspecialchars($data['customer_invoice_name']) : 'N/A' ?><br>
                                        No: <?= isset($data['custom_invoice_no']) ? htmlspecialchars($data['custom_invoice_no']) : 'N/A' ?>
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
                                                    'INST+' => 'INST+',
                                                    'H/DLIV+' => 'H/DLIV+',
                                                    'VOL+' => 'VOL+',
                                                    'WATERCON+' => 'WATERCON+',
                                                    'DOOR/R+' => 'DOOR/R+',
                                                ];

                                                // Check for any key matching P/UP(x)
                                                $selectedPUP = null;
                                                foreach ($data['items'] as $key => $value) {
                                                    if (preg_match('/^P\/UP\(\d+\)$/', $key)) {
                                                        $selectedPUP = $key;
                                                        break;
                                                    }
                                                }

                                                // If a P/UP key is found, add it dynamically
                                                $pupKey = $selectedPUP ?: 'P/UP';
                                                $allOptions[$pupKey] = $pupKey;
                                                $allOptionsIndex = 0;
                                                ?>

                                                <?php foreach ($allOptions as $key => $label): ?>
                                                    <td>
                                                        <div class="d-flex">
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input form-checkboxes" id="<?= htmlspecialchars($label) ?>-<?php echo $allOptionsIndex; ?>" name="item[<?php echo $allOptionsIndex; ?>][<?= htmlspecialchars($label) ?>]" <?= isset($data['items'][$key]) ? 'checked' : '' ?>>
                                                                <label for="<?= htmlspecialchars($label) ?>-<?php echo $allOptionsIndex; ?>" class="form-check-label"><?= htmlspecialchars($label) ?></label>
                                                                <input type="text" name="item[<?php echo $allOptionsIndex; ?>][<?= htmlspecialchars($label) ?>_value]" class="form-control mt-1" disable placeholder="" value="<?= isset($data['items'][$key]) ? number_format((float)$data['items'][$key], 2) : '0.00' ?>">
                                                            </div>
                                                        </div>



                                                    </td>
                                                <?php $allOptionsIndex++;
                                                endforeach; ?>
                                            </tr>

                                        </table>
                                    </td>
                                    <td>$<?= number_format(array_sum($data['items']), 2) ?></td>
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
                            <td><input type="text" id="tax_rate" name="tax_rate" class="form-control" placeholder="$0.00"></td>
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
        $(document).ready(function() {

            $('#runsheet_no').append(<?php echo $invoiceId;?>+'1001')

            var today = new Date();
            var formattedDate = today.toLocaleDateString(); // Format the date (e.g., "3/7/2025" in US format)

            // Append the date to the element
            $('#runsheet_date').append(formattedDate);

            const maxRows = 25;
            let currentRunsheet = null; // Store current runsheet data
            let runsheetIndex = 0; // Unique identifier for runsheets
            $(".add-runsheet-button").click(function() {
                // Get Runsheet details using prompt()
                let runsheetNumber = prompt("Enter Runsheet Number:");
                if (runsheetNumber === null || runsheetNumber.trim() === "") return; // Exit if empty or canceled

                let runsheetDate = prompt("Enter Runsheet Date (YYYY-MM-DD):");
                if (runsheetDate === null || runsheetDate.trim() === "") return; // Exit if empty or canceled
                runsheetIndex++; // Increment unique runsheet index
                // Validate date format (basic check)
                // if (!/^\d{4}-\d{2}-\d{2}$/.test(runsheetDate)) {
                //     alert("Invalid date format. Please use YYYY-MM-DD.");
                //     return;
                // }

                // Store current runsheet details for new rows
                currentRunsheet = {
                    number: runsheetNumber,
                    date: runsheetDate
                };

                // Runsheet HTML template
                let runsheetRow = `
            
                <tr>
                    <th colspan="3"  id="runsheet-${runsheetIndex}">
                        <div style=" gap: 50px; display: flex;">
                            <strong>Runsheet No: ${runsheetNumber}</strong>
                            <strong>Runsheet Date: ${runsheetDate}</strong>
                            <strong><button class="btn btn-danger btn-sm remove-runsheet" data-id="runsheet-${runsheetIndex}">Remove</button></strong>
                        </div>
                    </th>
                </tr>
           `;

                // Append runsheet below the last item row
                $(".table-container #tbody").append(runsheetRow);
            });

            $(document).on("click", ".remove-runsheet", function() {
                let runsheetId = $(this).attr("data-id"); // Get the ID of the runsheet row
                $("#" + runsheetId).remove(); // Remove the respective runsheet row
            });
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
                        inputField.prop("disabled", true).val("");
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


                $(row).find(".form-check input[type='text']").prop("disabled", true);
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

                for (let i = 0; i < newRows; i++) {
                    const lastRow = $(".table-container #tbody tr#tabletr").last();
                    const newRow = lastRow.clone();
                    const rowIndex = $(".table-container #tbody tr#tabletr").length;

                    newRow.find("input, select").each(function() {
                        if (this.type === "checkbox") {
                            this.checked = false;
                        } else {
                            // $(this).val("").prop("disabled", true);
                        }
                        if ($(this).hasClass("customer-inv-no")) {
                            $(this).prop("disabled", false).val("");
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


                    if (currentRunsheet) {
                        newRow.attr("data-runsheet-number", currentRunsheet.number);
                        newRow.attr("data-runsheet-date", currentRunsheet.date);
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
            $("#tax_rate, #other_cost").on("input", calculateSubTotal);
            calculateSubTotal();
        });






        $("#invoiceForm").on("submit", function(e) {
            e.preventDefault();
            const formData = {
                invoice_id: <?php echo $invoiceId ?? ''; ?>,
                date: $("input[name='date']").val(),
                invoice: $("input[name='invoice']").val(),
                company: $("input[name='company']").val(),
                address: $("input[name='address']").val(),
                phone: $("input[name='phone']").val(),
                postal_code: $("input[name='postal_code']").val(),
                abn: $("input[name='abn']").val(),
                runsheet: $("input[name='runsheet']").val(),
                sub_total: $("#sub_total").val(),
                tax_rate: $("#tax_rate").val(),
                other_cost: $("#other_cost").val(),
                total_cost: $("#total_cost").val(),
                items: []
            };

            $(".table-container #tbody tr#tabletr").each(function(index) {
                const customerInvoiceNo = $(this).find(".customer-inv-no").val() || '';
                const customer_inv_name = $(this).find(".customer-inv-name").val() || '';
                const amount = $(this).find(".amount-field").val() || 0;
                let row = $(this);
                $(this).find(".form-check").each(function() {
                    const checkbox = $(this).find("input[type='checkbox']");
                    const inputField = $(this).find("input[type='text']");

                    if (checkbox.prop("checked")) {
                        console.log(row.attr("data-runsheet-number"));
                        console.log(row.attr("data-runsheet-date"));
                        formData.items.push({
                            item_row_id: `${index + 1}`,
                            customer_inv_no: customerInvoiceNo,
                            customer_inv_name: customer_inv_name,
                            item_name: $(this).find("label").text().trim(),
                            item_value: inputField.val() || 0,
                            amount: amount,
                            runsheet_number: row.attr("data-runsheet-number") || "",
                            runsheet_date: row.attr("data-runsheet-date") || ""
                        });
                    }
                });

                const selectField = $(this).find(".form-contro");
                if (selectField.val() !== "") {
                    formData.items.push({
                        item_row_id: `${index + 1}`,
                        customer_inv_no: customerInvoiceNo,
                        customer_inv_name: customer_inv_name,
                        item_name: selectField.find("option:selected").text().trim(),
                        item_value: selectField.siblings("input[type='text']").val() || 0,
                        amount: amount,
                        runsheet_number: row.attr("data-runsheet-number") || "",
                        runsheet_date: row.attr("data-runsheet-date") || ""
                    });
                }
            });


            fetch("process_update_invoice.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(formData),
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        alert(data.message);
                        isFormDirty = false; // Reset dirty flag on successful submission
                        // window.location.href = "index.php";
                    } else {
                        alert("Error: " + data.message);
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("An error occurred. Please try again.");
                });
        });
    </script>


    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>