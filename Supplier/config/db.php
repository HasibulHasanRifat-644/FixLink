<?php
/*
 * IMPORTANT: This is a placeholder.
 * Copy the contents of your EXISTING db.php (the one your old files
 * used with include("db.php")) into this file, unchanged. It just needs
 * to end up setting a $conn variable (a mysqli connection).
 *
 * Example of what it probably looks like:
 */

$conn = mysqli_connect("localhost", "root", "", "fixlink");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
