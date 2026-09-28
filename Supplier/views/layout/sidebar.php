<?php $iframeMode = isset($iframeMode) ? $iframeMode : false; ?>

<aside class="sidebar">

<fieldset>

<legend>

<b>Dashboard</b>

</legend>

<ul>

<li>

<a href="index.php?page=dashboard">

Dashboard

</a>

</li>

<li>

<a href="index.php?page=parts<?php echo $iframeMode ? '&embed=1' : ''; ?>"
   <?php echo $iframeMode ? 'target="contentFrame"' : ''; ?>>

My Parts

</a>

</li>

<li>

<a href="index.php?page=addPart<?php echo $iframeMode ? '&embed=1' : ''; ?>"
   <?php echo $iframeMode ? 'target="contentFrame"' : ''; ?>>

Add New Part

</a>

</li>

<li>

<a href="index.php?page=requests<?php echo $iframeMode ? '&embed=1' : ''; ?>"
   <?php echo $iframeMode ? 'target="contentFrame"' : ''; ?>>

Part Requests

</a>

</li>

<li>

<a href="index.php?page=income<?php echo $iframeMode ? '&embed=1' : ''; ?>"
   <?php echo $iframeMode ? 'target="contentFrame"' : ''; ?>>

Income

</a>

</li>

<li>

<a href="index.php?page=profile<?php echo $iframeMode ? '&embed=1' : ''; ?>"
   <?php echo $iframeMode ? 'target="contentFrame"' : ''; ?>>

My Profile

</a>

</li>

<li>

<a href="../Logout.php">

Logout

</a>

</li>

</ul>

</fieldset>

</aside>
