<!DOCTYPE html>

<html>

<head>

<title>My Profile</title>

<link rel="stylesheet" href="style.css">

</head>

<body>

<?php if(!$embedded): ?>

<?php
$pageTitle = "My Profile";
require __DIR__ . "/layout/topbar.php";
?>

<div class="layout">

<?php require __DIR__ . "/layout/sidebar.php"; ?>

<main class="content">

<?php endif; ?>

<?php if($saved): ?>

<p style="color:#4ade80; text-align:center; margin-bottom:16px;">Profile updated successfully.</p>

<?php endif; ?>

<form method="POST" action="index.php?page=profile<?php echo $embedded ? '&embed=1' : ''; ?>">

<table align="center" cellpadding="10">

<tr>

<td>Name:</td>

<td><input type="text" name="name" value="<?php echo htmlspecialchars($profile["name"]); ?>" required></td>

</tr>

<tr>

<td>Email:</td>

<td><input type="text" value="<?php echo htmlspecialchars($profile["email"]); ?>" disabled></td>

</tr>

<tr>

<td>Phone:</td>

<td><input type="text" name="phone" value="<?php echo htmlspecialchars($profile["phone"]); ?>" required></td>

</tr>

<tr>

<td>Address:</td>

<td><input type="text" name="address" value="<?php echo htmlspecialchars($profile["address"]); ?>" required></td>

</tr>

<tr>

<td colspan="2" align="center">

<input type="submit" name="submit" value="Save Changes">

</td>

</tr>

</table>

</form>

<?php if(!$embedded): ?>

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
