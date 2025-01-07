<?php

require 'session.php';
require 'config/database.php';
require 'save_invoice.php';

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style-2.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>
  

    <div class="contanier">

        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="save-button">Save</button>
                <button type="button" class="export-button">Export PDF</button>
                <button type="button" class="add-button">Add Row</button>
                <button type="button" class="remove-button">Remove Row</button>
            </div>
        </div>
    </div>

    <div class="container invoice-container mb-5">

        <div class="row">
            <div class="col-md-12">
                <div class="header-img">
                    <img src="assets/images/head.png" alt="invoice-header" style="width: 100%; height: 140px;">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">

                <form action="save_invoice.php<?php echo isset($_GET['invoice_id']) ? '?id=' . $_GET['invoice_id'] : ''; ?>" method="POST" class="form-group p-1">

                    <div class="mb-2 d-flex align-items-center">
                        <label for="date" class="form-label mb-0 me-2">Date:</label>
                        <input type="date" name="date" class="form-control form-control-sm custom-width me-3" value="<?php echo htmlspecialchars($inv_date); ?>">

                        <label for="Invoice" class="form-label mb-0 me-2">Invoice No #</label>
                        <input type="text" name="invoice" class="form-control custom-width-1" placeholder="Enter Invoice Number" value="<?php echo htmlspecialchars($inv_invoice); ?>">
                    </div>

                    <h3 class="mt-1 mb-2 heading">Bill To:</h3>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="Company" class="form-label mb-0 me-3">Company:</label>
                        <input type="text" name="company" class="form-control w-50" placeholder="Type Company Name" value="<?php echo htmlspecialchars($inv_company); ?>">
                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="address" class="form-label mb-0 me-4">Address:</label>
                        <input type="text" name="address" class="form-control custom-width-2" placeholder="Enter Address Here" value="<?php echo htmlspecialchars($inv_address); ?>">
                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="phone" class="form-label mb-0 me-2">Phone No:</label>
                        <input type="text" name="phone" class="form-control custom-width-1 me-3" placeholder="Insert Phone Number" value="<?php echo htmlspecialchars($inv_phone); ?>">

                        <label for="postal-code" class="form-label mb-0 me-2">Postal Code:</label>
                        <input type="text" name="postal_code" class="form-control custom-width-3" placeholder="Postal Code" value="<?php echo htmlspecialchars($inv_postal_code); ?>">
                    </div>

                    <div class="mb-2 d-flex align-items-center">
                        <label for="abn" class="form-label mb-0 me-5">ABN:</label>
                        <input type="text" name="abn" class="form-control custom-width-1" placeholder="Insert ABN Number" value="<?php echo htmlspecialchars($inv_abn); ?>">
                    </div>

                    <div class="mb-0 d-flex align-items-center">
                        <!-- <label for="date" class="form-label mb-0 me-5">Date:</label>
                        <input type="date" class="form-control custom-width me-3"> -->

                        <label for="runsheet" class="form-label mb-0 me-2">RunSheet No:</label>
                        <input type="text" name="runsheet" class="form-control custom-width-1" placeholder="Enter RunSheet Number" value="<?php echo htmlspecialchars($inv_runsheet); ?>">
                    </div>
                </form>
            </div>

            <div class="col-md-4">
                <div class="service-items">
                    <h4>For:</h4>
                    <h6>1. Assembly</h6>
                    <h6>2. Delivery</h6>
                    <h6>3. Repairs</h6>
                </div>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col-md-12">
                <table class="table-container">
                    <tr>
                        <th>Customer's Inv No</th>
                        <th>Items Description</th>
                        <th>Amount</th>
                    </tr>

                    <tr>
                        <td>
                            <form action="save_invoice.php<?php echo isset($_GET['invoice_id']) ? '?id=' . $_GET['invoice_id'] : ''; ?>" method="POST">
                                <div class="mb-2 d-flex align-items-center">
                                    <label for="Invoice" class="form-label mb-0 me-1">Invoice#</label>
                                    <input type="text" name="customer_invoice_no" class="form-control custom-width-4" placeholder="Invoice No" value="<?php echo htmlspecialchars($inv_customer_invoice_no); ?>">
                                </div>
                        </td>

                        <td>
                            <div class="form-group">

                                <!-- items's checkboxes -->

                                <div class="d-flex flex-row justify-content-start align-items-center">

                                    <div class="form-check check custom-postion">
                                        <input type="checkbox" class="form-checkboxes" id="deliv">
                                    </div>
                                    <div class="form-check check me-3">
                                        <input type="checkbox" class="form-checkboxes" id="disas">
                                    </div>
                                    <div class="form-check check me-2">
                                        <input type="checkbox" class="form-checkboxes" id="assem">
                                    </div>
                                    <div class="form-check check me-1">
                                        <input type="checkbox" class="form-checkboxes" id="rub">
                                    </div>
                                    <div class="form-check check me-3">
                                        <input type="checkbox" class="form-checkboxes" id="upst">
                                    </div>
                                    <div class="form-check check me-4">
                                        <input type="checkbox" class="form-checkboxes" id="downst">
                                    </div>
                                    <div class="form-check check me-3">
                                        <input type="checkbox" class="form-checkboxes" id="prem">
                                    </div>
                                    <div class="form-check check me-2">
                                        <input type="checkbox" class="form-checkboxes" id="br_trans">
                                    </div>
                                    <div class="form-check check me-1">
                                        <input type="checkbox" class="form-checkboxes" id="ins">
                                    </div>
                                    <div class="form-check check">
                                        <input type="checkbox" class="form-checkboxes" id="h_div">
                                    </div>
                                    <div class="form-check check me-3">
                                        <input type="checkbox" class="form-checkboxes" id="vol">
                                    </div>
                                    <div class="form-check check me-3">
                                        <input type="checkbox" class="form-checkboxes" id="water_con">
                                    </div>
                                    <div class="form-check check me-3">
                                        <input type="checkbox" class="form-checkboxes" id="door_remove">
                                    </div>
                                    <div class="form-check check">
                                        <input type="checkbox" class="form-checkboxes" id="pup">
                                    </div>
                                </div>

                                <!-- items's labels -->

                                <div class="d-flex flex-row mb-3">

                                    <div class="form-check-2">
                                        <label class="form-check-label" for="deliv">DELIV+</label>
                                    </div>

                                    <div class="form-check-2">
                                        <label class="form-check-label" for="disas">DISAS+</label>
                                    </div>
                                    <div class="form-check-2">
                                        <label class="form-check-label" for="assem">ASSEM+</label>
                                    </div>
                                    <div class="form-check-2">
                                        <label class="form-check-label" for="rub">RUB+</label>
                                    </div>
                                    <div class="form-check-2">
                                        <label class="form-check-label" for="upst">UPST+</label>
                                    </div>
                                    <div class="form-check-2">
                                        <label class="form-check-label" for="downst">DOWNST+</label>
                                    </div>
                                    <div class="form-check-2">
                                        <label class="form-check-label" for="prem">PREM+</label>
                                    </div>
                                    <div class="form-check-2">
                                        <label class="form-check-label" for="br_trans">BRTrans+</label>
                                    </div>
                                    <div class="form-check-2">
                                        <label class="form-check-label" for="ins">Ins+</label>
                                    </div>
                                    <div class="form-check-2">
                                        <label class="form-check-label" for="h_dliv">H/Dliv+</label>
                                    </div>
                                    <div class="form-check-2">
                                        <label class="form-check-label" for="vol">Vol+</label>
                                    </div>
                                    <div class="form-check-2">
                                        <label class="form-check-label" for="water_con">WaterCon+</label>
                                    </div>
                                    <div class="form-check-2">
                                        <label class="form-check-label" for="door_remove">Door/R+</label>
                                    </div>
                                    <div class="form-check-2">
                                        <div class=" d-flex align-items-center">
                                            <label for="pup" class="form-check-label">
                                                <select id="pup" class="custom-select label-custom-width">
                                                    <option value="0">P/UP</option>
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
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- items input fields -->

                                <div class="d-flex flex-row">
                                    <div class="inv-items">
                                        <input type="text" id="deliv" name="deliv" class="form-control item-value-1" value="<?php echo htmlspecialchars($inv_deliv); ?>" oninput="calculateItems()">
                                    </div>
                                    <div class="inv-items">
                                        <input type="text" id="disas" name="disas" class="form-control item-value-2" value="<?php echo htmlspecialchars($inv_disas); ?>" oninput="calculateItems()">
                                    </div>
                                    <div class="inv-items">
                                        <input type="text" id="assem" name="assem" class="form-control item-value-3" value="<?php echo htmlspecialchars($inv_assem); ?>" oninput="calculateItems()">
                                    </div>
                                    <div class="inv-items">
                                        <input type="text" id="rub" name="rub" class="form-control item-value-4" value="<?php echo htmlspecialchars($inv_rub); ?>" oninput="calculateItems()">
                                    </div>
                                    <div class="inv-items">
                                        <input type="text" id="upst" name="upst" class="form-control item-value-5" value="<?php echo htmlspecialchars($inv_upst); ?>" oninput="calculateItems()">
                                    </div>
                                    <div class="inv-items">
                                        <input type="text" id="downst" name="downst" class="form-control item-value-6" value="<?php echo htmlspecialchars($inv_downst); ?>" oninput="calculateItems()">
                                    </div>
                                    <div class="inv-items">
                                        <input type="text" id="prem" name="prem" class="form-control item-value-7" value="<?php echo htmlspecialchars($inv_prem); ?>" oninput="calculateItems()">
                                    </div>
                                    <div class="inv-items">
                                        <input type="text" id="br_trans" name="br_trans" class="form-control item-value-8" value="<?php echo htmlspecialchars($inv_br_trans); ?>" oninput="calculateItems()">
                                    </div>
                                    <div class="inv-items">
                                        <input type="text" id="ins" name="ins" class="form-control item-value-9" value="<?php echo htmlspecialchars($inv_ins); ?>" oninput="calculateItems()">
                                    </div>
                                    <div class="inv-items">
                                        <input type="text" id="h_div" name="h_div" class="form-control item-value-10" value="<?php echo htmlspecialchars($inv_h_div); ?>" oninput="calculateItems()">
                                    </div>
                                    <div class="inv-items">
                                        <input type="text" id="vol" name="vol" class="form-control item-value-11" value="<?php echo htmlspecialchars($inv_vol); ?>" oninput="calculateItems()">
                                    </div>
                                    <div class="inv-items">
                                        <input type="text" id="water_con" name="water_con" class="form-control item-value-12" value="<?php echo htmlspecialchars($inv_water_con); ?>" oninput="calculateItems()">
                                    </div>
                                    <div class="inv-items">
                                        <input type="text" id="door_remove" name="door_remove" class="form-control item-value-13" value="<?php echo htmlspecialchars($inv_door_remove); ?>" oninput="calculateItems()">
                                    </div>
                                    <div class="inv-items">
                                        <input type="text" id="pup" name="pup" class="form-control item-value-14" value="<?php echo htmlspecialchars($inv_pup); ?>" oninput="calculateItems()">
                                    </div>
                                </div>

                            </div>
                        </td>

                        <td>
                            <div class="form-group">
                                <input type="text" id="amount" name="amount" class="form-control custom-width-5" readonly placeholder="$0.00" value="<?php echo htmlspecialchars($inv_amount); ?>">
                            </div>
                        </td>

                        </form>
                    </tr>
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
                    <form action="invoice.php<?php echo isset($_GET['invoice_id']) ? '?id=' . $_GET['invoice_id'] : ''; ?>" method="POST">

                        <div class="form-group table-2-width">
                            <tr>
                                <td><strong><label for="sub_total" class="bold-label">Sub Total</label></strong></td>
                                <td><strong><input type="text" id="sub_total" name="sub_total" class="form-control bg-transparent" placeholder="$0.00" value="<?php echo htmlspecialchars($inv_sub_total); ?>" readonly></strong></td>
                            </tr>
                        </div>

                        <div class="form-group table-2-width">
                            <tr>
                                <td><label for="tax_rate">Tax Rate</label></td>
                                <td><input type="text" id="tax_rate" name="tax_rate" class="form-control bg-transparent" placeholder="$0.00" value="<?php echo htmlspecialchars($inv_tax_rate); ?>" oninput="calculateSubtotal()"></td>
                            </tr>
                        </div>

                        <div class="form-group table-2-width">
                            <tr>
                                <td><label for="other_cost">Other Costs</label></td>
                                <td><input type="text" id="other_cost" name="other_cost" class="form-control bg-transparent" placeholder="$0.00" value="<?php echo htmlspecialchars($inv_other_cost); ?>" oninput="calculateSubtotal()"></td>
                            </tr>
                        </div>

                        <div class="form-group table-2-width">
                            <tr>
                                <td><strong><label for="total_cost" class="bold-label">Total Cost</label></strong></td>
                                <td><strong><input type="text" id="total_cost" name="total_cost" class="form-control bg-transparent" placeholder="$0.00" value="<?php echo htmlspecialchars($inv_total_cost); ?>" readonly></strong></td>
                            </tr>
                        </div>
                    </form>
                </table>
            </div>
        </div>
        <!-- <button type="submit" class="btn btn-primary">Save Invoice</button> -->
    </div>
</div>


    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    
</body>
</html>