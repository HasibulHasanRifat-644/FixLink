<!DOCTYPE html>

<html>

<head>

<title>Part Requests</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<?php if (!$embedded): ?>

<?php
$pageTitle = "Part Requests";
require __DIR__ . "/../layout/topbar.php";
?>

<div class="layout">

<?php require __DIR__ . "/../layout/sidebar.php"; ?>

<main class="content">

<?php endif; ?>

<table border="1" cellpadding="10" align="center">

<tr>

<th>Request ID</th>

<th>Technician ID</th>

<th>Part</th>

<!-- ADDED -->
<th>Requested For</th>

<th>Quantity</th>

<th>Status</th>

<th>Request Date</th>

<th>Action</th>

</tr>

<?php while ($row = $requests->fetch_assoc()): ?>

<tr>

<td>

<?php echo $row["id"]; ?>

</td>

<td>

<?php echo $row["technician_id"]; ?>

</td>

<td>

<?php echo $row["part_name"] !== null ? htmlspecialchars($row["part_name"]) : "Part not found"; ?>

</td>

<!-- ADDED: shows the customer's original device/part need, if this
     request was linked to one via Technician_parts.php -->
<td>

<?php if ($row["customer_device_name"] !== null): ?>

<?php echo htmlspecialchars($row["customer_device_name"]); ?> &mdash; <?php echo htmlspecialchars($row["customer_requested_part_name"]); ?>

<?php else: ?>

<span style="color:#64748b;">&mdash;</span>

<?php endif; ?>

</td>

<td>

<?php echo $row["quantity"]; ?>

</td>

<td>

<?php echo htmlspecialchars($row["status"]); ?>

</td>

<td>

<?php echo $row["request_date"]; ?>

</td>

<td>

<?php if ($row["status"] === "Pending"): ?>

<a href="index.php?page=updateRequest&id=<?php echo $row["id"]; ?>&status=Accepted<?php echo $embedded ? '&embed=1' : ''; ?>">

Accept

</a>

|

<a href="index.php?page=updateRequest&id=<?php echo $row["id"]; ?>&status=Rejected<?php echo $embedded ? '&embed=1' : ''; ?>">

Reject

</a>

<?php else: ?>

No Action

<?php endif; ?>

</td>

</tr>

<?php endwhile; ?>

</table>

<?php if (!$embedded): ?>

</main>

</div>

<hr>

<p align="center">

Copyright &copy;

<?php echo date("Y"); ?>

</p>

<?php endif; ?>

</body>

</html>
