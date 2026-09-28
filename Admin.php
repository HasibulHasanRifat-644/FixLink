<?php
session_start();

if(!isset($_SESSION["userid_email"])) {
    header("Location: login.php");
    exit();
}
include("db.php"); 
$total_users = 0;
$pending_verifications = 0;
$open_disputes = 0;
$active_listings = 0;

if ($conn) {
    $res = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
    if ($res) { $total_users = mysqli_fetch_assoc($res)['count'] ?? 0; }

    $res = mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE status = 'pending'");
    if ($res) { $pending_verifications = mysqli_fetch_assoc($res)['count'] ?? 0; }

    $res = mysqli_query($conn, "SELECT COUNT(*) as count FROM disputes WHERE status = 'open'");
    if ($res) { $open_disputes = mysqli_fetch_assoc($res)['count'] ?? 0; }
    $res = mysqli_query($conn, "SELECT COUNT(*) as count FROM listings WHERE status = 'active'");
    if ($res) { $active_listings = mysqli_fetch_assoc($res)['count'] ?? 0; }
}
$result_reports = false;
if ($conn) {
    $query_reports = "SELECT *, 'Complaint' as type FROM complaints ORDER BY id DESC LIMIT 5";
    $result_reports = @mysqli_query($conn, $query_reports);
}
?>

<!DOCTYPE html>
<head>
    <title>Admin - Overview</title>
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
                <a href="Admin.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; background-color: #f0f0f0;">Overview</a>
                <a href="admin_users.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center;">Users</a>
                <a href="admin_catalog.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center;">Catalog</a>
                <a href="admin_activity.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center;">Activity</a>
                <a href="admin_moderation.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center;">Moderation</a>
                <a href="admin_settings.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center;">Settings</a>
            </div>
            <div style="flex: 1; padding: 30px;">
                
                <h3 style="margin-top: 0; margin-bottom: 25px; font-size: 20px; font-weight: normal;">Dashboard Overview</h3>
                <div style="display: flex; gap: 15px; margin-bottom: 35px; flex-wrap: wrap;">
                    
                    <div style="flex: 1; min-width: 130px; border: 1px solid #333; border-radius: 5px; padding: 20px; text-align: center; background-color: #fafafa;">
                        <div style="font-size: 12px; color: #555; text-transform: uppercase; margin-bottom: 8px;">Total Users</div>
                        <div style="font-size: 24px; font-weight: bold;"><?php echo number_format($total_users); ?></div>
                    </div>
                    
                    <div style="flex: 1; min-width: 130px; border: 1px solid #333; border-radius: 5px; padding: 20px; text-align: center; background-color: #fafafa;">
                        <div style="font-size: 12px; color: #555; text-transform: uppercase; margin-bottom: 8px;">Pending Verifications</div>
                        <div style="font-size: 24px; font-weight: bold;"><?php echo number_format($pending_verifications); ?></div>
                    </div>

                    <div style="flex: 1; min-width: 130px; border: 1px solid #333; border-radius: 5px; padding: 20px; text-align: center; background-color: #fafafa;">
                        <div style="font-size: 12px; color: #555; text-transform: uppercase; margin-bottom: 8px;">Open Disputes</div>
                        <div style="font-size: 24px; font-weight: bold;"><?php echo number_format($open_disputes); ?></div>
                    </div>

                    <div style="flex: 1; min-width: 130px; border: 1px solid #333; border-radius: 5px; padding: 20px; text-align: center; background-color: #fafafa;">
                        <div style="font-size: 12px; color: #555; text-transform: uppercase; margin-bottom: 8px;">Active Listings</div>
                        <div style="font-size: 24px; font-weight: bold;"><?php echo number_format($active_listings); ?></div>
                    </div>

                </div>

                <h3 style="margin-top: 0; margin-bottom: 15px; font-size: 18px; font-weight: normal;">Recent Complaints</h3>
                <div style="border: 1px solid #333; border-radius: 5px;">
                    <?php 
                    if ($result_reports && mysqli_num_rows($result_reports) > 0) {
                        while($report = mysqli_fetch_assoc($result_reports)) {
                            $title = $report['title'] ?? ($report['complaint_text'] ?? 'Complaint #' . $report['id']);
                            $status = $report['status'] ?? 'pending';
                            $status_color = (strtolower($status) == 'resolved') ? 'green' : 'red';
                            ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; border-bottom: 1px solid #ccc;">
                                <span style="font-size: 14px;">
                                    <strong>[Complaint]</strong> <?php echo htmlspecialchars($title); ?>
                                </span>
                                <span style="border: 1px solid #999; padding: 4px 10px; font-size: 12px; border-radius: 4px; color: <?php echo $status_color; ?>; text-transform: capitalize;">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div style="padding: 15px; text-align: center; color: #555; font-size: 14px;">
                            No recent complaints found.
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