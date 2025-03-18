<?php

require 'session.php';
require 'config/database.php';

// Fetch the last invoice number
$sql = "SELECT MAX(CAST(invoice_number AS UNSIGNED)) AS last_invoice FROM invoices";
$result = mysqli_query($conn, $sql);

$lastInvoice = 0;
if ($result && $row = mysqli_fetch_assoc($result)) {
    $lastInvoice = (int)$row['last_invoice']; // Get the last invoice number
}


// Set starting invoice number if no records exist
$startingInvoice = 10001;
$newInvoice = ($lastInvoice > 0) ? $lastInvoice + 1 : $startingInvoice;

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
                        <!-- <button type="button" class="btn btn-primary add-button">Add Row</button> -->
                        <!-- <button type="button" class="btn btn-danger remove-button">Remove Row</button> -->
                        <button type="button" class="btn btn-dark  add-bulk-button">Add Row</button>
                        <button type="button" class="btn btn-dark remove-bulk-button">Remove Row</button>
                        <button type="button" class="btn btn-dark add-runsheet-button">Add Runsheet</button>
                        <button type="submit" class="btn btn-success export-button">Save Invoice</button>
                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="date" class="form-label mb-0 me-2">DATE:</label>
                        <input type="date" name="date" class="form-control form-control-sm custom-width me-3" style=" font-size: 18px;" value="">

                        <label for="invoice" class="form-label mb-0 me-2">INVOICE NO</label>

                        <input type="text" id="invoice" name="invoice" style="border: none; font-size: 18px;" value="<?php echo htmlspecialchars($newInvoice); ?>" readonly>

                    </div>

                    <h3 class="mt-1 mb-2 heading">Bill To:</h3>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="Company" class="form-label mb-0 me-3">COMPANY NAME:</label>
                        <input type="text" name="company" class="form-control w-50" placeholder="Type Company Name" value="">
                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="address" class="form-label mb-0 me-4">ADDREESS:</label>
                        <input type="text" name="address" class="form-control custom-width-2" placeholder="Enter Address Here" value="">
                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="abn" class="form-label mb-0 me-5">ABN:</label>
                        <input type="text" name="abn" class="form-control custom-width-1" placeholder="Insert ABN Number" value="">
                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="phone" class="form-label mb-0 me-2">PHONE:</label>
                        <input type="text" name="phone" class="form-control custom-width-1 me-3" placeholder="Insert Phone Number" value="">

                        <!-- <label for="postal-code" class="form-label mb-0 me-2">Postal Code:</label>
                        <input type="text" name="postal_code" class="form-control custom-width-3" placeholder="Postal Code" value=""> -->
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
                    <tbody>

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
            const maxRows = 25;
            let currentRunsheet = null;
            let runsheetIndex = 0;

            initializePage();

            $(".add-runsheet-button").click(addRunSheetValues);
            $(".add-button").click(() => addRows(1));
            $(".add-bulk-button").click(() => addRows(promptForRowCount("add")));
            $(".remove-button").click(() => removeRows(1));
            $(".remove-bulk-button").click(() => removeRows(promptForRowCount("remove")));

            $("#invoiceForm").on("submit", handleFormSubmit);
            $("#tax_rate, #other_cost").on("input", calculateSubTotal);

            function initializePage() {
                $('#runsheet_no').append(<?php echo $newInvoice; ?> + '1001');
                $('#runsheet_date').append(new Date().toLocaleDateString());
                attachRowListeners($(".table-container tbody tr#tabletr"));
                calculateSubTotal();
            }

            function promptForRowCount(action) {
                let count = prompt(`How many rows do you want to ${action}? (1-25)`, "1");
                count = parseInt(count, 10);
                if (isNaN(count) || count <= 0 || count > 25) {
                    alert("Please enter a valid number between 1 and 25.");
                    return 0;
                }
                return count;
            }

            function addRunSheetValues() {
                let runsheetNumber = prompt("Enter Runsheet Number:");
                if (!runsheetNumber || runsheetNumber.trim() === "") return;

                let runsheetDate = prompt("Enter Runsheet Date (YYYY-MM-DD):");
                if (!runsheetDate || runsheetDate.trim() === "") return;

                runsheetIndex++;
                currentRunsheet = {
                    number: runsheetNumber,
                    date: runsheetDate
                };

                let runsheetRow = `
            <tr>
                <th colspan="3" id="runsheet-${runsheetIndex}">
                    <div style="gap: 50px; display: flex;">
                        <strong>Runsheet No: ${runsheetNumber}</strong>
                        <strong>Runsheet Date: ${runsheetDate}</strong>
                        <strong><button class="btn btn-danger btn-sm remove-runsheet" data-id="runsheet-${runsheetIndex}">Remove</button></strong>
                    </div>
                </th>
            </tr>`;
                $(".table-container tbody").append(runsheetRow);
            }

            $(document).on("click", ".remove-runsheet", function() {
                $("#" + $(this).attr("data-id")).remove();
            });

            function calculateRowAmount(row) {
                let amount = 0;
                $(row).find(".form-checkboxes:checked").each(function() {
                    const value = parseFloat($(this).closest(".form-check").find("input[type='text']").val()) || 0;
                    amount += value;
                });

                const selectValue = parseFloat($(row).find(".form-contro").siblings("input[type='text']").val()) || 0;
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
                $("#sub_total").val(subTotal.toFixed(2));
                $("#total_cost").val((subTotal + taxRate + otherCost).toFixed(2));
            }

            function attachRowListeners(row) {
                $(row).find(".form-checkboxes").change(function() {
                    const inputField = $(this).closest(".form-check").find("input[type='text']");
                    inputField.prop("disabled", !this.checked).val("");
                    calculateRowAmount(row);
                });

                $(row).find(".form-contro").change(function() {
                    const inputField = $(this).siblings("input[type='text']");
                    inputField.prop("disabled", !$(this).val()).val("");
                    calculateRowAmount($(this).closest("tr"));
                });

                $(row).find(".form-check input[type='text']").on("input", function() {
                    calculateRowAmount(row);
                });

                $(row).find("input[type='text']").keypress(function(e) {
                    if (!/^[0-9.]+$/.test(e.key) && e.key !== "Backspace") {
                        e.preventDefault();
                    }
                });

                $(row).find("#customer-inv-name").keypress(function(e) {
                    if (!/^[\w\s.,;!?()\-"'&@#$%^*+=<>_/|\\`~]+$/.test(e.key) && e.key !== "Backspace") {
                        e.preventDefault();
                    }
                });

                $(row).find(".form-check input[type='text']").prop("disabled", true);
                $(row).find(".form-contro").siblings("input[type='text']").prop("disabled", true);
            }

            function addRows(count) {
                if (count <= 0) return;
                let currentRows = $(".table-container tbody tr#tabletr").length;
                let newRows = Math.min(count, maxRows - currentRows);

                if (newRows <= 0) {
                    alert(`You cannot add more than ${maxRows} rows.`);
                    return;
                }

                for (let i = 0; i < newRows; i++) {
                    const newRow = $(".table-container tbody tr#tabletr").last().clone();
                    const rowIndex = $(".table-container tbody tr#tabletr").length;

                    newRow.find("input, select").each(function() {
                        if (this.type === "checkbox") this.checked = false;
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

                    $(".table-container tbody").append(newRow);
                    attachRowListeners(newRow);
                }
            }

            function removeRows(count) {
                if (count <= 0) return;
                let currentRows = $(".table-container tbody tr#tabletr").length;

                if (currentRows <= 1) {
                    alert("You cannot remove the last row.");
                    return;
                }

                let removeCount = Math.min(count, currentRows - 1);
                if (removeCount > 0) {
                    if (confirm(`Are you sure you want to remove ${removeCount} row(s)?`)) {
                        for (let i = 0; i < removeCount; i++) {
                            $(".table-container tbody tr#tabletr").last().remove();
                        }
                        calculateSubTotal();
                    }
                }
            }

            function handleFormSubmit(e) {
                e.preventDefault();
                const formData = {
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

                $(".table-container tbody tr#tabletr").each(function(index) {
                    const customerInvoiceNo = $(this).find(".customer-inv-no").val() || '';
                    const customer_inv_name = $(this).find(".customer-inv-name").val() || '';
                    const amount = $(this).find(".amount-field").val() || 0;
                    let row = $(this);
                    $(this).find(".form-check").each(function() {
                        const checkbox = $(this).find("input[type='checkbox']");
                        const inputField = $(this).find("input[type='text']");

                        if (checkbox.prop("checked")) {
                            formData.items.push({
                                item_row_id: `${index + 1}`,
                                customer_inv_no: customerInvoiceNo,
                                customer_inv_name: customer_inv_name,
                                item_name: $(this).find("label").text().trim(),
                                item_value: inputField.val() || 0,
                                amount: amount,
                                runsheet_number: row.attr("data-runsheet-number") || $("#runsheet_no").text(),
                                runsheet_date: row.attr("data-runsheet-date") || $("#runsheet_date").text()
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
                            runsheet_number: row.attr("data-runsheet-number") || $("#runsheet_no").text(),
                            runsheet_date: row.attr("data-runsheet-date") || $("#runsheet_date").text()
                        });
                    }
                });

                console.log(formData);
                fetch("save_invoice.php", {
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
                        } else {
                            alert("Error: " + data.message);
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        alert("An error occurred. Please try again.");
                    });
            }
        });
    </script>


    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>