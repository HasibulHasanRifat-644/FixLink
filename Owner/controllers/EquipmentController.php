<?php

class EquipmentController
{
    private $conn;
    private $equipmentModel;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->equipmentModel = new Equipment($conn);
    }

    public function list()
    {
        // ADDED: table body is now populated entirely via AJAX (search()
        // is called once on page load with an empty term, and again on
        // every keystroke). This view no longer needs $equipment.
        $embedded = isset($_GET["embed"]);

        require __DIR__ . "/../views/equipment/list.php";
    }

    // ADDED: AJAX endpoint. Returns JSON, not a view -- the JS in
    // views/equipment/list.php fetches this and rebuilds the table rows.
    public function search()
    {
        $ownerId = $_SESSION["id"];
        $term = isset($_GET["term"]) ? $_GET["term"] : "";

        $result = $this->equipmentModel->search($ownerId, $term);

        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        header("Content-Type: application/json");
        echo json_encode($rows);
        exit();
    }

    public function add()
    {
        $ownerId = $_SESSION["id"];
        $embedded = isset($_GET["embed"]);
        $mode = "add";

        $item = array(
            "id" => "",
            "equipment_name" => "",
            "category" => "",
            "brand" => "",
            "model" => "",
            "description" => "",
            "rental_price" => "",
        );

        if (isset($_POST["submit"])) {
            $this->equipmentModel->create($ownerId, $_POST);

            $redirect = "index.php?page=equipment" . ($embedded ? "&embed=1" : "");
            echo "<script>alert('Equipment Added Successfully');</script>";
            echo "<script>window.location='" . $redirect . "';</script>";
            exit();
        }

        require __DIR__ . "/../views/equipment/form.php";
    }

    public function edit()
    {
        $ownerId = $_SESSION["id"];
        $embedded = isset($_GET["embed"]);
        $id = (int) $_GET["id"];
        $mode = "edit";

        $item = $this->equipmentModel->find($id, $ownerId);

        if (!$item) {
            echo "Equipment not found.";
            exit();
        }

        if (isset($_POST["submit"])) {
            $this->equipmentModel->update($id, $ownerId, $_POST);

            $redirect = "index.php?page=equipment" . ($embedded ? "&embed=1" : "");
            echo "<script>alert('Equipment Updated Successfully');</script>";
            echo "<script>window.location='" . $redirect . "';</script>";
            exit();
        }

        require __DIR__ . "/../views/equipment/form.php";
    }

    public function delete()
    {
        $ownerId = $_SESSION["id"];
        $embedded = isset($_GET["embed"]);
        $id = (int) $_GET["id"];

        $this->equipmentModel->delete($id, $ownerId);

        $redirect = "index.php?page=equipment" . ($embedded ? "&embed=1" : "");
        echo "<script>alert('Equipment Deleted Successfully');</script>";
        echo "<script>window.location='" . $redirect . "';</script>";
        exit();
    }
}
