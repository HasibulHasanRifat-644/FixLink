<?php
/*
 * Replace with your real database name if it isn't "fixlink".
 * Using 127.0.0.1 instead of "localhost" avoids the XAMPP/Windows
 * named-pipe issue that causes "MySQL server has gone away".
 */

$conn = mysqli_connect("127.0.0.1", "root", "", "fixlink");

if (!$conn) {
    die("Connection Failed");
}
