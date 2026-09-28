<?php
session_start();

if (!isset($_SESSION["userid_email"])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/Applications/XAMPP/xamppfiles/htdocs/FixLink/models/db.php";
require_once __DIR__ . "/Applications/XAMPP/xamppfiles/htdocs/FixLink/models/Technician.php";

$user_email = $_SESSION["userid_email"];
$technicianModel = new Technician($conn);

$success_message = "";
$error_message = "";

// Handle Form Submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_profile'])) {
        $new_spec = $_POST['specialization'];
        $new_exp = $_POST['experience'];
        $new_avail = $_POST['availability'];

        if ($technicianModel->updateProfile($user_email, $new_spec, $new_exp, $new_avail)) {
            $success_message = "Profile successfully updated!";
        } else {
            $error_message = "Error updating profile.";
        }
    }

    if (isset($_POST['upload_cert'])) {
        $cert_title = $_POST['cert_title'];
        
        if (isset($_FILES['cert_image']) && $_FILES['cert_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['cert_image']['tmp_name'];
            $file_name = time() . "_" . basename($_FILES['cert_image']['name']);
            $upload_dir = __DIR__ . "/../uploads/";
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $target_path_relative = "uploads/" . $file_name;
            $target_path_absolute = $upload_dir . $file_name;

            if (move_uploaded_file($file_tmp, $target_path_absolute)) {
                $technicianModel->uploadCertificate($user_email, $cert_title, $target_path_relative);
                $success_message = "Certification uploaded successfully!";
            } else {
                $error_message = "Failed to move uploaded certificate file.";
            }
        } else {
            $error_message = "Please select a valid certificate image.";
        }
    }
}

// Fetch technician info
$user_data = $technicianModel->findByEmail($user_email);
$username = $user_data['name'] ?? $user_email;
$specialization = $user_data['specialization'] ?? "Refrigeration, HVAC"; 
$experience = $user_data['experience'] ?? "6 years";
$availability = $user_data['availability'] ?? "Available now";

$cert_count = $technicianModel->getCertificateCount($user_email);
$certifications_count = $cert_count . " uploaded";

// Fetch repair requests
$repair_result = $technicianModel->getActiveRepairRequests($username);

// Load View file
require_once __DIR__ . "/../views/technician_profile.php";
?>