<?php
include 'connect_db.php';

// Fetch Free Trucks for Dropdown
$trucksQuery = "SELECT TruckID, LicenseNumber FROM Trucks WHERE Status = 'Free'";
$trucksResult = $conn->query($trucksQuery);

// Fetch Suppliers for Dropdown
$suppliersQuery = "SELECT SupplierID, SupplierName FROM Suppliers";
$suppliersResult = $conn->query($suppliersQuery);

// Create Shipment
if (isset($_POST['create_shipment'])) {
    $truckID = $_POST['truck_id'];
    $supplierID = $_POST['supplier_id'];
    $materialType = $_POST['material_type'];
    $materialName = $_POST['material_name'];
    $shipmentType = $_POST['shipment_type'];
    $location = 'Entrance';
    $entryTime = date("Y-m-d H:i:s");

    // Fetch License Number from Trucks
    $truckQuery = "SELECT LicenseNumber FROM Trucks WHERE TruckID = ?";
    $truckStmt = $conn->prepare($truckQuery);
    $truckStmt->bind_param("i", $truckID);
    $truckStmt->execute();
    $truckResult = $truckStmt->get_result();
    $truckRow = $truckResult->fetch_assoc();
    $licenseNumber = $truckRow['LicenseNumber'];
    $truckStmt->close();

    // Fetch Supplier Name from Suppliers
    $supplierQuery = "SELECT SupplierName FROM Suppliers WHERE SupplierID = ?";
    $supplierStmt = $conn->prepare($supplierQuery);
    $supplierStmt->bind_param("i", $supplierID);
    $supplierStmt->execute();
    $supplierResult = $supplierStmt->get_result();
    $supplierRow = $supplierResult->fetch_assoc();
    $supplierName = $supplierRow['SupplierName'];
    $supplierStmt->close();

    // Fetch Material ID from RawMaterials
    $materialQuery = "SELECT MaterialID FROM RawMaterials WHERE MaterialType = ? AND MaterialName = ? AND SupplierID = ?";
    $materialStmt = $conn->prepare($materialQuery);
    $materialStmt->bind_param("ssi", $materialType, $materialName, $supplierID);
    $materialStmt->execute();
    $materialResult = $materialStmt->get_result();
    $materialRow = $materialResult->fetch_assoc();
    $materialID = $materialRow['MaterialID'];
    $materialStmt->close();

    // Transaction to ensure atomicity
    $conn->begin_transaction();
    try {
        // Insert into Shipments
        $insertShipmentQuery = "INSERT INTO Shipments (Status, Location, TruckID, LicenseNumber, SupplierName, SupplierID, MaterialType, MaterialName, MaterialID, EntryTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $insertShipment = $conn->prepare($insertShipmentQuery);
        $insertShipment->bind_param("ssississis", $shipmentType, $location, $truckID, $licenseNumber, $supplierName, $supplierID, $materialType, $materialName, $materialID, $entryTime);
        $insertShipment->execute();
        $insertShipment->close();

        // Update Truck Status
        $updateTruckQuery = "UPDATE Trucks SET Status = 'Busy' WHERE TruckID = ?";
        $updateTruck = $conn->prepare($updateTruckQuery);
        $updateTruck->bind_param("i", $truckID);
        $updateTruck->execute();
        $updateTruck->close();

        $conn->commit();
        echo "<p style='color:green;'>Shipment created and truck status updated successfully!</p>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<p style='color:red;'>Error creating shipment: " . $e->getMessage() . "</p>";
    }
}

// HTML Form for Creating Shipment
echo "<form method='post'>";
echo "<h2>Create Shipment</h2>";

echo "Truck (License Number): <select name='truck_id' id='truck_id'>";
while ($row = $trucksResult->fetch_assoc()) {
    echo "<option value='" . $row['TruckID'] . "'>" . $row['LicenseNumber'] . "</option>";
}
echo "</select> <br>";

echo "Supplier: <select name='supplier_id' id='supplier_id' onchange='loadMaterialTypes()'>";
echo "<option value=''>Select Supplier</option>";
while ($row = $suppliersResult->fetch_assoc()) {
    echo "<option value='" . $row['SupplierID'] . "'>" . $row['SupplierName'] . "</option>";
}
echo "</select> <br>";

echo "Material Type: <select name='material_type' id='material_type' onchange='loadMaterialNames()'>";
echo "<option value=''>Select Material Type</option>";
echo "</select> <br>";

echo "Material Name: <select name='material_name' id='material_name'>";
echo "<option value=''>Select Material Name</option>";
echo "</select> <br>";

echo "Shipment Type: <select name='shipment_type'>
    <option value='Incoming'>Incoming</option>
    <option value='Outgoing'>Outgoing</option>
</select> <br>";

echo "<input type='submit' name='create_shipment' value='Create Shipment'>";
echo "</form>";

// Include jQuery for AJAX
echo "<script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>";

// JavaScript for dynamic dropdowns
echo "<script type='text/javascript'>
    function loadMaterialTypes() {
        var supplierId = $('#supplier_id').val();
        $.ajax({
            url: 'get_material_types.php',
            type: 'POST',
            data: {supplier_id: supplierId},
            success: function(response) {
                $('#material_type').html(response);
                $('#material_type').change(); // Trigger change to reset material names
            }
        });
    }

    function loadMaterialNames() {
        var materialType = $('#material_type').val();
        var supplierId = $('#supplier_id').val();
        $.ajax({
            url: 'get_material_names.php',
            type: 'POST',
            data: {material_type: materialType, supplier_id: supplierId},
            success: function(response) {
                $('#material_name').html(response);
            }
        });
    }
</script>";
echo "</body></html>";
$conn->close();
?>