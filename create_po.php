<?php
include 'connect_db.php';
ini_set('display_errors', 1); 
error_reporting(E_ALL);

// Fetch Shipments for Dropdown
$shipmentsQuery = "SELECT TruckID, ShipmentID, LicenseNumber FROM Shipments WHERE Status = 'Incoming' AND Location = 'Office'";
$shipmentsResult = $conn->query($shipmentsQuery);

// Handle Form Submission
if (isset($_POST['create_po'])) {

$shipmentId = $_POST['shipment_id'];

    // Fetch TruckID and SupplierID from Shipments table
    $shipmentQuery = "SELECT TruckID, SupplierID FROM Shipments WHERE ShipmentID = ?";
    $shipmentStmt = $conn->prepare($shipmentQuery);
    $shipmentStmt->bind_param("i", $shipmentId);
    $shipmentStmt->execute();
    $shipmentResult = $shipmentStmt->get_result();
    if ($shipmentRow = $shipmentResult->fetch_assoc()) {
        $truckId = $shipmentRow['TruckID'];
        $supplierId = $shipmentRow['SupplierID'];
    } else {
        echo "<p style='color:red;'>Error: Shipment details not found.</p>";
        $conn->close();
        exit;
    }
    $shipmentStmt->close();
    
    
    $supplierName = $_POST['supplier_name']; // From the form
    $materialId = $_POST['material_id']; // From the form
    $materialType = $_POST['material_type']; // From the form
    $materialName = $_POST['material_name']; // From the form

    $pricePerKg = $_POST['price_per_kg'];
    $shippingCosts = $_POST['shipping_costs'];
    $vat = isset($_POST['vat']);
$vatValue = $vat ? 9 : 0;
$vatDecimal = $vatValue/ 100;

    $invoiceStatus = $_POST['invoice_status'];
    $paymentStatus = $_POST['payment_status'];
    $supplierInvoice = $_POST['supplier_invoice'];
    $documentInfo = $_POST['document_info'];
    $comments = $_POST['comments'];
$weight1 = $_POST['weight1'];
$weight2 = $_POST['weight2'];
    $netWeight = $_POST['net_weight'];
    $unit = $_POST['unit'];
    $quantity = $_POST['quantity'];

    // Calculate total price
    $totalPrice = ($pricePerKg * $netWeight) + $shippingCosts;
    if ($vat > 0) {
        $totalPrice += $totalPrice * ($vat / 100);
    }

    // Insert into Purchases
    $insertPurchaseQuery = "INSERT INTO Purchases (Date, SupplierID, TruckID, MaterialID, MaterialType, MaterialName, Unit, Quantity, Weight1, Weight2, NetWeight, ShippingCost, VAT, PricePerKG, TotalPrice, InvoiceStatus, PaymentStatus, InvoiceNumber, DocumentInfo, Comments, ShipmentID) VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insertPurchase = $conn->prepare($insertPurchaseQuery);
    $insertPurchase->bind_param("iiisssiiddidddsssssi", $supplierId, $truckId, $materialId, $materialType, $materialName, $unit, $quantity, $weight1, $weight2, $netWeight, $shippingCosts, $vatDecimal, $pricePerKg, $totalPrice, $invoiceStatus, $paymentStatus, $supplierInvoice, $documentInfo, $comments, $shipmentId);
    $insertPurchase->execute();
    $purchaseId = $conn->insert_id; // Fetch the newly created Purchase ID
    if ($insertPurchase->error) {
        echo "<p style='color:red;'>Error: " . $insertPurchase->error . "</p>";
    } else {
        echo "<p style='color:green;'>Purchase Order created successfully!</p>";
    }
    $insertPurchase->close();


    // Update Shipments
    $updateShipmentQuery = "UPDATE Shipments SET Status = 'Delivered', Location = 'Delivered', NetWeight = ?,PricePerKG = ?, ShippingCost = ?, SupplierID = ?, PurchaseID = ?, VAT = ?, InvoiceStatus = ?, PaymentStatus = ?, ExitTime = NOW(), DocumentInfo = ?, Comments = ? WHERE ShipmentID = ?";
    $updateShipment = $conn->prepare($updateShipmentQuery);
    $updateShipment->bind_param("dddiisssssi", $netWeight, $pricePerKg, $shippingCosts, $supplierId, $purchaseId, $vatDecimal, $invoiceStatus, $paymentStatus, $documentInfo, $comments, $shipmentId);
    $updateShipment->execute();
    $updateShipment->close();

    // Update Trucks
    $updateTruckQuery = "UPDATE Trucks SET Status = 'Free', Location = 'Entrance' WHERE TruckID = ?";
    $updateTruck = $conn->prepare($updateTruckQuery);
    $updateTruck->bind_param("i", $truckId);
    $updateTruck->execute();
    if ($updateTruck->error) {
        echo "<p style='color:red;'>Error updating truck: " . $updateTruck->error . "</p>";
    } else {
        echo "<p style='color:green;'>Truck status updated successfully!</p>";
    }
    $updateTruck->close();

    echo "<p style='color:green;'>Trucks and Shipments UPDATED successfully!</p>";
}

