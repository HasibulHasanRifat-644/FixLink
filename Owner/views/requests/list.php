<!DOCTYPE html>
<html>
<head>
<title>Rental Requests</title>
<link rel="stylesheet" href="design.css">
</head>
<body>

<?php if (!$embedded): ?>

<?php
$pageTitle = "Rental Requests";
require __DIR__ . "/../layout/topbar.php";
?>

<div class="layout">

<?php require __DIR__ . "/../layout/sidebar.php"; ?>

<main class="content">

<?php endif; ?>

<fieldset>

<table border="1" cellpadding="10" align="center">

<tr>

<th>Request ID</th>

<th>Technician ID</th>

<th>Equipment</th>

<th>Rental Days</th>

<th>Status</th>

<th>Request Date</th>

<th>Action</th>

</tr>

<?php while ($row = $requests->fetch_assoc()): ?>

<tr>

<td><?php echo $row["id"]; ?></td>

<td><?php echo $row["technician_id"]; ?></td>

<td><?php echo $row["equipment_name"] !== null ? htmlspecialchars($row["equipment_name"]) : "Equipment not found"; ?></td>

<td><?php echo $row["rental_days"]; ?></td>

<td><?php echo htmlspecialchars($row["status"]); ?></td>

<td><?php echo $row["request_date"]; ?></td>

<td>

<?php if ($row["status"] === "Pending"): ?>

<a href="index.php?page=updateRequest&id=<?php echo $row["id"]; ?>&status=Accepted<?php echo $embedded ? '&embed=1' : ''; ?>">Accept</a>
|
<a href="index.php?page=updateRequest&id=<?php echo $row["id"]; ?>&status=Rejected<?php echo $embedded ? '&embed=1' : ''; ?>">Reject</a>

<?php else: ?>

No Action

<?php endif; ?>

</td>

</tr>

<?php endwhile; ?>

</table>

</fieldset>

<?php if (!$embedded): ?>

</main>

</div>

<hr>

<?php endif; ?>

</body>
</html>
