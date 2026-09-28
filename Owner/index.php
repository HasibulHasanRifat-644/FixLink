<?php

session_start();

require __DIR__ . "/config/db.php";

// CHANGED: bridges the shared team login into $_SESSION["id"]
require __DIR__ . "/session_bridge.php";

require __DIR__ . "/models/Equipment.php";
require __DIR__ . "/models/RentalRequest.php";
require __DIR__ . "/models/IncomeReport.php";
require __DIR__ . "/models/UserProfile.php";

require __DIR__ . "/controllers/DashboardController.php";
require __DIR__ . "/controllers/EquipmentController.php";
require __DIR__ . "/controllers/RentalRequestController.php";
require __DIR__ . "/controllers/IncomeController.php";
require __DIR__ . "/controllers/ProfileController.php";

$page = isset($_GET["page"]) ? $_GET["page"] : "dashboard";

switch ($page) {

    case "dashboard":
        $controller = new DashboardController($conn);
        $controller->index();
        break;

    case "equipment":
        $controller = new EquipmentController($conn);
        $controller->list();
        break;

    // ADDED: AJAX search endpoint for the My Equipment live search box
    case "searchEquipment":
        $controller = new EquipmentController($conn);
        $controller->search();
        break;

    case "addEquipment":
        $controller = new EquipmentController($conn);
        $controller->add();
        break;

    case "editEquipment":
        $controller = new EquipmentController($conn);
        $controller->edit();
        break;

    case "deleteEquipment":
        $controller = new EquipmentController($conn);
        $controller->delete();
        break;

    case "requests":
        $controller = new RentalRequestController($conn);
        $controller->list();
        break;

    case "updateRequest":
        $controller = new RentalRequestController($conn);
        $controller->updateStatus();
        break;

    case "income":
        $controller = new IncomeController($conn);
        $controller->index();
        break;

    // ADDED: My Profile
    case "profile":
        $controller = new ProfileController($conn);
        $controller->index();
        break;

    default:
        http_response_code(404);
        echo "Page not found.";
        break;
}