// HTML and JavaScript
echo "<form method='post' id='po_form'>";

echo "<h2>Create Purchase Order</h2>";

echo "Shipment (Lic Number): <select name='shipment_id' id='shipment_id' onchange='loadShipmentDetails()'>";
echo "<option value=''>Select Shipment</option>";
while ($row = $shipmentsResult->fetch_assoc()) {
    echo "<option value='" . $row['ShipmentID'] . "'>" . $row['LicenseNumber'] . "</option>";
}
echo "</select><br>";

echo "Supplier Name: <input type='text' name='supplier_name' id='supplier_name' readonly><br>";
echo "Material ID: <input type='text' name='material_id' id='material_id' readonly><br>";
echo "Material Type: <input type='text' name='material_type' id='material_type' readonly><br>";
echo "Material Name: <input type='text' name='material_name' id='material_name' readonly><br>";

echo "<input type='checkbox' name='approve_data' id='approve_data'> Approve Data<br>";
echo "Weight1: <input type='number' name='weight1' id='weight1' readonly><br>";
echo "Weight2: <input type='number' name='weight2' id='weight2' readonly><br>";
echo "Net Weight: <input type='number' name='net_weight' id='net_weight' readonly><br>";

echo "<input type='checkbox' name='approve_weights' id='approve_weights'> Approve Weights<br>";
echo "Unload Location: <input type='text' name='unload_location' id='unload_location' readonly><br>";

echo "Unit: <select name='unit' id='unit'>";
echo "<option value='Bale'>Bale</option>";
echo "<option value='Pallet'>Pallet</option>";
echo "<option value='Bag'>Bag</option>";
echo "<option value='Other'>Other</option>";
echo "</select><br>";

echo "Quantity: <input type='number' name='quantity' id='quantity' readonly><br>";
echo "<input type='checkbox' name='approve_quantity' id='approve_quantity'> Approve Quantity<br>";

echo "Price Per KG: <input type='number' name='price_per_kg' id='price_per_kg'><br>";
echo "Shipping Costs: <input type='number' name='shipping_costs' id='shipping_costs'><br>";

echo "VAT: <input type='checkbox' name='vat' id='vat' value='9'><br>";
echo "Invoice Status: <select name='invoice_status' id='invoice_status'>";
echo "<option value='Received'>Received</option>";
echo "<option value='NA'>NA</option>";
echo "</select><br>";

echo "Payment Status: <select name='payment_status' id='payment_status'>";
echo "<option value='Terms'>Terms</option>";
echo "<option value='Paid'>Paid</option>";
echo "</select><br>";

echo "Supplier Invoice: <input type='text' name='supplier_invoice' id='supplier_invoice'><br>";
echo "Document Info: <textarea name='document_info' id='document_info'></textarea><br>";
echo "Comments: <textarea name='comments' id='comments'></textarea><br>";

echo "<input type='submit' name='create_po' value='Create PO'>";
echo "</form>";

// Include jQuery
echo "<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>";

// JavaScript for dynamic data loading
echo "<script type='text/javascript'>
    function loadShipmentDetails() {
        var shipmentId = $('#shipment_id').val();
        $.ajax({
            url: 'get_shipment_details.php',
            type: 'POST',
            data: {shipment_id: shipmentId},
            success: function(response) {
                var data = JSON.parse(response);
                $('#supplier_name').val(data.SupplierName);
                $('#material_id').val(data.MaterialID);
                $('#material_type').val(data.MaterialType);
                $('#material_name').val(data.MaterialName);
                $('#weight1').val(data.Weight1);
                $('#weight2').val(data.Weight2);
                $('#net_weight').val(Math.abs(data.Weight1 - data.Weight2));
                $('#unload_location').val(data.UnloadLocation);
                $('#quantity').val(data.Quantity);
            }
        });
    }
</script>";

echo "</body></html>";
$conn->close();
?>