<?php
include 'connect_db.php'; // Include database connection

$query = "SELECT * FROM Products WHERE Status = 'In-Stock' ORDER BY Width";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>".$row["ReelNumber"]."</td>
                <td>".$row["Width"]."</td>
                <td>".$row["Length"]."</td>
                <td>".$row["GSM"]."</td>
                <td>".$row["Grade"]."</td>

<td>".$row["Breaks"]."</td>
<td>".$row["Location"]."</td>
<td>".$row["Status"]."</td>
<td>".$row["Comments"]."</td>




              </tr>";
    }
} else {
    echo "<tr><td colspan='9'>No In-Stock Rolls Available</td></tr>";
}
?>

