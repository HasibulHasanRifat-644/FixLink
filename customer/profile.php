<?php
session_start();
include('db.php');
 
 
$user_id = $_SESSION["id"] ?? 1;
$message = "";
 
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
 
    if (strlen($password) < 6) {
        $message = "<div class='alert error'>Password must be at least 6 characters long.</div>";
    } else {
      
        $sql = "UPDATE users SET name='$name', phone='$phone', password='$password' WHERE id=$user_id";
        
        if (mysqli_query($conn, $sql)) {
            $_SESSION["username"] = $name;
            $message = "<div class='alert success'>Profile updated successfully!</div>";
        } else {
            $message = "<div class='alert error'>Failed to update profile.</div>";
        }
    }
}
 
$result = mysqli_query($conn, "SELECT * FROM users WHERE id=$user_id");
$user_data = ($result && mysqli_num_rows($result) > 0) ? mysqli_fetch_assoc($result) : [];
?>
 
<!DOCTYPE html>
<html>
<head>
    <title>My Profile - FixLink</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f7f9;
            margin: 0;
            padding: 40px 20px;
        }
        h1 {
            color: #1e3a8a;
            text-align: center;
            margin-bottom: 25px;
            font-size: 26px;
        }
        .form-box {
            width: 440px;
            margin: auto;
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }
        .alert {
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .alert.success {
            background-color: #e6f7ec;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert.error {
            background-color: #fce8e6;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        label {
            font-weight: 600;
            color: #334155;
            display: block;
            margin-top: 14px;
            font-size: 14px;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            box-sizing: border-box;
            padding: 10px 12px;
            margin-top: 6px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
        }
        input:focus {
            border-color: #2563eb;
        }
        .btn-container {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 22px;
        }
        .btn-update {
            flex: 1;
            background-color: #0d9488;
            color: white;
            border: none;
            padding: 10px 0;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        .btn-update:hover {
            background-color: #0f766e;
        }
        .btn-back {
            background-color: #64748b;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
        }
        .btn-back:hover {
            background-color: #475569;
        }
    </style>
</head>
<body>
 
    <h1>Manage Profile</h1>
 
    <div class="form-box">
        <?php echo $message; ?>
 
        <form method="POST" autocomplete="off">
            <label>Full Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user_data['name'] ?? ''); ?>" required>
 
            <label>Email Address (Read Only):</label>
            <input type="email" value="<?php echo htmlspecialchars($user_data['email'] ?? ''); ?>" disabled style="background-color: #f1f5f9; color: #64748b;">
 
            <label>Phone Number:</label>
            <input type="text" name="phone" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>" required>
 
            <label>Default Address / City:</label>
            <input type="text" name="address" placeholder="e.g. Dhanmondi, Dhaka" value="<?php echo htmlspecialchars($user_data['address'] ?? ''); ?>">
 
            <label>Password (Min 6 Characters):</label>
            <input type="password" name="password" value="<?php echo htmlspecialchars($user_data['password'] ?? ''); ?>" minlength="6" autocomplete="new-password" required>
 
            <div class="btn-container">
                <input type="submit" name="update_profile" value="Update Profile" class="btn-update">
                <a href="customerdashboard.php" class="btn-back">Back</a>
            </div>
        </form>
    </div>
 
    <p align="center" style="color: #64748b; font-size: 13px; margin-top: 30px;">
        Copyright &copy; <?php echo date("Y"); ?> FixLink
    </p>
 
</body>
</html>