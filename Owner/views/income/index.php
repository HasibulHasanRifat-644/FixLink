<!DOCTYPE html>
<html>
<head>
<title>Rental Income</title>
<link rel="stylesheet" href="design.css">
</head>
<body>

<?php if (!$embedded): ?>

<?php
$pageTitle = "Rental Income";
require __DIR__ . "/../layout/topbar.php";
?>

<div class="layout">

<?php require __DIR__ . "/../layout/sidebar.php"; ?>

<main class="content">

<div class="content-panel content-panel-stack">

<?php endif; ?>

<div class="stats-grid">

<div class="stat-card">

<div class="stat-label">Total Income</div>

<div class="stat-value">$<?php echo number_format($summary["total_income"], 2); ?></div>

</div>

<div class="stat-card">

<div class="stat-label">Accepted Rentals</div>

<div class="stat-value"><?php echo $summary["total_rentals"]; ?></div>

</div>

</div>

<h3 class="section-heading">Monthly Income</h3>

<?php if (count($summary["monthly"]) === 0): ?>

<p class="empty-message">No accepted rentals yet.</p>

<?php else: ?>

<?php
$maxMonthValue = 0;
foreach ($summary["monthly"] as $value) {
    if ($value > $maxMonthValue) {
        $maxMonthValue = $value;
    }
}
?>

<div class="bar-chart">

<?php foreach ($summary["monthly"] as $monthKey => $value): ?>

<?php
$barHeight = ($maxMonthValue > 0) ? round(($value / $maxMonthValue) * 100) : 0;
$label = date("M Y", strtotime($monthKey . "-01"));
?>

<div class="bar-column">

<div class="bar-value">$<?php echo number_format($value, 0); ?></div>

<div class="bar" style="height: <?php echo $barHeight; ?>%;"></div>

<div class="bar-label"><?php echo $label; ?></div>

</div>

<?php endforeach; ?>

</div>

<?php endif; ?>

<?php if (!$embedded): ?>

</div>

</main>

</div>

<hr>

<?php endif; ?>

</body>
</html>
