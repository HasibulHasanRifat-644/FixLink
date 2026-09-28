<?php

/*
 * This file bridges the shared team login (Login.php, which sets
 * $_SESSION["userid_email"] and $_SESSION["role"]) into the
 * $_SESSION["id"] / $_SESSION["username"] keys that every Model and
 * Controller in this project already uses.
 *
 * index.php requires this AFTER connecting to the database (so $conn
 * already exists) and BEFORE loading any Models/Controllers.
 */

if(!isset($_SESSION["userid_email"]) || !isset($_SESSION["role"]))
{
    header("Location: ../Login.php");
    exit();
}

if($_SESSION["role"] !== "supplier")
{
    // Logged in, but as the wrong role -- send back to the shared login.
    header("Location: ../Login.php");
    exit();
}

// CHANGED: re-resolve whenever the logged-in identity has changed, not
// just when $_SESSION["id"] happens to be unset. Without this, switching
// accounts (logging in as a different user without fully logging out
// first) would keep showing the PREVIOUS user's cached id/username,
// since Login.php only updates userid_email/role, not our custom keys.
if(!isset($_SESSION["bridged_identifier"]) || $_SESSION["bridged_identifier"] !== $_SESSION["userid_email"])
{
    $identifier = $_SESSION["userid_email"];

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE (email = ? OR ID = ?) AND role = 'supplier'");
    mysqli_stmt_bind_param($stmt, "ss", $identifier, $identifier);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if(!$row)
    {
        header("Location: ../Login.php");
        exit();
    }

    $_SESSION["id"] = $row["ID"];
    $_SESSION["username"] = $row["name"];
    $_SESSION["bridged_identifier"] = $identifier;
}

?>
