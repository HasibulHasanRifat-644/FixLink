<!DOCTYPE html>

<html>

<head>

<title><?php echo $mode === "add" ? "Add Part" : "Edit Part"; ?></title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<?php if (!$embedded): ?>

<?php
$pageTitle = $mode === "add" ? "Add New Part" : "Edit Part";
require __DIR__ . "/../layout/topbar.php";
?>

<div class="layout">

<?php require __DIR__ . "/../layout/sidebar.php"; ?>

<main class="content">

<?php endif; ?>

<?php
$actionPage = $mode === "add" ? "addPart" : "editPart";
$actionUrl = "index.php?page=" . $actionPage;
if ($mode === "edit") {
    $actionUrl .= "&id=" . $part["id"];
}
if ($embedded) {
    $actionUrl .= "&embed=1";
}
?>

<form method="POST" action="<?php echo $actionUrl; ?>">

<table align="center" cellpadding="10">

<tr>

<td>

Part Name:

</td>

<td>

<input type="text" name="part_name" value="<?php echo htmlspecialchars($part["part_name"]); ?>" required>

</td>

</tr>

<tr>

<td>

Category:

</td>

<td>

<select name="category" required>

<option value="">Select Category</option>

<?php foreach (array("Battery", "Keyboard", "Display", "Storage", "Charger", "Motherboard", "Other") as $cat): ?>

<option value="<?php echo $cat; ?>" <?php echo ($part["category"] === $cat) ? "selected" : ""; ?>>

<?php echo $cat; ?>

</option>

<?php endforeach; ?>

</select>

</td>

</tr>

<tr>

<td>

Description:

</td>

<td>

<textarea name="description" rows="4" cols="30"><?php echo htmlspecialchars($part["description"]); ?></textarea>

</td>

</tr>

<tr>

<td>

Price (per day):

</td>

<td>

<input type="number" step="0.01" name="price" id="price" value="<?php echo $part["price"]; ?>" oninput="updateTotal()" required>

</td>

</tr>

<tr>

<td>

Security Deposit:

</td>

<td>

<input type="number" step="0.01" name="security_deposit" id="security_deposit" value="<?php echo $part["security_deposit"]; ?>" oninput="updateTotal()" required>

</td>

</tr>

<tr>

<td>

Rental Duration (days):

</td>

<td>

<input type="number" name="rental_duration_days" id="rental_duration_days" value="<?php echo $part["rental_duration_days"]; ?>" min="1" oninput="updateTotal()" required>

</td>

</tr>

<tr>

<td>

Stock:

</td>

<td>

<input type="number" name="stock" value="<?php echo $part["stock"]; ?>" required>

</td>

</tr>

<tr>

<td>

Estimated Total:

</td>

<td>

<b id="estimated_total">0.00</b>

<span style="color:#64748b; font-size:12px;">(price &times; duration + deposit)</span>

</td>

</tr>

<tr>

<td colspan="2" align="center">

<input type="submit" name="submit" value="<?php echo $mode === "add" ? "Add Part" : "Update Part"; ?>">

</td>

</tr>

</table>

</form>

<script>
function updateTotal() {
    var price = parseFloat(document.getElementById("price").value) || 0;
    var deposit = parseFloat(document.getElementById("security_deposit").value) || 0;
    var duration = parseFloat(document.getElementById("rental_duration_days").value) || 0;
    var total = (price * duration) + deposit;
    document.getElementById("estimated_total").innerText = total.toFixed(2);
}
updateTotal();
</script>

<br>

<center>

<a href="index.php?page=parts<?php echo $embedded ? '&embed=1' : ''; ?>">

<input type="button" value="Back to My Parts">

</a>

</center>

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
