<?php
session_start();

if(!isset($_SESSION["userid_email"])) {
    header("Location: login.php");
    exit();
}

include("db.php");

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'part_requests';
$sql = "";
switch ($active_tab) {
    case 'part_requests':
        $sql = "SELECT id, part_name AS display_text, status FROM part_requests ORDER BY created_at DESC";
        $prefix = "requested: ";
        break;
    case 'repair_requests':
        $sql = "SELECT id, equipment_name AS display_text, status FROM repair_requests ORDER BY created_at DESC";
        $prefix = "repair: ";
        break;
    case 'rentals':
        $sql = "SELECT id, equipment_name AS display_text, status FROM rentals ORDER BY created_at DESC";
        $prefix = "rental: ";
        break;
    case 'orders':
        $sql = "SELECT id, item_details AS display_text, status FROM orders ORDER BY created_at DESC";
        $prefix = "order: ";
        break;
    default:
        $sql = "SELECT id, part_name AS display_text, status FROM part_requests ORDER BY created_at DESC";
        $prefix = "requested: ";
        $active_tab = 'part_requests';
}

$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<head>
    <title>Admin - Activity</title>
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
                <a href="admin_activity.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px; background-color: #f0f0f0;">
                    Activity
                </a>
                <a href="admin_moderation.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
           Moderation
                </a>
                <a href="admin_settings.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                    Settings
                </a>
            </div>

            <div style="flex: 1; padding: 30px;">
                
                <div style="display: flex; gap: 10px; margin-bottom: 25px;">
                    <a href="?tab=part_requests" style="text-decoration: none; color: #000; padding: 8px 16px; border: 1px solid <?php echo ($active_tab == 'part_requests') ? '#333' : '#ccc'; ?>; background-color: <?php echo ($active_tab == 'part_requests') ? '#eee' : '#fff'; ?>; border-radius: 5px; font-size: 14px;">
                        Part requests <?php echo ($active_tab == 'part_requests') ? '✓' : ''; ?>
                    </a>
                    
                    <a href="?tab=repair_requests" style="text-decoration: none; color: #000; padding: 8px 16px; border: 1px solid <?php echo ($active_tab == 'repair_requests') ? '#333' : '#ccc'; ?>; background-color: <?php echo ($active_tab == 'repair_requests') ? '#eee' : '#fff'; ?>; border-radius: 5px; font-size: 14px;">
                        Repair requests <?php echo ($active_tab == 'repair_requests') ? '✓' : ''; ?>
                    </a>
                    
                    <a href="?tab=rentals" style="text-decoration: none; color: #000; padding: 8px 16px; border: 1px solid <?php echo ($active_tab == 'rentals') ? '#333' : '#ccc'; ?>; background-color: <?php echo ($active_tab == 'rentals') ? '#eee' : '#fff'; ?>; border-radius: 5px; font-size: 14px;">
                        Rentals <?php echo ($active_tab == 'rentals') ? '✓' : ''; ?>
                    </a>

                    <a href="?tab=orders" style="text-decoration: none; color: #000; padding: 8px 16px; border: 1px solid <?php echo ($active_tab == 'orders') ? '#333' : '#ccc'; ?>; background-color: <?php echo ($active_tab == 'orders') ? '#eee' : '#fff'; ?>; border-radius: 5px; font-size: 14px;">
                        Orders <?php echo ($active_tab == 'orders') ? '✓' : ''; ?>
                    </a>
                </div>

                <div style="border: 1px solid #333; border-radius: 5px;">
                    <?php
                  
                    if ($result && mysqli_num_rows($result) > 0) {
                        $count = mysqli_num_rows($result);
                        $current = 0;

                        while ($row = mysqli_fetch_assoc($result)) {
                            $current++;
                           
                            $border_style = ($current < $count) ? "border-bottom: 1px solid #333;" : "";
                            ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; <?php echo $border_style; ?>">
                                <span style="font-size: 14px;">ID <?php echo htmlspecialchars($row['id']); ?> - <?php echo $prefix . htmlspecialchars($row['display_text']); ?></span>
                                <span style="font-size: 14px; color: #555; text-transform: lowercase;">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </div>
                            <?php
                        }
                    } else {
                        
                        ?>
                        <div style="padding: 15px; text-align: center; color: #777; font-size: 14px;">
                            No data found for this category.
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