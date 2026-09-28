<?php
$pageTitle = isset($pageTitle) ? $pageTitle : "FixLink";
$displayName = isset($displayName) ? $displayName : $_SESSION["username"];
?>

<h1 align="center">

<?php echo htmlspecialchars($pageTitle); ?>

</h1>

<hr>

<h3 align="right">

Welcome,

<?php echo htmlspecialchars($displayName); ?>

|

<a href="logout.php">Logout</a>

</h3>

<br>
