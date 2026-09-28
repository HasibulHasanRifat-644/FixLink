<?php

class IncomeController
{
    private $conn;
    private $incomeModel;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->incomeModel = new IncomeReport($conn);
    }

    public function index()
    {
        $ownerId = $_SESSION["id"];
        $embedded = isset($_GET["embed"]);

        $summary = $this->incomeModel->getSummary($ownerId);

        require __DIR__ . "/../views/income/index.php";
    }
}
