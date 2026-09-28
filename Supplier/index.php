<?php

session_start();

require __DIR__ . "/config/db.php";

// CHANGED: bridges the shared team login (Login.php) into $_SESSION["id"]
// instead of checking for it directly -- see session_bridge.php for why.
require __DIR__ . "/session_bridge.php";

require __DIR__ . "/models/Part.php";
require __DIR__ . "/models/PartRequest.php";
require __DIR__ . "/models/IncomeReport.php";
require __DIR__ . "/models/User.php";
require __DIR__ . "/models/UserProfile.php";

require __DIR__ . "/controllers/DashboardController.php";
require __DIR__ . "/controllers/PartController.php";
require __DIR__ . "/controllers/RequestController.php";
require __DIR__ . "/controllers/IncomeController.php";
require __DIR__ . "/controllers/ProfileController.php";

$page = isset($_GET["page"]) ? $_GET["page"] : "dashboard";

switch ($page) {

    case "dashboard":
        $controller = new DashboardController($conn);
        $controller->index();
        break;

    case "parts":
        $controller = new PartController($conn);
        $controller->list();
        break;

    // ADDED: AJAX search endpoint for the My Parts live search box
    case "searchParts":
        $controller = new PartController($conn);
        $controller->search();
        break;

    case "addPart":
        $controller = new PartController($conn);
        $controller->add();
        break;

    case "editPart":
        $controller = new PartController($conn);
        $controller->edit();
        break;

    case "deletePart":
        $controller = new PartController($conn);
        $controller->delete();
        break;

    case "requests":
        $controller = new RequestController($conn);
        $controller->list();
        break;

    case "updateRequest":
        $controller = new RequestController($conn);
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
