// For Cloning and Removing Rows //

document.addEventListener("DOMContentLoaded", () => {
    const table = document.querySelector(".table-container"); // Select the table container
    const addButton = document.querySelector(".add-button"); // Add Row button
    const removeButton = document.querySelector(".remove-button"); // Remove Row button
    const saveButton = document.querySelector(".save-button"); // Save button (assume it exists)
    let isModified = false; // Track if the table has been modified

    // Function to style the rows
    function styleRows() {
        const rows = table.querySelectorAll("tr");
        rows.forEach((row, index) => {
            row.style.backgroundColor = ""; // Clear existing styles
            if (index === 1) {
                row.style.backgroundColor = "#ebebeb"; // Apply gray to the second row
            } else if (index > 0) {
                row.style.backgroundColor = index % 2 === 0 ? "#f7f5f5" : "#ebebeb"; // Alternate row colors
            }
        });
    }

    // Function to attach checkbox listeners for dynamic input visibility
    function attachCheckboxListeners(row) {
        const checkboxes = row.querySelectorAll(".form-checkboxes");
        checkboxes.forEach((checkbox) => {
            checkbox.addEventListener("change", function () {
                const inputField = row.querySelector(`input[name="${this.id}"]`);
                if (this.checked) {
                    inputField.style.display = "block"; // Show input field
                } else {
                    inputField.style.display = "none"; // Hide input field
                }
            });
        });

        // Keep "Amount" and "Customer's Inv No" fields always visible
        const amountField = row.querySelector("input[name='amount']");
        const customerInvNoField = row.querySelector("input[name='customer-inv-no']");
        if (amountField) amountField.style.display = "block";
        if (customerInvNoField) customerInvNoField.style.display = "block";
    }
    
    // Add a new row to the table
    addButton.addEventListener("click", () => {
        isModified = true; // Mark the table as modified
        const rows = table.querySelectorAll("tr");
        if (rows.length >= 21) { // Including header row
            alert("You cannot add more than 20 rows.");
            return;
        }

        const lastRow = rows[rows.length - 1];
        const newRow = lastRow.cloneNode(true); // Clone the last row

        // Reset input values and visibility in the cloned row
        const inputs = newRow.querySelectorAll("input");
        inputs.forEach((input) => {
            if (input.type === "checkbox") {
                input.checked = false; // Uncheck checkboxes
            } else if (input.type === "text") {
                input.value = ""; // Clear text inputs
                if (input.name === 'amount' || input.name === 'customer-inv-no') {
                    input.style.display = "block"; // Keep Amount and Customer's Inv No visible
                } else {
                    input.style.display = "none"; // Hide other fields initially
                }
            }
        });

        table.appendChild(newRow); // Append the new row to the table
        attachCheckboxListeners(newRow); // Attach listeners to the new row
        styleRows(); // Reapply styles after adding a new row
    });

    // Remove the last row from the table
    removeButton.addEventListener("click", () => {
        isModified = true; // Mark the table as modified
        const rows = table.querySelectorAll("tr");
        if (rows.length <= 2) { // Prevent removing the header row
            alert("You cannot remove the first row.");
            return;
        }

        const isConfirmed = confirm("Are you sure you want to remove this row?");
        if (isConfirmed) {
            table.removeChild(rows[rows.length - 1]); // Remove the last row
            styleRows(); // Reapply styles after removing a row
        }
    });

    // Save changes and reset the modified flag
    saveButton.addEventListener("click", () => {
        isModified = false; // Mark as saved
        alert("Invoice have been saved successfully.");
    });

    // Warn the user when they try to reload the page if the table has been modified
    window.addEventListener("beforeunload", (event) => {
        if (isModified) {
            event.preventDefault();
            event.returnValue = ""; // Some browsers require this line for the dialog to show
        }
    });

    // Attach listeners to the initial rows and ensure visibility of specific fields
    const initialRows = table.querySelectorAll("tr");
    initialRows.forEach((row) => attachCheckboxListeners(row));

    // Initial styling when the page loads
    styleRows();
});

// Checkbox Appear //

document.addEventListener('DOMContentLoaded', () => {
    // Select all checkboxes
    const checkboxes = document.querySelectorAll('.form-checkboxes');

    checkboxes.forEach(checkbox => {
        // Attach change event listener to each checkbox
        checkbox.addEventListener('change', function () {
            const inputField = document.querySelector(`input[name="${this.id}"]`);
            if (this.checked) {
                inputField.style.display = 'block'; // Show input field
            } else {
                inputField.style.display = 'none'; // Hide input field
            }
        });
    });

    // Initially hide all input fields
    const inputFields = document.querySelectorAll('.inv-items input');
    inputFields.forEach(input => input.style.display = 'none');
});


// Calculation //

function calculateItems() {
    const ids = [
        'deliv', 'disas', 'assem', 'rub', 'upst', 'downst',
        'prem', 'br_trans', 'ins', 'h_div', 'vol',
        'water_con', 'door_remove', 'pup'
    ];

    let amount = 0;

    // Iterate through all IDs, calculate the sum, and update the formatted value
    ids.forEach(id => {
        const inputElement = document.getElementById(id);

        // Check if the element exists
        if (inputElement) {
            const value = parseFloat(inputElement.value) || 0;
            console.log(`ID: ${id}, Value: ${value}`); // Debugging output
            amount += value;
            inputElement.value = value.toFixed(2); // Format the input value
        } else {
            console.error(`Element with ID '${id}' not found!`); // Error message for missing element
        }
    });

    // Display the total amount in the #amount input field
    const amountField = document.getElementById('amount');
    if (amountField) {
        amountField.value = amount.toFixed(2);
        console.log(`amount: ${amount.toFixed(2)}`); // Debugging output
    } else {
        console.error("Element with ID 'amount' not found!");
    }
}

function calculateAmount() {
    var amount = 0;

    // Loop through checkboxes and add to amount if checked
    var checkboxes = document.querySelectorAll('.item-checkbox:checked');
    checkboxes.forEach(function (checkbox) {
        amount += parseFloat(checkbox.dataset.amount);
    });

    document.getElementById('amount').value = amount.toFixed(2);
    calculateSubtotal();
}

function calculateSubtotal() {
    var amount = parseFloat(document.getElementById('amount').value) || 0;
    var other_cost = parseFloat(document.getElementById('other_cost').value) || 0;
    var tax_rate = parseFloat(document.getElementById('tax_rate').value) || 0;

    // Subtotal is just the amount
    var sub_total = amount;

    // Total cost is subtotal + tax_rate + other_cost
    var total_cost = sub_total + tax_rate + other_cost;

    // Update the fields dynamically
    document.getElementById('sub_total').value = sub_total.toFixed(2);
    document.getElementById('total_cost').value = total_cost.toFixed(2);

}






















