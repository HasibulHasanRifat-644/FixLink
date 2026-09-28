<?php
session_start();
include('db.php');

$cust_id = $_SESSION["id"] ?? 1;
$message = "";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_part_request'])) {
    $device_name = mysqli_real_escape_string($conn, $_POST['device_name']);
    $part_name   = mysqli_real_escape_string($conn, $_POST['part_name']);
    $quantity    = intval($_POST['quantity']);
    $details     = mysqli_real_escape_string($conn, $_POST['details']);

    if (!empty($device_name) && !empty($part_name) && $quantity > 0) {
        $sql = "INSERT INTO part_requests (customer_id, device_name, part_name, quantity, details, status) 
                VALUES ('$cust_id', '$device_name', '$part_name', '$quantity', '$details', 'Pending')";

        if (mysqli_query($conn, $sql)) {
            $message = "<font color='green'><b>Part request submitted successfully!</b></font>";
        } else {
            $message = "<font color='red'><b>Failed to submit part request.</b></font>";
        }
    } else {
        $message = "<font color='red'><b>Please fill in all required fields.</b></font>";
    }
}


$result = mysqli_query($conn, "SELECT * FROM part_requests WHERE customer_id=$cust_id ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Part Requests - FixLink</title>
</head>
<body>

<h1 align="center">Part Requests & Tracking</h1>
<hr>

<div align="center">
    <?php echo $message; ?>
    <br><br>

    
    <form method="POST" action="part_request.php">
        <table border="0" cellpadding="8">
            <tr>
                <td><b>Device / Model Name:</b></td>
                <td><input type="text" name="device_name" placeholder="e.g. HP Pavilion Laptop" required></td>
            </tr>
            <tr>
                <td><b>Part Name:</b></td>
                <td><input type="text" name="part_name" placeholder="e.g. Battery, Keyboard" required></td>
            </tr>
            <tr>
                <td><b>Quantity:</b></td>
                <td><input type="number" name="quantity" value="1" min="1" required></td>
            </tr>
            <tr>
                <td><b>Details / Specification:</b></td>
                <td><textarea name="details" rows="3" cols="25" placeholder="Any specific model or serial no..."></textarea></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input type="submit" name="submit_part_request" value="Submit Part Request">
                    <a href="customerdashboard.php"><input type="button" value="Back"></a>
                </td>
            </tr>
        </table>
    </form>
</div>

<br><hr><br>


<h2 align="center">My Part Requests Status</h2>

<table border="1" cellpadding="10" align="center" width="80%">
    <thead>
        <tr bgcolor="#f1f5f9">
            <th>ID</th>
            <th>Device</th>
            <th>Part Name</th>
            <th>Quantity</th>
            <th>Details</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td align='center'>" . $row['id'] . "</td>";
                echo "<td>" . htmlspecialchars($row['device_name']) . "</td>";
                echo "<td><b>" . htmlspecialchars($row['part_name']) . "</b></td>";
                echo "<td align='center'>" . $row['quantity'] . "</td>";
                echo "<td>" . htmlspecialchars($row['details'] ?? 'N/A') . "</td>";
                echo "<td align='center'>";
                if ($row['status'] == 'Approved' || $row['status'] == 'Available') {
                    echo "<font color='green'><b>" . $row['status'] . "</b></font>";
                } elseif ($row['status'] == 'Delivered') {
                    echo "<font color='blue'><b>Delivered</b></font>";
                } else {
                    echo "<font color='orange'><b>Pending</b></font>";
                }
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6' align='center'>No part requests found.</td></tr>";
        }
        ?>
    </tbody>
</table>

<hr>
<p align="center">
    Copyright &copy; <?php echo date("Y"); ?> FixLink
</p>

</body>
</html>