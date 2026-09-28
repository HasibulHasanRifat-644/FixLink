<?php

class ProfileController
{
    private $conn;
    private $profileModel;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->profileModel = new UserProfile($conn);
    }

    public function index()
    {
        $id = $_SESSION["id"];
        $embedded = isset($_GET["embed"]);
        $saved = false;

        if(isset($_POST["submit"]))
        {
            $this->profileModel->update($id, $_POST);

            // Keep the header's "Welcome, ..." text in sync if the name changed
            $_SESSION["username"] = $_POST["name"];

            $saved = true;
        }

        $profile = $this->profileModel->find($id);

        require __DIR__ . "/../views/profile.php";
    }
}
