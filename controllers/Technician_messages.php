<?php
session_start();

if (!isset($_SESSION["userid_email"])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../db.php";
require_once __DIR__ . "/../models/Technician.php";

$user_email = $_SESSION["userid_email"];
$technicianModel = new Technician($conn);

// Fetch current technician details
$user_data = $technicianModel->findByEmail($user_email);
$tech_id = $user_data['id'] ?? ($user_data['ID'] ?? 1);

// Handle sending a message via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    $receiver_id = intval($_POST['receiver_id']);
    $message_text = $_POST['message_text'];

    if ($technicianModel->sendMessage($tech_id, $receiver_id, $message_text)) {
        header("Location: TechnicianMessagesController.php?customer_id=" . $receiver_id);
        exit();
    }
}

// Fetch unique customers associated with the system
$customers_query = $technicianModel->getAllCustomers();

// Get selected customer ID to chat with
$selected_cust_id = isset($_GET['customer_id']) ? intval($_GET['customer_id']) : 0;
$selected_cust_name = $technicianModel->getCustomerNameById($selected_cust_id);

// Fetch chat messages if a customer is selected
$result_msg = null;
if ($selected_cust_id > 0) {
    $result_msg = $technicianModel->getMessages($tech_id, $selected_cust_id);
}

// Load View file
require_once __DIR__ . "/../views/technician_messages.php";
?>