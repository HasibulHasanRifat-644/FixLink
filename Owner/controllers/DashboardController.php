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
        require __DIR__ . "/../views/dashboard.php";
    }
}
