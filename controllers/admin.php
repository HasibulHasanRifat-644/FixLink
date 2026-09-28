<?php

session_start();

if (!isset($_SESSION["userid_email"])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/db.php";
require_once __DIR__ . "/../models/AdminModel.php";

$adminModel = new AdminModel($conn);

$total_users = $adminModel->getTotalUsers();
$pending_verifications = $adminModel->getPendingVerifications();
$open_disputes = $adminModel->getOpenDisputes();
$active_listings = $adminModel->getActiveListings();
$result_reports = $adminModel->getRecentComplaints();

require __DIR__ . "/../views/admin.php";

?>