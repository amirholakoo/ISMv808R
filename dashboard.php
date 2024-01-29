<?php
include 'connect_db.php';

// Fetch data for reports
$query = "SELECT * FROM Shipments";
$result = $conn->query($query);

// Dashboard HTML with styling
echo "<!DOCTYPE html><html><head><title>&#127981;ISMv808R Dashboard</title>";
echo "<style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; }
        h1 { color: #4CAF50; }
        nav a { color: #5D5C61; margin-right: 20px; text-decoration: none; font-size: 1.2em; }
        nav a:hover { color: #4CAF50; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #4CAF50; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
      </style>";
echo "</head><body>";

echo "<h1>🏭 ISMv808 Dashboard</h1>";

// Navigation Links
echo "<nav>";
echo "<a href='add_truck.php'>&#128666; Trucks</a> | ";
echo "<a href='add_customer_supplier.php'>👥 Customers & Suppliers</a> | ";
echo "<a href='add_raw_materials.php'>&#128230; Raw Materials </a> | ";
echo "<a href='add_roll.php'>&#129531; Add Rolls </a> | ";
echo "<a href='create_shipment.php'>&#128203; Shipments</a> | ";
echo "<a href='weight_station.php'>&#128200; Weight Station </a> | ";
echo "<a href='forklift_interface.php'>&#127959; Forklift Interface</a> | ";
echo "<a href='sales.php'>💰 Sales</a> | ";
echo "<a href='create_po.php'>🛒 Purchases</a> | ";
echo "<a href='report_page.php'>&#128203; Reports</a><br><br>";
echo "</nav>";

// Shipments Report
echo "<h2>🚛 Shipments Report</h2>";
if ($result->num_rows > 0) {
    echo "<table><tr><th>ID</th><th>Lic Number &#128666;</th><th>&#8592; Status &#8594;</th><th>Location &#128246;</th><th>Quantity &#128202;</th><th>Roll Numbers &#129531;&#129531;&#129531;</th></tr>";
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>".$row["ShipmentID"]."</td><td>".$row["LicenseNumber"]."</td><td>".$row["Status"]."</td><td>".$row["Location"]."</td><td>".$row["Quantity"]."</td><td>".$row["ListofReels"]."</td></tr>";
    }
    echo "</table>";
} else {
    echo "0 results";
}
echo "</body></html>";

$conn->close();
?>