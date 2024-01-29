<?php
include 'connect_db.php';

// Fetch Shipments for Dropdown
$shipmentsQuery = "SELECT ShipmentID, LicenseNumber FROM Shipments WHERE Status = 'Outgoing' AND Location = 'Office'";
$shipmentsResult = $conn->query($shipmentsQuery);

// Fetch Customers for Dropdown
$customersQuery = "SELECT CustomerID, CustomerName FROM Customers";
$customersResult = $conn->query($customersQuery);

// Handle Form Submission
if (isset($_POST['create_invoice'])) {
    // Extract and sanitize form inputs
    // Process data and insert into Sales
    // Update Shipments, Trucks, and Products
    // Error handling and success message
}

// HTML and JavaScript for form
echo "<form method='post' id='sales_form'>";

echo "<h2>Create Invoice</h2>";

// Dropdown for Shipments
echo "Shipment: <select name='shipment_id' id='shipment_id' onchange='loadShipmentDetails()'>";
echo "<option value=''>Select Shipment</option>";
while ($row = $shipmentsResult->fetch_assoc()) {
    echo "<option value='" . $row['ShipmentID'] . "'>" . $row['LicenseNumber'] . "</option>";
}
echo "</select><br>";

// Dropdown for Customers
echo "Customer Name: <select name='customer_id' id='customer_id'>";
echo "<option value=''>Select Customer</option>";
while ($row = $customersResult->fetch_assoc()) {
    echo "<option value='" . $row['CustomerID'] . "'>" . $row['CustomerName'] . "</option>";
}
echo "</select><br>";

// Additional form fields (readonly and input fields)

echo "<input type='submit' name='create_invoice' value='Create Invoice'>";
echo "</form>";

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
                // Update form fields based on the response
            }
        });
    }
</script>";

echo "</body></html>";
$conn->close();
?>
