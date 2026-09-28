<?php

class RequestController
{
    private $conn;
    private $requestModel;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->requestModel = new PartRequest($conn);
    }

    public function list()
    {
        $supplierId = $_SESSION["id"];
        $embedded = isset($_GET["embed"]);

        $requests = $this->requestModel->getAllBySupplier($supplierId);

        require __DIR__ . "/../views/requests/list.php";
    }

    public function updateStatus()
    {
        $supplierId = $_SESSION["id"];
        $embedded = isset($_GET["embed"]);
        $id = (int) $_GET["id"];
        $status = $_GET["status"];

        $this->requestModel->updateStatus($id, $supplierId, $status);

        $redirect = "index.php?page=requests" . ($embedded ? "&embed=1" : "");
        echo "<script>alert('Request Updated Successfully');</script>";
        echo "<script>window.location='" . $redirect . "';</script>";
        exit();
    }
}
