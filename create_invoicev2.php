<?php
include 'connect_db.php';

// Fetch Shipments for Dropdown
$shipmentsQuery = "SELECT ShipmentID, LicenseNumber FROM Shipments WHERE Status = 'Outgoing' AND Location = 'Office'";
$shipmentsResult = $conn->query($shipmentsQuery);

// Fetch Customers for Dropdown
$customersQuery = "SELECT CustomerID, CustomerName FROM Customers";
$customersResult = $conn->query($customersQuery);

// ... [Previous PHP code]

// Handle Form Submission
if (isset($_POST['create_invoice'])) {
    // Capture form data
    // ... [Previous data capture]

    // Calculate Total Price
    $pricePerKg = $_POST['price_per_kg'];
    $netWeight = $_POST['net_weight'];
    $vatChecked = isset($_POST['vat']);
    $shippingCost = $_POST['shipping_cost'];
    $totalPrice = $pricePerKg * $netWeight;
    if ($vatChecked) {
        $totalPrice += $totalPrice * 0.09; // Adding 9% VAT
    }
    $totalPrice += $shippingCost;

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert into Sales
        $insertSalesQuery = "INSERT INTO Sales (Date, CustomerID, TruckID, LicenseNumber, ListofReels, Weight1, Weight2, NetWeight, ShippingCost, VAT, TotalPrice, InvoiceStatus, PaymentStatus, InvoiceNumber, DocumentInfo, Comments, ShipmentID) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $insertSales = $conn->prepare($insertSalesQuery);
        // Bind parameters from form data
        $insertSales->bind_param("iiisssdddidsssss", $customerID, $truckID, $licenseNumber, $listOfReels, $weight1, $weight2, $netWeight, $shippingCost, $vatChecked, $totalPrice, $invoiceStatus, $paymentStatus, $invoiceNumber, $documentInfo, $comments, $shipmentID);
        $insertSales->execute();
        $saleID = $conn->insert_id;

        // Check for errors
        if ($insertSales->error) {
            throw new Exception("Error in inserting sales: " . $insertSales->error);
        }

        // Update Shipments
        // Update query for Shipments table
        // ...

        // Update Trucks
        // Update query for Trucks table
        // ...

        // Update Products (ListofReels)
        // Update query for each reel in the list
        // ...

        // Commit transaction
        $conn->commit();

        echo "<p style='color:green;'>Invoice created successfully with Sale ID: $saleID</p>";
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "<p style='color:red;'>Transaction failed: " . $e->getMessage() . "</p>";
    }
}

// ... [Rest of the script]

// HTML for form
echo "<form method='post' id='sales_form'>";

echo "<h2>Create Invoice</h2>";

// Dropdown for Shipments
echo "Shipment: <select name='shipment_id' id='shipment_id' onchange='loadShipmentDetails()'>";
echo "<option value=''>Select Shipment</option>";
foreach ($shipmentsResult as $row) {
    echo "<option value='" . $row['ShipmentID'] . "'>" . $row['LicenseNumber'] . "</option>";
}
echo "</select><br>";

// Dropdown for Customers
echo "Customer Name: <select name='customer_id' id='customer_id'>";
echo "<option value=''>Select Customer</option>";
foreach ($customersResult as $row) {
    echo "<option value='" . $row['CustomerID'] . "'>" . $row['CustomerName'] . "</option>";
}
echo "</select><br>";

// Additional form fields (readonly and input fields)
// ... [Previous form elements]

// Readonly fields for Shipment Details
echo "ShipmentID: <input type='text' name='shipment_id_display' id='shipment_id_display' readonly><br>";
echo "SaleID: <input type='text' name='sale_id' id='sale_id' readonly><br>";
echo "TruckID: <input type='text' name='truck_id' id='truck_id' readonly><br>";
echo "LicenseNumber: <input type='text' name='license_number' id='license_number' readonly><br>";
echo "ListofReels: <textarea name='list_of_reels' id='list_of_reels' readonly></textarea><br>";
echo "Weight1: <input type='number' name='weight1' id='weight1' readonly><br>";
echo "Weight2: <input type='number' name='weight2' id='weight2' readonly><br>";
echo "NetWeight: <input type='number' name='net_weight' id='net_weight' readonly><br>";

// Input fields for Price, VAT, etc.
echo "PricePerKG: <input type='number' name='price_per_kg' id='price_per_kg'><br>";
echo "VAT 9%: <input type='checkbox' name='vat' id='vat' value='9'><br>";
echo "Shipping Cost: <input type='number' name='shipping_cost' id='shipping_cost'><br>";
echo "InvoiceStatus: <select name='invoice_status' id='invoice_status'>";
echo "<option value='NA'>NA</option>";
echo "<option value='Sent'>Sent</option>";
echo "</select><br>";
echo "PaymentStatus: <select name='payment_status' id='payment_status'>";
echo "<option value='Terms'>Terms</option>";
echo "<option value='Paid'>Paid</option>";
echo "</select><br>";
echo "InvoiceNumber: <input type='text' name='invoice_number' id='invoice_number'><br>";
echo "DocumentInfo: <textarea name='document_info' id='document_info'></textarea><br>";
echo "Comments: <textarea name='comments' id='comments'></textarea><br>";

echo "<input type='submit' name='create_invoice' value='Create Invoice'>";
echo "</form>";

// ... [JavaScript for dynamic data loading]


// Include jQuery
echo "<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>";

// JavaScript for dynamic data loading
echo "<script type='text/javascript'>
    function loadShipmentDetails() {
        var shipmentId = $('#shipment_id').val();
        $.ajax({
            url: 'get_shipment_sales_details.php',
            type: 'POST',
            data: {shipment_id: shipmentId},
            success: function(response) {
                var data = JSON.parse(response);
                // Populate form fields based on the response
                // ... [to be continued in the next prompt]
            }
        });
    }
</script>";

echo "</body></html>";
$conn->close();
?>
