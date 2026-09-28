<?php $pageTitle = isset($pageTitle) ? $pageTitle : "FixLink"; ?>

<div class="logo-section">
    <div class="brand">
        <img src="logo.png" alt="FixLink Logo">
        <span>FixLink</span>
    </div>
    <h1><?php echo htmlspecialchars($pageTitle); ?></h1>
</div>

<hr>

<h3 align="right">
Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?> |
<a href="logout.php">Logout</a>
</h3>

<br>
