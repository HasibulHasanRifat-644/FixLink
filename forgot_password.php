<?php
session_start();
include("db.php");

$error = "";
$success = "";
$step = 1;
$verified_id = "";

// Handle Step 1: Verify User Details
if(isset($_POST['verify_user'])) {
    $id = trim($_POST['user_id'] ?? '');
    $name = trim($_POST['user_name'] ?? '');
    $role = trim($_POST['user_role'] ?? '');

    if(!empty($id) && !empty($name) && !empty($role)) {
        $sql = "SELECT * FROM users WHERE ID='$id' AND name='$name' AND role='$role'";
        $result = mysqli_query($conn, $sql);

        if($result && mysqli_num_rows($result) > 0) {
            $step = 2;
            $verified_id = $id;
        } else {
            $error = "Invalid ID, Name, or Role combination. Please try again.";
        }
    } else {
        $error = "All fields are required.";
    }
}

if(isset($_POST['update_password'])) {
    $id = trim($_POST['verified_id'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');

    if(!empty($new_password)) {
        $update_sql = "UPDATE users SET pass='$new_password' WHERE ID='$id'";
        if(mysqli_query($conn, $update_sql)) {
            $success = "Password updated successfully!";
            $step = 3; 
        } else {
            $error = "Error updating password. Please try again.";
            $step = 2;
            $verified_id = $id;
        }
    } else {
        $error = "Password cannot be empty.";
        $step = 2;
        $verified_id = $id;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; height: 100vh;">

    <div style="background-color: #fff; width: 100%; max-width: 450px; border: 1px solid #333; border-radius: 5px; padding: 30px; box-sizing: border-box;">
        
        <h2 style="margin-top: 0; font-size: 20px; text-align: center;">Forgot Password</h2>
        <hr style="border: 0; border-top: 1px solid #333; margin-bottom: 20px;">

        <?php if(!empty($error)) { ?>
            <p style="color: red; font-size: 14px; text-align: center; margin-bottom: 15px;"><?php echo $error; ?></p>
        <?php } ?>
        <?php if($step == 1) { ?>
            <form method="POST">
                <div style="margin-bottom: 15px;">
                    <label style="font-size: 14px; display: block; margin-bottom: 5px;">User ID:</label>
                    <input type="text" name="user_id" placeholder="Enter your ID" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label style="font-size: 14px; display: block; margin-bottom: 5px;">Name:</label>
                    <input type="text" name="user_name" placeholder="Enter your name" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;" required>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="font-size: 14px; display: block; margin-bottom: 5px;">Role:</label>
                    <select name="user_role" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; background-color: #fff;" required>
                        <option value="">Select Role</option>
                        <option value="customer">Customer</option>
                        <option value="technician">Technician</option>
                        <option value="supplier">Supplier</option>
                        <option value="equipment">Equipment Owner</option>
                    </select>
                </div>

                <button type="submit" name="verify_user" style="width: 100%; padding: 10px; border: 1px solid #333; background-color: #fff; cursor: pointer; border-radius: 4px; font-weight: bold;">Verify Details</button>
            </form>
        <?php } ?>
        <?php if($step == 2) { ?>
            <form method="POST">
                <p style="color: green; font-size: 14px; margin-top: 0;">User verified successfully! Please enter your new password.</p>
                <input type="hidden" name="verified_id" value="<?php echo htmlspecialchars($verified_id); ?>">
                
                <div style="margin-bottom: 15px;">
                    <input type="password" name="new_password" placeholder="Enter new password" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;" required>
                </div>
                
                <button type="submit" name="update_password" style="width: 100%; padding: 10px; border: 1px solid #333; background-color: #fff; cursor: pointer; border-radius: 4px; font-weight: bold;">Update Password</button>
            </form>
        <?php } ?>
        <?php if($step == 3) { ?>
            <div style="text-align: center;">
                <p style="color: green; font-size: 14px;"><?php echo $success; ?></p>
                <a href="login.php" style="display: inline-block; margin-top: 10px; padding: 8px 20px; border: 1px solid #333; background-color: #fff; color: #000; text-decoration: none; border-radius: 4px;">Proceed to Login</a>
            </div>
        <?php } ?>

        <?php if($step != 3) { ?>
            <div style="text-align: center; margin-top: 20px;">
                <a href="login.php" style="font-size: 12px; color: #000; text-decoration: underline;">Back to Login</a>
            </div>
        <?php } ?>

    </div>

</body>
</html>