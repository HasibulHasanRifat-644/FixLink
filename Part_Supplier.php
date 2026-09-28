<?php

/*
 * This is the file Login.php redirects to after a successful login
 * with role = 'supplier'. It just hands off into the Supplier MVC app.
 * All the real logic lives in Supplier/index.php and session_bridge.php.
 */

header("Location: Supplier/index.php?page=dashboard");
exit();

?>
