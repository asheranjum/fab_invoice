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

    <div class="container invoice-container mb-5">

        <div class="row">
            <div class="col-md-12">
                <div class="header-img">
                    <img src="assets/images/head.png" alt="invoice-header" style="width: 100%; ">
                </div>
            </div>
        </div>

        <div class="row mt-3 ">
            <div class="col-md-7">

                <form id="invoiceForm" class="form-group p-1">
                    <div class="top-nav">

                        <div class="topbtngr btn-group" role="group">
                            <button type="button" class="btn mergebtn add-runsheet-button">Add Runsheet</button>
                            <button type="button" class="btn mergebtn  add-bulk-button">Add Row</button>
                            <button type="button" class="btn mergebtn remove-bulk-button">Remove Row</button>
                        </div>

                        <div class=" btn-group">
                            <button type="submit" class="btn mergebtn export-button">Save Invoice</button>
                        </div>

                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="invoice" class="form-label mb-0 me-3 w-25">INVOICE NO</label>
                        <input type="text" id="invoice" name="invoice" style="border: none; font-size: 18px;" value="<?php echo htmlspecialchars($newInvoice); ?>">

                    </div>

                    
                    <div class="mb-2 d-flex align-items-center">
                        <label for="date" class="form-label mb-0 me-3 w-25">DATE:</label>
                        <input type="date" name="date" id="invoice_date" class="form-control form-control-sm custom-width me-3">
                        <div class="invalid-feedback">Invoice date is required.</div>
                    </div>

                    <div class="mb-2 d-flex align-items-center">
                         <label class="form-label mb-0 me-3 w-25">BILL TO:</label>
                    <select id="invoice_type" disabled name="invoice_type" class="form-control w-50 me-3" >
                        <option value="Electric">Electric</option>
                    </select>

                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="Company" class="form-label mb-0 me-3 w-25">COMPANY NAME:</label>
                        <input type="text" name="company" id="company_name" class="form-control w-50" placeholder="Type Company Name" value="">
                        <div class="invalid-feedback">Company name is required.</div>

                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="address" class="form-label mb-0 me-3 w-25">ADDREESS:</label>
                        <input type="text" name="address" id="company_address" class="form-control w-50" placeholder="Enter Address Here" value="">
                        <div class="invalid-feedback">Address is required.</div>

                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="abn" class="form-label mb-0 me-3 w-25">ABN:</label>
                        <input type="text" name="abn" id="company_abn" class="form-control w-50" placeholder="Insert ABN Number" value="">
                        <div class="invalid-feedback">ABN is required.</div>
                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="phone" class="form-label mb-0 me-3 w-25">PHONE:</label>
                        <input type="text" name="phone" id="phone" class="form-control w-50 me-3" placeholder="Insert Phone Number" value="">
                        <div class="invalid-feedback">Phone is required.</div>
                        <!-- <label for="postal-code" class="form-label mb-0 me-2">Postal Code:</label>
                        <input type="text" name="postal_code" class="form-control custom-width-3" placeholder="Postal Code" value=""> -->
                    </div>
                    

                </form>
            </div>


            <div class="col-md-5 position-relative" >
                    <div class="">
                        <div class="mb-2 d-flex align-items-center">
                            <label for="employer_company" class="form-label mb-0 me-3 w-50">EMPLOYER COMPANY</label>
                            <input type="text" name="employer_company" id="employer_company" class="form-control w-50" placeholder="Employer Company Name" value="">
                            <!-- <div class="invalid-feedback">Employer company name is required.</div> -->
                        </div>
                        <div class="mb-2 d-flex align-items-center">
                            <label for="employer_phone" class="form-label mb-0 me-3 w-50">EMPLOYER PHONE</label>
                            <input type="text" name="employer_phone" id="employer_phone" class="form-control w-50" placeholder="Employer Phone" value="">
                            <!-- <div class="invalid-feedback">Employer company phone is required.</div> -->
                        </div>
                        <div class="mb-2 d-flex align-items-center">
                            <label for="employer_abn" class="form-label mb-0 me-3 w-50">EMPLOYER ABN</label>
                            <input type="text" name="employer_abn" id="employer_abn" class="form-control w-50" placeholder="Employer ABN" value="">
                            <!-- <div class="invalid-feedback">Employer company abn is required.</div> -->
                        </div>
                        <div class="mb-2 d-flex align-items-center">
                            <label for="employer_address" class="form-label mb-0 me-3 w-50">EMPLOYER ADDRESS</label>
                            <input type="text" name="employer_address" id="employer_address" class="form-control w-50" placeholder="Employer Address" value="">
                            <!-- <div class="invalid-feedback">Employer company address is required.</div> -->
                        </div>
                    </div>

                <!-- <div class="info">
                    <h6>FAB TRANSPORT SERVICES PTY LTD</h6>
                    <h6>PHONE: 0403729966</h6>
                    <h6>ABN: 123 121 211 222 222</h6>
                    <h6>ADDRESS: 5 LOUIS STREET DOVETON VIC 3177</h6>
                </div> -->

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
                            <th>NOTE</th>
                            <th>AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr style="display: none;">
                            <th colspan="4">
                                <div style=" gap: 50px; display: flex;">
                                    <strong>Runsheet No: <span id="runsheet_no"></span> </strong>
                                    <strong>Runsheet Date: <span id="runsheet_date"></span> </strong>
                                </div>
                            </th>
                        </tr>

                        <tr id="tabletr"  style="display: none;">

                            <td style="width: 180px;">
                                <input type="text" name="customer_inv_name[]" id="customer-inv-name" class="form-control customer-inv-name mt-2" placeholder="Enter Inv Name">
                                <input type="text" name="customer_inv_no[]" class="form-control customer-inv-no" placeholder="Enter Inv No">
                            </td>

                            <td>
                               
