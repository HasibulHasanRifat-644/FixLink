<?php

/*
 * This is the file the shared Login.php redirects to after a successful
 * login with role = 'customer'.
 *
 * The Customer group's pages (part_request.php, profile.php, etc.) all
 * expect $_SESSION["id"] to be a real row id in the SEPARATE `customer`
 * database's own `users` table (their db.php connects to "customer",
 * not "fixlink"). So this file:
 *   1. Confirms the person actually logged in as role=customer
 *      (via the shared fixlink login)
 *   2. Finds or creates a matching row in the customer database by email
 *   3. Sets $_SESSION["id"] / $_SESSION["username"] to that row
 *   4. Hands off to their dashboard -- none of their files need to change
 */

session_start();

if(!isset($_SESSION["userid_email"]) || !isset($_SESSION["role"]))
{
    header("Location: Login.php");
    exit();
}

if($_SESSION["role"] !== "customer")
{
    header("Location: Login.php");
    exit();
}

// CHANGED: re-resolve whenever the logged-in identity has changed, so
// switching accounts without a full logout doesn't show stale data --
// see Supplier's session_bridge.php for the full explanation.
if(!isset($_SESSION["bridged_identifier"]) || $_SESSION["bridged_identifier"] !== $_SESSION["userid_email"])
{
    $identifier = $_SESSION["userid_email"];

    // Step 1: confirm the login and get the real display name from the
    // shared fixlink users table.
    $fixlinkConn = mysqli_connect("127.0.0.1", "root", "", "fixlink");

    $stmt = mysqli_prepare($fixlinkConn, "SELECT name FROM users WHERE (email = ? OR ID = ?) AND role = 'customer'");
    mysqli_stmt_bind_param($stmt, "ss", $identifier, $identifier);
    mysqli_stmt_execute($stmt);
    $fixlinkRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    mysqli_close($fixlinkConn);

    if(!$fixlinkRow)
    {
        header("Location: Login.php");
        exit();
    }

    $displayName = $fixlinkRow["name"];

    // Step 2: find or create the matching row in the customer database.
    $customerConn = mysqli_connect("127.0.0.1", "root", "", "customer");

    $stmt2 = mysqli_prepare($customerConn, "SELECT * FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt2, "s", $identifier);
    mysqli_stmt_execute($stmt2);
    $custRow = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt2));

    if($custRow)
    {
        $_SESSION["id"] = $custRow["id"];
        $_SESSION["username"] = $custRow["name"];
    }
    else
    {
        $stmt3 = mysqli_prepare($customerConn, "INSERT INTO users (name, email, password, role) VALUES (?, ?, '', 'customer')");
        mysqli_stmt_bind_param($stmt3, "ss", $displayName, $identifier);
        mysqli_stmt_execute($stmt3);

        $_SESSION["id"] = mysqli_insert_id($customerConn);
        $_SESSION["username"] = $displayName;
    }

    $_SESSION["bridged_identifier"] = $identifier;

    mysqli_close($customerConn);
}

header("Location: customer/customerdashboard.php");
exit();

?>
