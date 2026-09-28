<!DOCTYPE html>
<html>
<head>
<title><?php echo $mode === "add" ? "Add Equipment" : "Edit Equipment"; ?></title>
<link rel="stylesheet" href="design.css">
</head>
<body>

<?php if (!$embedded): ?>

<?php
$pageTitle = $mode === "add" ? "Add New Equipment" : "Edit Equipment";
require __DIR__ . "/../layout/topbar.php";
?>

<div class="layout">

<?php require __DIR__ . "/../layout/sidebar.php"; ?>

<main class="content">

<?php endif; ?>

<?php
$actionPage = $mode === "add" ? "addEquipment" : "editEquipment";
$actionUrl = "index.php?page=" . $actionPage;
if ($mode === "edit") {
    $actionUrl .= "&id=" . $item["id"];
}
if ($embedded) {
    $actionUrl .= "&embed=1";
}
?>

<form method="POST" action="<?php echo $actionUrl; ?>">

<table align="center" cellpadding="10">

<tr>

<td>Equipment Name:</td>

<td><input type="text" name="equipment_name" value="<?php echo htmlspecialchars($item["equipment_name"]); ?>" required></td>

</tr>

<tr>

<td>Category:</td>

<td>

<select name="category" required>

<option value="">Select Category</option>

<?php foreach (array("Testing Equipment", "Repair Equipment", "Measuring Equipment", "Power Equipment", "Other") as $cat): ?>

<option value="<?php echo $cat; ?>" <?php echo ($item["category"] === $cat) ? "selected" : ""; ?>>

<?php echo $cat; ?>

</option>

<?php endforeach; ?>

</select>

</td>

</tr>

<tr>

<td>Brand:</td>

<td><input type="text" name="brand" value="<?php echo htmlspecialchars($item["brand"]); ?>" required></td>

</tr>

<tr>

<td>Model:</td>

<td><input type="text" name="model" value="<?php echo htmlspecialchars($item["model"]); ?>" required></td>

</tr>

<tr>

<td>Description:</td>

<td><textarea name="description" rows="4" cols="30"><?php echo htmlspecialchars($item["description"]); ?></textarea></td>

</tr>

<tr>

<td>Rental Price / Day:</td>

<td><input type="number" name="rental_price" value="<?php echo $item["rental_price"]; ?>" required></td>

</tr>

<!-- REMOVED: the Availability dropdown. Availability is now computed
     automatically (see the "My Equipment" list) based on whether there's
     an active accepted rental request -- the owner no longer sets it by hand. -->

<tr>

<td colspan="2" align="center">

<input type="submit" name="submit" value="<?php echo $mode === "add" ? "Add Equipment" : "Update Equipment"; ?>">

</td>

</tr>

</table>

</form>

<br>

<center>

<a href="index.php?page=equipment<?php echo $embedded ? '&embed=1' : ''; ?>">

<input type="button" value="Back to My Equipment">

</a>

</center>

<?php if (!$embedded): ?>

</main>

</div>

<hr>

<?php endif; ?>

</body>
</html>
