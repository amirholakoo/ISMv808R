<!-- report_page.php -->

<?php

ini_set('display_errors', 1);

error_reporting(E_ALL);

include 'connect_db.php'; // Include the database connection
include 'jdf.php';

// Section 1: Shipments Data
$shipmentsQuery = "SELECT * FROM Shipments WHERE Status IN ('Incoming', 'Outgoing')";
$shipmentsResult = $conn->query($shipmentsQuery);

// Section 2: Truck Data
$trucksQuery = "SELECT * FROM Shipments WHERE Status = 'Incoming'";
$trucksResult = $conn->query($trucksQuery);

// Section 3: In-Stock Products Data
$inStockProductsQuery = "SELECT ReelNumber, Width, Breaks, Location, Status FROM Products WHERE Status = 'In-Stock'";
$inStockProductsResult = $conn->query($inStockProductsQuery);

// Section 4: Recent Sales Orders
$recentSalesQuery = "SELECT * FROM Sales ORDER BY Date DESC LIMIT 10";
$recentSalesResult = $conn->query($recentSalesQuery);

// Section 5: Recent Purchases
$recentPurchasesQuery = "SELECT * FROM Purchases ORDER BY Date DESC LIMIT 10";
$recentPurchasesResult = $conn->query($recentPurchasesQuery);

// Section 6: Alerts and Notices
$alerts = "Check for any low stock or other important notices here.";



// Start the HTML
echo "<!DOCTYPE html>
<html>
<head>
    <title>Report Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }
        h1, h2 {
            color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: #ffffff;
            padding: 10px;
        }
        td {
            padding: 8px;
            text-align: left;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .alert {
            padding: 10px;
            background-color: #ff9800;
            color: white;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Report Page</h1>";

    // Section 1: Shipments Data
    echo "<h2>Shipments Data</h2>";
    if ($shipmentsResult->num_rows > 0) {
        echo "<table><tr><th>ShipmentID</th><th>Status</th><th>Location</th><th>Customer</th><th>Supplier</th><th>Weight 1</th><th>Weight 2</th><th>Unload Location</th><th>Quantity</th><th>Rolls</th></tr>";
        while($row = $shipmentsResult->fetch_assoc()) {
            echo "<tr><td>" . $row["LicenseNumber"] . "</td><td>" . $row["Status"] . "</td><td>" . $row["Location"] . "</td><td>" . $row["CustomerName"] . "</td><td>" . $row["SupplierName"] . "</td><td>" . $row["Weight1"] . "</td><td>" . $row["Weight2"] . "</td><td>" . $row["UnloadLocation"] . "</td><td>" . $row["Quantity"] . "</td><td>" . $row["ListofReels"] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No Shipments Data Available<br>";
    }

    // Section 2: Truck Data
    echo "<h2>Incoming Trucks</h2>";
    if ($trucksResult->num_rows > 0) {
        echo "<table><tr><th>License Number</th><th>Location</th><th>Supplier Name</th><th>Material Name</th><th>Weight 1</th><th>Weight 2</th><th>NET Weight</th><th>Quantity</th><th>Unload Location</th></tr>";
        while($row = $trucksResult->fetch_assoc()) {

            echo "<tr><td>" . $row["LicenseNumber"] . "</td><td>" . $row["Location"] . "</td><td>" . $row["SupplierName"] . "</td><td>" . $row["MaterialName"] . "</td><td>" . $row["Weight1"] . "</td><td>" . $row["Weight2"] . "</td><td>" . $row["NetWeight"] . "</td><td>" . $row["Quantity"] . "</td><td>" . $row["UnloadLocation"] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No Truck Data Available<br>";
    }

    // Section 3: In-Stock Products Data
    echo "<h2>In-Stock Reel Numbers</h2>";
    if ($inStockProductsResult->num_rows > 0) {
        echo "<table><tr><th>Reel Number</th><th>Width</th><th>Breaks</th><th>Location</th><th>Status</th></tr>";
        while ($row = $inStockProductsResult->fetch_assoc()) {
            echo "<tr><td>" . $row["ReelNumber"] . "</td><td>" . $row["Width"] . "</td><td>" . $row["Breaks"] . "</td><td>" . $row["Location"] . "</td><td>" . $row["Status"] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No In-Stock Reels Available<br>";
    }

    // Section 4: Recent Sales Orders
    echo "<h2>Recent Sales Orders</h2>";
    if ($recentSalesResult->num_rows > 0) {
        echo "<table><tr><th>Sale ID</th><th>Customer ID</th><th>Sale Amount</th><th>Date</th></tr>";
        while ($row = $recentSalesResult->fetch_assoc()) {

    $jalaliDate = jdate("Y/m/d", strtotime($row["Date"])); // Convert to Jalali
 

            echo "<tr><td>" . $row["SaleID"] . "</td><td>" . $row["CustomerID"] . "</td><td>" . $row["SaleAmount"] . "</td><td>" . $jalaliDate . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No Recent Sales Orders<br>";
    }

    // Section 5: Recent Purchases
    echo "<h2>Recent Purchases</h2>";
    if ($recentPurchasesResult->num_rows > 0) {
        echo "<table><tr><th>Purchase ID</th><th>Supplier Name</th><th>Materia lType</th><th>Material Name</th><th>Unit</th><th>Quantity</th><th>NetWeight</th><th>Price Per KG</th><th>ShippingCost</th><th>VAT</th><<th>Total Price</th><th>Purchase Date</th></tr>";
        while ($row = $recentPurchasesResult->fetch_assoc()) {

    $jalaliDate = jdate("Y/m/d", strtotime($row["Date"])); // Convert to Jalali
    

            echo "<tr><td>" . $row["PurchaseID"] . "</td><td>" . $row["SupplierID"] . "</td><td>" . $row["MaterialType"] . "</td><td>" . $row["MaterialName"] . "</td><td>" . $row["Unit"] . "</td><td>" . $row["Quantity"] . "</td><td>" . $row["NetWeight"] . "</td><td>" . $row["PricePerKG"] . "</td><td>" . $row["ShippingCost"] . "</td><td>" . $row["VAT"] . "</td><td>" . $row["TotalPrice"] . "</td><td>" . $jalaliDate .  "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No Recent Purchases<br>";
    }

    // Section 6: Alerts and Notices
    echo "<h2>Alerts and Notices</h2>";
    echo $alerts;

echo "</body>
</html>";

$conn->close(); // Close the database connection
?>
