<?php

class DashboardController
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function index()
    {
        // CHANGED: session_bridge.php already resolved the display name
        // from the shared `users` table (column `name`, not `username`)
        // and put it in $_SESSION["username"] -- no need to query again.
        $displayName = $_SESSION["username"];

        require __DIR__ . "/../views/dashboard.php";
    }
}
