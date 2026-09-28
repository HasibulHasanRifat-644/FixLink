<?php

/*
 * This is the file Login.php redirects to after a successful login
 * with role = 'equipment'. It just hands off into the Owner MVC app.
 */

header("Location: Owner/index.php?page=dashboard");
exit();

?>
