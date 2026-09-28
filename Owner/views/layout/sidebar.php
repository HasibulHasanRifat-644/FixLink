<?php $iframeMode = isset($iframeMode) ? $iframeMode : false; ?>

<aside class="sidebar">

<fieldset>

<ul>

<li>

<a href="index.php?page=dashboard">Dashboard</a>

</li>

<li>

<a href="index.php?page=equipment<?php echo $iframeMode ? '&embed=1' : ''; ?>"
   <?php echo $iframeMode ? 'target="contentFrame"' : ''; ?>>My Equipment</a>

</li>

<li>

<a href="index.php?page=addEquipment<?php echo $iframeMode ? '&embed=1' : ''; ?>"
   <?php echo $iframeMode ? 'target="contentFrame"' : ''; ?>>Add New Equipment</a>

</li>

<li>

<a href="index.php?page=requests<?php echo $iframeMode ? '&embed=1' : ''; ?>"
   <?php echo $iframeMode ? 'target="contentFrame"' : ''; ?>>Rental Requests</a>

</li>

<!-- ADDED -->
<li>

<a href="index.php?page=income<?php echo $iframeMode ? '&embed=1' : ''; ?>"
   <?php echo $iframeMode ? 'target="contentFrame"' : ''; ?>>Income</a>

</li>

<li>

<a href="index.php?page=profile<?php echo $iframeMode ? '&embed=1' : ''; ?>"
   <?php echo $iframeMode ? 'target="contentFrame"' : ''; ?>>My Profile</a>

</li>

<li>

<a href="../Logout.php">Logout</a>

</li>

</ul>

</fieldset>

</aside>
