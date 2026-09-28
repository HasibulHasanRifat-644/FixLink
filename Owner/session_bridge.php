<?php

/*
 * Bridges the shared team login (Login.php sets $_SESSION["userid_email"]
 * and $_SESSION["role"]) into $_SESSION["id"] / $_SESSION["username"],
 * which every Model and Controller in this project already uses.
 *
 * Their Login.php sends role="equipment" users here (not "owner") --
 * see the elseif chain in their Login.php.
 */

if(!isset($_SESSION["userid_email"]) || !isset($_SESSION["role"]))
{
    header("Location: ../Login.php");
    exit();
}

if($_SESSION["role"] !== "equipment")
{
    header("Location: ../Login.php");
    exit();
}

// CHANGED: re-resolve whenever the logged-in identity has changed --
// see the Supplier session_bridge.php for the full explanation.
if(!isset($_SESSION["bridged_identifier"]) || $_SESSION["bridged_identifier"] !== $_SESSION["userid_email"])
{
    $identifier = $_SESSION["userid_email"];

    $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE (email = ? OR ID = ?) AND role = 'equipment'");
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
