<?php

class RentalRequestController
{
    private $conn;
    private $requestModel;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->requestModel = new RentalRequest($conn);
    }

    public function list()
    {
        $ownerId = $_SESSION["id"];
        $embedded = isset($_GET["embed"]);

        $requests = $this->requestModel->getAllByOwner($ownerId);

        require __DIR__ . "/../views/requests/list.php";
    }

    public function updateStatus()
    {
        $ownerId = $_SESSION["id"];
        $embedded = isset($_GET["embed"]);
        $id = (int) $_GET["id"];
        $status = $_GET["status"];

        $this->requestModel->updateStatus($id, $ownerId, $status);

        $redirect = "index.php?page=requests" . ($embedded ? "&embed=1" : "");
        echo "<script>alert('Rental Request Updated Successfully');</script>";
        echo "<script>window.location='" . $redirect . "';</script>";
        exit();
    }
}
