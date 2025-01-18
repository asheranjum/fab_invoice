<?php

require 'session.php';
require 'config/database.php';

// Fetch the last invoice number
$sql = "SELECT MAX(CAST(invoice AS UNSIGNED)) AS last_invoice FROM invoice";
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
                        <button type="button" class="btn btn-primary add-button">Add Row</button>
                        <button type="button" class="btn btn-danger remove-button">Remove Row</button>
                        <button type="submit" class="btn btn-success export-button">Save Invoice</button>
                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="date" class="form-label mb-0 me-2">Date:</label>
                        <input type="date" name="date" class="form-control form-control-sm custom-width me-3" style=" font-size: 18px;" value="">

                        <label for="invoice" class="form-label mb-0 me-2">Invoice No #</label>

                        <input type="text" id="invoice" name="invoice" style="border: none; font-size: 18px;" value="<?php echo htmlspecialchars($newInvoice); ?>" readonly>

                    </div>

                    <h3 class="mt-1 mb-2 heading">Bill To:</h3>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="Company" class="form-label mb-0 me-3">Company Name:</label>
                        <input type="text" name="company" class="form-control w-50" placeholder="Type Company Name" value="">
                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="address" class="form-label mb-0 me-4">Address:</label>
                        <input type="text" name="address" class="form-control custom-width-2" placeholder="Enter Address Here" value="">
                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="abn" class="form-label mb-0 me-5">ABN:</label>
                        <input type="text" name="abn" class="form-control custom-width-1" placeholder="Insert ABN Number" value="">
                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="phone" class="form-label mb-0 me-2">Phone No:</label>
                        <input type="text" name="phone" class="form-control custom-width-1 me-3" placeholder="Insert Phone Number" value="">

                        <!-- <label for="postal-code" class="form-label mb-0 me-2">Postal Code:</label>
                        <input type="text" name="postal_code" class="form-control custom-width-3" placeholder="Postal Code" value=""> -->
                    </div>

                    <div class="mb-0 d-flex align-items-center">


                        <label for="runsheet" class="form-label mb-0 me-2">RunSheet No:</label>
                        <input type="text" name="runsheet" class="form-control custom-width-1" placeholder="Enter RunSheet Number" value="">
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
                    <h6>1. Assembly</h6>
                    <h6>2. Delivery</h6>
                    <h6>3. Repairs</h6>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-12">
                <table class="table table-bordered table-container">
                    <thead>
                        <tr>
                            <th>Customer's Inv No</th>
                            <th>Items Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 180px;">
                                <input type="text" name="customer_inv_no[]" class="form-control customer-inv-no" placeholder="Enter Invoice No">
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
                    <p>Make all checks payable to Fab Transport services pty ltd
                        If you have any questions concerning this invoice,
                        use the following contact information</p>
                    <ul>
                        <li>Contact Name: John</li>
                        <li>Phone: 8888 999 000</li>
                        <li>Email: example@gmail.com</li>
                    </ul>
                    <h4>Thank You For Your Business!</h4>
                </div>
            </div>

            <div class="col-md-4 mt-3 ">
                <table class="table-container-2 mb-3 ml-5">

                    <div class="form-group table-2-width">
                        <tr>
                            <td><strong><label for="sub_total" class="bold-label">Total</label></strong></td>
                            <td><strong> <input type="text" id="sub_total" name="sub_total" class="form-control" readonly placeholder="$0.00"></strong></td>
                        </tr>
                    </div>

                    <div class="form-group table-2-width">
                        <tr>
                            <td><label for="tax_rate">Tax Rate</label></td>
                            <td><input type="text" id="tax_rate" name="tax_rate" class="form-control" placeholder="$0.00"></td>
                        </tr>
                    </div>

                    <div class="form-group table-2-width">
                        <tr>
                            <td><strong><label for="total_cost" class="bold-label">Total Cost</label></strong></td>
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

            function calculateRowAmount(row) {
                let amount = 0;

                $(row).find(".form-checkboxes").each(function() {
                    const inputField = $(this).closest(".form-check").find("input[type='text']");
                    const value = parseFloat(inputField.val()) || 0;

                    if (this.checked) amount += value;
                });

                // Include the select input value in the calculation
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

                    // Enable the input if the selected value is not empty, otherwise disable and clear it
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

                // Validation to restrict input to numbers only
                $(row).find("input[type='text']").off("keypress").on("keypress", function(e) {
                    if (!/^[0-9.]+$/.test(e.key) && e.key !== "Backspace") {
                        e.preventDefault();
                    }
                });

                // Disable all input fields initially
                $(row).find(".form-check input[type='text']").prop("disabled", true);
                $(row).find(".form-contro").siblings("input[type='text']").prop("disabled", true); // Ensure select inputs are disabled initially
            }

            $(".add-button").click(function() {
                const rows = $(".table-container tbody tr");
                if (rows.length >= maxRows) {
                    alert("You cannot add more than 25 rows.");
                    return;
                }

                const lastRow = rows.last();
                const newRow = lastRow.clone();
                const rowIndex = rows.length; // Calculate new row index for naming

                // Reset input values and update names for the cloned row
                newRow.find("input, select").each(function() {
                    if (this.type === "checkbox") {
                        this.checked = false; // Uncheck checkboxes
                    } else {
                        $(this).val("").prop("disabled", true); // Clear and disable inputs
                    }

                    if ($(this).hasClass("customer-inv-no")) {
                        $(this).prop("disabled", false).val("");
                    }
                });

                // Enable the select field in the duplicated row
                newRow.find(".form-contro").prop("disabled", false);

                // Update names dynamically
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

                $(".table-container tbody").append(newRow);
                attachRowListeners(newRow);
            });

            $(".remove-button").click(function() {
                const rows = $(".table-container tbody tr");
                if (rows.length <= 1) {
                    alert("You cannot remove the last row.");
                    return;
                }

                if (confirm("Are you sure you want to remove this row?")) {
                    rows.last().remove();
                    calculateSubTotal();
                }
            });

            attachRowListeners($(".table-container tbody tr"));
            $("#tax_rate, #other_cost").on("input", calculateSubTotal);
            calculateSubTotal();

            // Prevent page refresh and warn user
            let isFormDirty = false;
            $("#invoiceForm input, #invoiceForm select").on("input change", function() {
                isFormDirty = true;
            });

            $(window).on("beforeunload", function(e) {
                if (isFormDirty) {
                    return "Are you sure you want to leave? Your changes will be lost.";
                }
            });

            $("#invoiceForm").on("submit", function(e) {
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

                $(".table-container tbody tr").each(function(index) {
                    const customerInvoiceNo = $(this).find(".customer-inv-no").val() || '';
                    const amount = $(this).find(".amount-field").val() || 0;

                    $(this).find(".form-check").each(function() {
                        const checkbox = $(this).find("input[type='checkbox']");
                        const inputField = $(this).find("input[type='text']");

                        if (checkbox.prop("checked")) {
                            formData.items.push({
                                item_row_id: `${index + 1}`,
                                customer_inv_no: customerInvoiceNo,
                                item_name: $(this).find("label").text().trim(),
                                item_value: inputField.val() || 0,
                                amount: amount
                            });
                        }
                    });

                    const selectField = $(this).find(".form-contro");
                    if (selectField.val() !== "") {
                        formData.items.push({
                            item_row_id: `${index + 1}`,
                            customer_inv_no: customerInvoiceNo,
                            item_name: selectField.find("option:selected").text().trim(),
                            item_value: selectField.siblings("input[type='text']").val() || 0,
                            amount: amount
                        });
                    }
                });

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
                            isFormDirty = false; // Reset dirty flag on successful submission
                            window.location.href = "index.php";
                        } else {
                            alert("Error: " + data.message);
                        }
                    })
                    .catch((error) => {
                        console.error("Error:", error);
                        alert("An error occurred. Please try again.");
                    });
            });
        });
    </script>

    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>