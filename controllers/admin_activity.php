<?php

session_start();

if (!isset($_SESSION["userid_email"])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/../models/ActivityModel.php";

$activityModel = new ActivityModel($conn);

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'part_requests';
$prefix = "";

switch ($active_tab) {
    case 'part_requests':
        $prefix = "requested: ";
        break;
    case 'repair_requests':
        $prefix = "repair: ";
        break;
    case 'rentals':
        $prefix = "rental: ";
        break;
    case 'orders':
        $prefix = "order: ";
        break;
    default:
        $prefix = "requested: ";
        $active_tab = 'part_requests'; 
}

$result = $activityModel->getActivityData($active_tab);

require __DIR__ . "/../views/admin_activity.php";

?>