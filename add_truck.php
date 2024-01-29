<?php
include 'connect_db.php';

// Farsi Letters Array
$farsiLetters = ['الف', 'ب', 'پ', 'ت', 'ث', 'ج', 'چ', 'ح', 'خ', 'د', 'ذ', 'ر', 'ز', 'ژ', 'س', 'ش', 'ص', 'ض', 'ط', 'ظ', 'ع', 'غ', 'ف', 'ق', 'ک', 'گ', 'ل', 'م', 'ن', 'و', 'ه', 'ی'];

function formatLicenseNumber($digit1, $letter, $digit2, $digit3) {
    return $digit1 . '-' . $letter . '-' . $digit2 . ' IR ' . $digit3;
}

// Check License Number
$licenseExists = false;
if (isset($_POST['check_license'])) {
    $licenseNumber = formatLicenseNumber($_POST['digit1'], $_POST['farsi_letter'], $_POST['digit2'], $_POST['digit3']);
    $checkQuery = "SELECT * FROM Trucks WHERE LicenseNumber = '$licenseNumber'";
    $result = $conn->query($checkQuery);
    if ($result->num_rows > 0) {
        $licenseExists = true;
        echo "<p style='color:red;'>License Number already exists in the database.</p>";
    }
}

// Insert Truck Data
if (isset($_POST['add_truck']) && !$licenseExists) {
    $licenseNumber = formatLicenseNumber($_POST['digit1'], $_POST['farsi_letter'], $_POST['digit2'], $_POST['digit3']);
    $driverName = $conn->real_escape_string($_POST['driver_name']);
    $phone = $conn->real_escape_string($_POST['phone']);

    $insertQuery = "INSERT INTO Trucks (LicenseNumber, DriverName, Phone, Status, Location) VALUES ('$licenseNumber', '$driverName', '$phone', 'Free', 'Entrance')";
    
    if ($conn->query($insertQuery) === TRUE) {
        echo "<p style='color:green;'>New truck added successfully!</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}

// HTML Form for Checking License Number
echo "<form method='post'>";
echo "<h2>Check Truck License Number</h2>";
echo "2 Digits: <input type='text' name='digit1' required size='2' maxlength='2'> <br><br>";
echo "Farsi Letter: <select name='farsi_letter'> ";
foreach ($farsiLetters as $letter) {
    echo "<option value='$letter'>$letter</option>";
}
echo "</select> <br><br>";
echo "3 Digits: <input type='text' name='digit2' required size='3' maxlength='3'> <br><br>";
echo "IR <input type='text' name='digit3' required size='2' maxlength='2'> <br><br>";
echo "<input type='submit' name='check_license' value='Check'> <br><br>";
echo "</form>";

// HTML Form for Adding Truck
if (!$licenseExists) {
    echo "<form method='post'>";
    echo "<h2>Add New Truck</h2>";
    echo "2 Digits: <input type='text' name='digit1' required size='2' maxlength='2' value='" . ($_POST['digit1'] ?? '') . "' readonly> ";
echo "Farsi Letter: <input type='text' name='farsi_letter' required value='" . ($_POST['farsi_letter'] ?? '') . "' readonly> ";
echo "3 Digits: <input type='text' name='digit2' required size='3' maxlength='3' value='" . ($_POST['digit2'] ?? '') . "' readonly> <br>";
echo "IR <input type='text' name='digit3' required size='2' maxlength='2' value='" . ($_POST['digit3'] ?? '') . "' readonly> ";
echo "<br>";
echo "<br> Driver Name: <input type='text' name='driver_name' required> <br>";
echo "Phone: <input type='text' name='phone' required> <br><br>";
echo "<input type='submit' name='add_truck' value='Add Truck'> <br><br>";
echo "</form>";
}

echo "</body></html>";

$conn->close();
?>