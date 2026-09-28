<!DOCTYPE html>

<html>

<head>

<title>Supplier Dashboard</title>

<link rel="stylesheet" href="style.css">

</head>

<body>


<div class="logo-section">
    <div class="brand">
        <img src="logo.png" alt="FixLink Logo">
        <span>FixLink</span>
    </div>
    <h1>Part Supplier Dashboard</h1>
</div>





<div class="layout">

<?php
$iframeMode = true;
require __DIR__ . "/layout/sidebar.php";
?>

<main class="content">

<div class="content-panel">

<iframe
name="contentFrame"
id="contentFrame"
srcdoc='<!DOCTYPE html><html><head><style>
body { margin:0; padding:0; background-color:#0b1220; height:100vh; display:flex; align-items:center; justify-content:center; }
img { max-width:90%; max-height:90%; width:auto; height:auto; }
</style></head><body>
<img src="fixlink_logo_vector.png" alt="FixLink logo">
</body></html>'>
</iframe>

</div>

</main>

</div>

<hr>

<p align="center">

Copyright &copy;

<?php echo date("Y"); ?>

</p>

</body>

</html>
