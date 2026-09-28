<?php

class PartController
{
    private $conn;
    private $partModel;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->partModel = new Part($conn);
    }

    public function list()
    {
        // ADDED: the table body is now populated entirely by JavaScript
        // (via search(), called once on page load with an empty term, and
        // again on every keystroke). This view no longer needs $parts.
        $embedded = isset($_GET["embed"]);

        require __DIR__ . "/../views/parts/list.php";
    }

    // ADDED: AJAX endpoint. Returns JSON, not a view -- the JS in
    // views/parts/list.php fetches this and rebuilds the table rows.
    public function search()
    {
        $supplierId = $_SESSION["id"];
        $term = isset($_GET["term"]) ? $_GET["term"] : "";

        $result = $this->partModel->search($supplierId, $term);

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
        $supplierId = $_SESSION["id"];
        $embedded = isset($_GET["embed"]);
        $mode = "add";

        // Empty defaults so the shared form view has something to read
        $part = array(
            "id" => "",
            "part_name" => "",
            "category" => "",
            "description" => "",
            "price" => "",
            "security_deposit" => 0,
            "rental_duration_days" => 1,
            "stock" => "",
        );

        if (isset($_POST["submit"])) {
            $this->partModel->create($supplierId, $_POST);

            $redirect = "index.php?page=parts" . ($embedded ? "&embed=1" : "");
            echo "<script>alert('Part Added Successfully');</script>";
            echo "<script>window.location='" . $redirect . "';</script>";
            exit();
        }

        require __DIR__ . "/../views/parts/form.php";
    }

    public function edit()
    {
        $supplierId = $_SESSION["id"];
        $embedded = isset($_GET["embed"]);
        $id = (int) $_GET["id"];
        $mode = "edit";

        $part = $this->partModel->find($id, $supplierId);

        if (!$part) {
            echo "Part not found.";
            exit();
        }

        if (isset($_POST["submit"])) {
            $this->partModel->update($id, $supplierId, $_POST);

            $redirect = "index.php?page=parts" . ($embedded ? "&embed=1" : "");
            echo "<script>alert('Part Updated Successfully');</script>";
            echo "<script>window.location='" . $redirect . "';</script>";
            exit();
        }

        require __DIR__ . "/../views/parts/form.php";
    }

    public function delete()
    {
        $supplierId = $_SESSION["id"];
        $embedded = isset($_GET["embed"]);
        $id = (int) $_GET["id"];

        $this->partModel->delete($id, $supplierId);

        $redirect = "index.php?page=parts" . ($embedded ? "&embed=1" : "");
        echo "<script>alert('Part Deleted Successfully');</script>";
        echo "<script>window.location='" . $redirect . "';</script>";
        exit();
    }
}
