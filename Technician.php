<?php
session_start();

if(!isset($_SESSION["userid_email"])) {
    header("Location: login.php");
    exit();
}

include("db.php"); 

$user_email = $_SESSION["userid_email"];
$success_message = "";
$error_message = "";

if ($conn) {
    $safe_email = mysqli_real_escape_string($conn, $user_email);

    // Handle Profile Update Form Submission
    if (isset($_POST['update_profile'])) {
        $new_spec = mysqli_real_escape_string($conn, $_POST['specialization']);
        $new_exp = mysqli_real_escape_string($conn, $_POST['experience']);
        $new_avail = mysqli_real_escape_string($conn, $_POST['availability']);

        $update_sql = "UPDATE users SET specialization = '$new_spec', experience = '$new_exp', availability = '$new_avail' WHERE email = '$safe_email'";
        if (mysqli_query($conn, $update_sql)) {
            $success_message = "Profile successfully updated!";
        } else {
            $error_message = "Error updating profile: " . mysqli_error($conn);
        }
    }

    // Handle Certification Upload Form Submission
    if (isset($_POST['upload_cert'])) {
        $cert_title = mysqli_real_escape_string($conn, $_POST['cert_title']);
        
        if (isset($_FILES['cert_image']) && $_FILES['cert_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['cert_image']['tmp_name'];
            $file_name = time() . "_" . basename($_FILES['cert_image']['name']);
            $upload_dir = "uploads/";
            
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $target_path = $upload_dir . $file_name;
            if (move_uploaded_file($file_tmp, $target_path)) {
                $ins_cert = "INSERT INTO technician_certifications (technician_email, cert_title, cert_image) VALUES ('$safe_email', '$cert_title', '$target_path')";
                mysqli_query($conn, $ins_cert);
                $success_message = "Certification uploaded successfully!";
            } else {
                $error_message = "Failed to move uploaded certificate file.";
            }
        } else {
            $error_message = "Please select a valid certificate image.";
        }
    }

    // Fetch technician info from database
    $profile_query = mysqli_query($conn, "SELECT * FROM users WHERE email = '$safe_email' LIMIT 1");
    if ($profile_query && mysqli_num_rows($profile_query) > 0) {
        $user_data = mysqli_fetch_assoc($profile_query);
        $username = $user_data['name'] ?? $user_email;
        $specialization = $user_data['specialization'] ?? "Refrigeration, HVAC"; 
        $experience = $user_data['experience'] ?? "6 years";
        $availability = $user_data['availability'] ?? "Available now";
    } else {
        $username = $user_email;
        $specialization = "Refrigeration, HVAC"; 
        $experience = "6 years";
        $availability = "Available now";
    }

    // Count uploaded certifications
    $cert_count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM technician_certifications WHERE technician_email = '$safe_email'");
    $cert_row = mysqli_fetch_assoc($cert_count_query);
    $certifications_count = ($cert_row['total'] ?? 0) . " uploaded";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technician Portal - FixLink</title>
    <style>
        .tab-btn { background: none; border: none; cursor: pointer; text-align: left; width: 100%; font: inherit; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; font-size: 13px; color: #555; margin-bottom: 5px; }
        .form-group input { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
    </style>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; display: flex; justify-content: center;">

    <!-- Main Dashboard Container -->
    <div style="background-color: #fff; width: 100%; max-width: 950px; border: 1px solid #333; display: flex; flex-direction: column; min-height: 600px;">
        
        <!-- Top Navigation Bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #333;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <h2 style="margin: 0; font-size: 18px; font-weight: normal;">Technician Portal</h2>
            </div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="Logout.php" style="font-size: 12px; color: red; text-decoration: none;">Logout</a>
            </div>
        </div>

        <!-- Layout Split -->
        <div style="display: flex; flex: 1;">
            
            <!-- Sidebar -->
            <div style="width: 200px; border-right: 1px solid #333; padding: 20px; display: flex; flex-direction: column; gap: 15px;">
                <a href="Technician.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px; background-color: #f0f0f0;">
                    Profile ✓
                </a>
                <a href="Technician_parts.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                    Find Parts
                </a>
                <a href="Technician_repair.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                    Repair Jobs
                </a>
                <a href="Technician_equipment.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                    Equipment
                </a>
                <a href="Technician_messages.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                    Messages
                </a>
            </div>

            <!-- Main Content Area -->
            <div style="flex: 1; padding: 30px;">
                <h3 style="margin-top: 0; font-weight: normal; margin-bottom: 20px;">Professional Profile (<?php echo htmlspecialchars($username); ?>)</h3>
                
                <?php if(!empty($success_message)): ?>
                    <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 13px;"><?php echo htmlspecialchars($success_message); ?></div>
                <?php endif; ?>
                <?php if(!empty($error_message)): ?>
                    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 20px; font-size: 13px;"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <!-- Profile Data Grid View -->
                <div style="display: flex; flex-wrap: wrap; gap: 40px; margin-bottom: 30px; background: #fafafa; padding: 15px; border: 1px solid #ddd; border-radius: 6px;">
                    <div>
                        <p style="margin: 0 0 5px 0; color: #555; font-size: 13px;">Specialization</p>
                        <p style="margin: 0; font-size: 14px; font-weight: bold;"><?php echo htmlspecialchars($specialization); ?></p>
                    </div>
                    <div>
                        <p style="margin: 0 0 5px 0; color: #555; font-size: 13px;">Experience</p>
                        <p style="margin: 0; font-size: 14px; font-weight: bold;"><?php echo htmlspecialchars($experience); ?></p>
                    </div>
                    <div>
                        <p style="margin: 0 0 5px 0; color: #555; font-size: 13px;">Certifications</p>
                        <p style="margin: 0; font-size: 14px; font-weight: bold;"><?php echo htmlspecialchars($certifications_count); ?></p>
                    </div>
                    <div>
                        <p style="margin: 0 0 5px 0; color: #555; font-size: 13px;">Availability</p>
                        <p style="margin: 0; font-size: 14px; font-weight: bold;"><?php echo htmlspecialchars($availability); ?></p>
                    </div>
                </div>

                <!-- Forms Container for Updating Profile & Uploading Certificates -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                    
                    <!-- Update Profile Form -->
                    <div style="border: 1px solid #ccc; padding: 15px; border-radius: 6px; background: #fff;">
                        <h4 style="margin-top: 0; font-size: 15px; border-bottom: 1px solid #eee; padding-bottom: 8px;">Edit Profile Information</h4>
                        <form method="POST">
                            <div class="form-group">
                                <label>Specialization</label>
                                <input type="text" name="specialization" value="<?php echo htmlspecialchars($specialization); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Experience</label>
                                <input type="text" name="experience" value="<?php echo htmlspecialchars($experience); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Availability</label>
                                <input type="text" name="availability" value="<?php echo htmlspecialchars($availability); ?>" required>
                            </div>
                            <button type="submit" name="update_profile" style="background: #333; color: #fff; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 13px;">Save Changes</button>
                        </form>
                    </div>

                    <!-- Upload Certification Form -->
                    <div style="border: 1px solid #ccc; padding: 15px; border-radius: 6px; background: #fff;">
                        <h4 style="margin-top: 0; font-size: 15px; border-bottom: 1px solid #eee; padding-bottom: 8px;">Upload Certificate</h4>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="form-group">
                                <label>Certificate Title</label>
                                <input type="text" name="cert_title" placeholder="e.g. HVAC Advanced Master" required>
                            </div>
                            <div class="form-group">
                                <label>Certificate Image</label>
                                <input type="file" name="cert_image" accept="image/*" required style="padding: 5px;">
                            </div>
                            <button type="submit" name="upload_cert" style="background: #007bff; color: #fff; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; font-size: 13px;">Upload Certificate</button>
                        </form>
                    </div>

                </div>

                <!-- Active Repair Requests -->
                <h4 style="font-weight: normal; margin-bottom: 15px; color: #333;">Active repair requests</h4>
                <div style="border: 1px solid #333; border-radius: 5px;">
                    <?php
                    // Adjust this query based on how your repair requests map to technicians (via username or email)
                    $safe_username = mysqli_real_escape_string($conn, $username);
                    $repair_query = "SELECT * FROM repair_requests WHERE customer_name = '$safe_username' OR issue_description LIKE '%$safe_username%' ORDER BY id DESC";
                    $repair_result = $conn ? mysqli_query($conn, $repair_query) : false;

                    if ($repair_result && mysqli_num_rows($repair_result) > 0) {
                        $total_rows = mysqli_num_rows($repair_result);
                        $current_row = 0;
                        while($row = mysqli_fetch_assoc($repair_result)) {
                            $current_row++;
                            $border_style = ($current_row < $total_rows) ? "border-bottom: 1px solid #333;" : "";
                            $job_title = $row['equipment_name'] ?? 'Repair Task';
                            $status = $row['status'] ?? 'pending';
                            ?>
                            <div style="display: flex; justify-content: space-between; padding: 12px 15px; <?php echo $border_style; ?>">
                                <span style="font-size: 14px;"><?php echo htmlspecialchars($job_title); ?> (Issue: <?php echo htmlspecialchars($row['issue_description']); ?>)</span>
                                <span style="font-size: 12px; color: #555; text-transform: capitalize;"><?php echo htmlspecialchars($status); ?></span>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div style="padding: 15px; text-align: center; color: #555; font-size: 14px;">
                            No active repair requests found.
                        </div>
                        <?php
                    }
                    ?>
                </div>

            </div>
        </div>
    </div>

</body>
</html>