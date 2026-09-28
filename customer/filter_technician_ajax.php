<?php
include('db.php');

$skill = isset($_POST['skill']) ? mysqli_real_escape_string($conn, trim($_POST['skill'])) : '';
$location = isset($_POST['location']) ? mysqli_real_escape_string($conn, trim($_POST['location'])) : '';
$experience = isset($_POST['experience']) ? mysqli_real_escape_string($conn, trim($_POST['experience'])) : '';
$rating = isset($_POST['rating']) ? mysqli_real_escape_string($conn, trim($_POST['rating'])) : '';

$sql = "SELECT * FROM technicians WHERE 1=1";

if (!empty($skill)) {
    $sql .= " AND skill LIKE '%$skill%'";
}
if (!empty($location)) {
    $sql .= " AND location LIKE '%$location%'";
}
if (!empty($experience)) {
    $sql .= " AND experience LIKE '%$experience%'";
}
if (!empty($rating)) {
    $sql .= " AND rating >= '$rating'";
}

$sql .= " ORDER BY rating DESC";

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
    echo "<tr><td colspan='6' align='center'>No technicians found matching the filter criteria.</td></tr>";
}
?>