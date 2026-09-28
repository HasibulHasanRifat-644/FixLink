<?php
session_start();
include("db.php");

if(!isset($_SESSION["userid_email"])) {
    header("Location: login.php");
    exit();
}

$message = "";
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $site_name = mysqli_real_escape_string($conn, $_POST['site_name']);
    $support_email = mysqli_real_escape_string($conn, $_POST['support_email']);
    $require_verify = mysqli_real_escape_string($conn, $_POST['require_verify']);

    $update_sql = "UPDATE settings SET site_name='$site_name', support_email='$support_email', require_verify='$require_verify' WHERE id=1";
    
    if(mysqli_query($conn, $update_sql)) {
        $message = "Settings saved successfully!";
    } else {
        $message = "Error saving settings: " . mysqli_error($conn);
    }
}


$query = "SELECT * FROM settings WHERE id=1";
$result = mysqli_query($conn, $query);
$settings = mysqli_fetch_assoc($result);

$site_name = $settings['site_name'] ?? 'FixLink';
$support_email = $settings['support_email'] ?? 'support@fixlink.com';
$require_verify = $settings['require_verify'] ?? 'on';
?>
<!DOCTYPE html>
<head>
    <title>Admin - Settings</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; display: flex; justify-content: center;">

    <div style="background-color: #fff; width: 100%; max-width: 900px; border: 1px solid #333; display: flex; flex-direction: column; min-height: 600px;">
        
      
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #333;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <h2 style="margin: 0; font-size: 18px; font-weight: normal;">Admin Console</h2>
            </div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="logout.php" style="font-size: 12px; color: red; text-decoration: none;">Logout</a>
            </div>
        </div>

        <div style="display: flex; flex: 1;">

            <div style="width: 200px; border-right: 1px solid #333; padding: 20px; display: flex; flex-direction: column; gap: 15px;">
                <a href="Admin.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                   Overview
                </a>
                <a href="admin_users.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                  Users
                </a>
                <a href="admin_catalog.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
       Catalog
                </a>
                <a href="admin_activity.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                  Activity
                </a>
                <a href="admin_moderation.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                    Moderation
                </a>
                <a href="admin_settings.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px; background-color: #f0f0f0;">
                 Settings
                </a>
            </div>

            
            <div style="flex: 1; padding: 30px;">
                
                <h3 style="margin-top: 0; font-weight: normal; margin-bottom: 25px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 10px;">Settings</h3>
                <h4 style="font-weight: normal; margin-bottom: 20px; color: #555;">Website settings</h4>

                <?php if(!empty($message)) echo "<p style='color: green; font-size: 14px; margin-bottom: 20px;'>$message</p>"; ?>

                <form method="POST" action="">
                    
               
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #333;">Site name</label>
                        <input type="text" name="site_name" value="<?php echo htmlspecialchars($site_name); ?>" style="width: 100%; max-width: 400px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                    </div>

                
                    <div style="margin-bottom: 20px;">
                        <label style="display: block; margin-bottom: 8px; font-size: 14px; color: #333;">Support email</label>
                        <input type="email" name="support_email" value="<?php echo htmlspecialchars($support_email); ?>" style="width: 100%; max-width: 400px; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                    </div>

                    
                    <div style="margin-bottom: 30px;">
                        <label style="display: block; margin-bottom: 10px; font-size: 14px; color: #333;">Require admin verification for new register</label>
                        
                        <div style="display: flex; gap: 15px; align-items: center;">
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 5px; font-size: 14px;">
                                <input type="radio" name="require_verify" value="on" <?php echo ($require_verify == 'on') ? 'checked' : ''; ?>> 
                                <span style="border: 1px solid #333; padding: 4px 12px; border-radius: 3px;">ON</span>
                            </label>
                            
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 5px; font-size: 14px;">
                                <input type="radio" name="require_verify" value="off" <?php echo ($require_verify == 'off') ? 'checked' : ''; ?>> 
                                <span style="border: 1px solid #ccc; padding: 4px 12px; border-radius: 3px;">OFF</span>
                            </label>
                        </div>
                    </div>

                   
                    <div>
                        <button type="submit" style="padding: 10px 25px; border: 1px solid #333; background-color: #fff; cursor: pointer; font-size: 14px; border-radius: 4px;">Save changes</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</body>
</html>