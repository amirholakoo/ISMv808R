<?php
include 'connect_db.php'; // Include database connection

// Query to fetch all columns from Shipments table except CustomerSupplierID
$query = "SELECT * FROM Shipments WHERE Status != 'Delivered'";
$result = $conn->query($query);

// Check if there are results
if ($result->num_rows > 0) {
    // Output data of each row in table format
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>".$row["ShipmentID"]."</td>
                <td>".$row["TruckID"]."</td>
<td>".$row["LicenseNumber"]."</td>
                <td>".$row["ReelNumbers"]."</td>
                <td>".$row["MaterialID"]."</td>
                <td>".$row["SalesID"]."</td>
                <td>".$row["PurchaseID"]."</td>
                <td>".$row["EntryTime"]."</td>
                <td>".$row["Location"]."</td>
                <td>".$row["UnloadLocation"]."</td>
                <td>".$row["Status"]."</td>
                <td>".$row["LoadedWeight"]."</td>
                <td>".$row["UnloadedWeight"]."</td>
                <td>".$row["ExitTime"]."</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='13'>No Shipments Available</td></tr>";
}
?>