<div class="d-flex item_names_check">
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
                            <td>
                            <div class="note-text">        
                                <label for="note-text" class="form-check-label">Add Note</label>
                                <input type="text" id="note-text"  name="note-text-value[]" class="form-control note-text-value mt-1"  placeholder="">
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
        <div class="topbtngr btn-group" role="group">
                            <button type="button" class="btn mergebtn add-runsheet-button">Add Runsheet</button>
                            <button type="button" class="btn mergebtn  add-bulk-button">Add Row</button>
                            <button type="button" class="btn mergebtn remove-bulk-button">Remove Row</button>
                        </div>




        <div class="row mt-1 footer-bg">
            <div class="col-md-8">
                <div class="footer mt-3">
                    <p>MAKE ALL CHECKS PAYABLE TO "FAB TRANSPORT SERVICES PTY LTD"
                        IF YOU HAVE ANY QUESTIONS CONCERNING THIS INVOICE,
                        USE THE FOLLOWING CONTACT INFORMATION</p>
                    <ul>
                        <li>CONTACT NAME: SAM</li>
                        <li>PHONE: 0403 729 966</li>
                        <li>EMAIL: info@fabtransport.com.au</li>
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
            let isDataEntered = false;
            initializePage();

            // $(".add-runsheet-button").click(addRunSheetValues);
            // $(".add-button").click(() => addRows(1));
            // $(".add-bulk-button").click(() => addRows(promptForRowCount("add")));
            // $(".remove-button").click(() => removeRows(1));
            // $(".remove-bulk-button").click(() => removeRows(promptForRowCount("remove")));

            // $("#invoiceForm").on("submit", handleFormSubmit);
            // $("#tax_rate, #other_cost").on("input", calculateSubTotal);


            $(".add-runsheet-button").click(showAddRunsheetModal);
            $("#addRunsheet").click(addRunsheet);
            $(".add-bulk-button").click(() => {

                // Check if at least one runsheet row exists
                if ($(".table-container tbody tr[id^='runsheet-']").length === 0) {
                    alert("Please add at least one Runsheet before adding rows.");
                    return;
                }

                addRows(promptForRowCount("add"))
            });
            $(".remove-bulk-button").click(() => removeRows(promptForRowCount("remove")));
            $("#invoiceForm").on("submit", handleFormSubmit);
            $("#tax_rate, #other_cost").on("input", calculateSubTotal);



            function initializePage() {
                // Initialization logic
                attachRowListeners($(".table-container tbody tr"));
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

            function formatDateToDDMMYYYY(dateStr) {
                if (!dateStr) return "";
                const parts = dateStr.split("-");
                if (parts.length === 3) {
                    return `${parts[2]}-${parts[1]}-${parts[0]}`;
                }
                return dateStr; // return as-is if format is unexpected
            }

            function addRunsheet() {

                // Check for duplicate runsheet number
                let duplicateRunsheet = false;



                const runsheetNumber = $("#addRunsheetNumber").val();
                const runsheetDate = $("#addRunsheetDate").val();

                if (!runsheetNumber || !runsheetDate) {
                    alert("Please fill in both Runsheet Number and Runsheet Date.");
                    return;
                }

                runsheetIndex++;
                currentRunsheet = {
                    number: runsheetNumber,
                    date: formatDateToDDMMYYYY(runsheetDate)
                };

                const runsheetRow = `
            <tr id="runsheet-${runsheetIndex}">
                <th colspan="4" id="runsheet-${runsheetIndex}">
                    <div style="gap: 50px; display: flex;">
                      <h5><strong>Runsheet No: <span class="runsheet-no">${runsheetNumber}</span></strong></h5>
                      <h5><strong>Runsheet Date: <span class="runsheet-date">${formatDateToDDMMYYYY(runsheetDate)}</span></strong></h5>
                        <strong><button class="btn btn-danger btn-sm edit-onpage-runsheet-button" data-id="runsheet-${runsheetIndex}" data-run-number="${runsheetNumber}" data-run-date="${runsheetDate}">Edit</button></strong>
                        <strong><button class="btn btn-danger btn-sm remove-runsheet" data-id="runsheet-${runsheetIndex}">Remove</button></strong>
                    </div>
                </th>
            </tr>`;

                $(".table-container tbody tr[id^='runsheet-']").each(function() {
                    const text = $(this).text();
                    if (text.includes(runsheetNumber)) {
                        duplicateRunsheet = true;
                        return false; // break
                    }
                });

                if (duplicateRunsheet) {
                    alert("This Runsheet Number already exists. Please enter a unique Runsheet Number.");
                    return;
                }
                $(".table-container tbody").append(runsheetRow);

                attachRowListeners($(`#tabletr-${runsheetIndex}`));

                $("#addRunsheetModal").modal("hide");
            }


            $(document).on("click", ".remove-runsheet", function() {
                  
                const runsheetId = $(this).data("id");

                if (confirm(`Would you like to remove runsheet?`)) {
                      
                    $(`#${runsheetId}`).remove();
                    $(`#tabletr-${runsheetId.split('-')[1]}`).remove();
                    }
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
                $(row).find(".form-checkboxes:checked").each(function() {
                    const $formCheck = $(this).closest(".form-check");

                    // Skip if the parent has class "note-text-value"
                    if ($formCheck.hasClass("note-text-value")) return;

                    const value = parseFloat($formCheck.find("input[type='text']").val()) || 0;
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

                // ✅ Numeric-only fields
                $(row).find(".numeric-only").on("keypress", function(e) {
                    const key = e.key;
                    const allowedPattern = /^[0-9.]$/;
                    if (!allowedPattern.test(key)) {
                        e.preventDefault();
                    }
                });

                $(row).find(".numeric-only").on("paste", function(e) {
                    const pasted = (e.originalEvent || e).clipboardData.getData('text');
                    if (!/^[0-9.]+$/.test(pasted)) {
                        e.preventDefault();
                    }
                });



                $(row).find(".customer-inv-name").on("keypress", function(e) {
                    const key = e.key;
                    const allowedPattern = /^[a-zA-Z0-9\s.,()\-'"&]$/;
                    if (!allowedPattern.test(key)) {
                        e.preventDefault();
                    }
                });



                // $(row).find(".form-check input[type='text']").prop("disabled", true);
                $(row).find(".form-contro").siblings("input[type='text']").prop("disabled", true);
            }

            function addRows(count) {
                const rows = $(".table-container tbody tr#tabletr");
                let currentRows = rows.length;
                let newRows = Math.min(count, maxRows - currentRows);

                if (newRows <= 0) {
                    alert("You cannot add more than " + maxRows + " rows.");
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

                    newRow.removeAttr("style");

                    newRow.find(".form-contro").prop("disabled", false);

                    newRow.find("#customer-inv-name").val("");
                    newRow.find("#note-text").val("");

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
                        inputField.val("");
                        inputField.prop("disabled", true);;
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


            $('#invoice_date, #addRunsheetDate').on('input', function() {
                const inputDate = $(this).val(); // format is YYYY-MM-DD
                if (inputDate) {
                    const [year, month, day] = inputDate.split('-');
                    const formatted = `${day}-${month}-${year}`;
                    $('#formatted_date_display').val(formatted);
                } else {
                    $('#formatted_date_display').val('');
                }
            });
            // Monitor changes in input fields
            $("input, select, textarea").on("change keyup", function() {
                isDataEntered = true;
            });



            // Monitor changes in runsheets
            $(document).on("click", ".add-runsheet-button, .remove-runsheet", function() {
                isDataEntered = true;
            });
            // Add beforeunload event listener
            window.addEventListener("beforeunload", function(e) {
                if (isDataEntered) {
                    // Display a confirmation dialog
                    const confirmationMessage = "You have unsaved changes. If you reload this page, your data will be lost.";
                    e.preventDefault(); // Prevent default behavior
                    e.returnValue = confirmationMessage; // Standard for modern browsers
                    return confirmationMessage; // Legacy browsers
                }
            });


            function handleFormSubmit(e) {
                e.preventDefault();
                isDataEntered = false; // Data is saved, so no need for aler

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

                $(".table-container tbody tr#tabletr").each(function() {
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

                $(".table-container tbody tr#tabletr").each(function() {
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

                const formData = {
                    invoice_type: $("select[name='invoice_type']").val(),
                    date: $("input[name='date']").val(),
                    invoice: $("input[name='invoice']").val(),
                    employer_company: $("input[name='employer_company']").val(),
                    employer_phone: $("input[name='employer_phone']").val(),
                    employer_abn: $("input[name='employer_abn']").val(),
                    employer_address: $("input[name='employer_address']").val(),
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
                    console.log(index);
                    const customerInvoiceNo = $(this).find(".customer-inv-no").val() || '';
                    const customer_inv_name = $(this).find(".customer-inv-name").val() || '';
                    const note_text_value = $(this).find(".note-text-value").val() || '';
                    
                    const amount = $(this).find(".amount-field").val() || 0;
                    let row = $(this);
                    $(this).find(".item_names_check > .form-check").each(function() {
                        const checkbox = $(this).find("input[type='checkbox']");
                        const inputField = $(this).find("input[type='text']");
                        // Get the corresponding note field for the current item
                        if (checkbox.prop("checked")) {
                            formData.items.push({
                                item_row_id: `${index + 1}`,
                                customer_inv_no: customerInvoiceNo,
                                customer_inv_name: customer_inv_name,
                                note_text_value: note_text_value,
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
                            note_text_value: note_text_value,
                            item_name: selectField.find("option:selected").text().trim(),
                            item_value: selectField.siblings("input[type='text']").val() || 0,
                            amount: amount,
                            runsheet_number: row.attr("data-runsheet-number") || $("#runsheet_no").text(),
                            runsheet_date: row.attr("data-runsheet-date") || $("#runsheet_date").text()
                        });
                    }
                });

                // console.log(formData);
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
                            window.location.href = "index.php"; // Redirect after success
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