<?php
include('db.php');

$value = "";

if (isset($_POST['value'])) {
    $value = mysqli_real_escape_string($conn, trim($_POST['value']));
}

$sql = "SELECT * FROM technicians WHERE 
        name LIKE '%$value%' OR 
        skill LIKE '%$value%' OR 
        location LIKE '%$value%'";

$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['skill'] . "</td>";
        echo "<td>" . $row['location'] . "</td>";
        echo "<td>" . $row['experience'] . "</td>";
        echo "<td>★ " . $row['rating'] . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='6' align='center'>No technicians found matching your query.</td></tr>";
}
?>