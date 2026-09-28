<?php
session_start();
include("db.php");

if(!isset($_SESSION["userid_email"])) {
    header("Location: login.php");
    exit();
}

$active_tab = $_GET['tab'] ?? 'reviews';
$allowed_tabs = ['reviews', 'complaints', 'disputes', 'listings'];
if(!in_array($active_tab, $allowed_tabs)) {
    $active_tab = 'complaints';
}

$table = $active_tab;

// ADDED: each table actually has different columns -- this describes
// what each one really has, instead of assuming every table has
// title/description/status.
$table_config = [
    'reviews'    => ['title_col' => 'review_type', 'desc_col' => null,          'status_col' => null],
    'complaints' => ['title_col' => 'title',        'desc_col' => 'description', 'status_col' => 'status'],
    'disputes'   => ['title_col' => 'title',        'desc_col' => null,          'status_col' => 'status'],
    'listings'   => ['title_col' => 'title',        'desc_col' => null,          'status_col' => 'status'],
];
$config = $table_config[$active_tab];

if(isset($_GET['action']) && isset($_GET['id'])) {
    $action_id = intval($_GET['id']);

    // CHANGED: only allow "resolve" on tables that actually have a
    // status column (reviews doesn't -- trying this before would throw
    // "Unknown column 'status'" as a fatal error).
    if($_GET['action'] == 'resolve' && $config['status_col'] !== null) {
        $status_col = $config['status_col'];
        $update_sql = "UPDATE `$table` SET `$status_col`='resolved' WHERE id=$action_id";
        mysqli_query($conn, $update_sql);
    } elseif($_GET['action'] == 'remove') {
        $delete_sql = "DELETE FROM `$table` WHERE id=$action_id";
        mysqli_query($conn, $delete_sql);
    }
    header("Location: admin_moderation.php?tab=" . $active_tab);
    exit();
}
$query = "SELECT * FROM `$table`";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<head>
    <title>Admin - Moderation</title>
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
                <a href="admin_moderation.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px; background-color: #f0f0f0;">
                     Moderation
                </a>
               <a href="admin_settings.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                    Settings
                </a>
            </div>

            <div style="flex: 1; padding: 30px;">
                
                <div style="display: flex; gap: 10px; margin-bottom: 25px;">
                    <?php foreach($allowed_tabs as $tab): ?>
                        <a href="admin_moderation.php?tab=<?php echo $tab; ?>" style="text-decoration: none; padding: 8px 16px; border: 1px solid <?php echo ($active_tab == $tab) ? '#333' : '#ccc'; ?>; background-color: <?php echo ($active_tab == $tab) ? '#eee' : '#fff'; ?>; color: #000; border-radius: 5px; font-size: 14px; text-transform: capitalize;">
                            <?php echo ucfirst($tab); ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <div style="border: 1px solid #333; border-radius: 5px;">
                    <?php if($result && mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <?php
                                // CHANGED: read whichever column actually holds the
                                // title/description/status for THIS table, instead
                                // of assuming they're always called title/description/status.
                                $title_val = $row[$config['title_col']] ?? '(untitled)';
                                $desc_val = $config['desc_col'] !== null ? ($row[$config['desc_col']] ?? '') : null;
                                $status_val = $config['status_col'] !== null ? ($row[$config['status_col']] ?? 'unresolved') : null;
                            ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; border-bottom: 1px solid #333;">
                                <div style="flex: 1;">
                                    <span style="display: block; font-size: 15px; font-weight: bold; margin-bottom: 5px;"><?php echo htmlspecialchars($title_val); ?> (ID: <?php echo $row['id']; ?>)</span>
                                    <?php if($desc_val !== null): ?>
                                    <span style="display: block; font-size: 13px; color: #555;"><?php echo htmlspecialchars($desc_val); ?></span>
                                    <?php else: ?>
                                    <span style="display: block; font-size: 13px; color: #aaa; font-style: italic;">No additional details for this item.</span>
                                    <?php endif; ?>
                                </div>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <?php if($status_val !== null): ?>
                                    <span style="border: 1px solid #999; padding: 4px 10px; font-size: 12px; border-radius: 4px; color: #555; text-transform: uppercase;"><?php echo htmlspecialchars($status_val); ?></span>
                                    <?php endif; ?>

                                    <button onclick="alert('Details for ID <?php echo $row['id']; ?>:\n<?php echo htmlspecialchars($desc_val ?? $title_val); ?>')" style="border: 1px solid #333; padding: 6px 12px; font-size: 12px; border-radius: 4px; background-color: #fff; cursor: pointer;">Open details</button>

                                    <?php if($status_val !== null && $status_val != 'resolved'): ?>
                                        <a href="admin_moderation.php?tab=<?php echo $active_tab; ?>&action=resolve&id=<?php echo $row['id']; ?>" style="border: 1px solid #333; padding: 6px 12px; font-size: 12px; border-radius: 4px; background-color: #fff; color: #000; text-decoration: none;">Mark resolved</a>
                                    <?php endif; ?>

                                    <a href="admin_moderation.php?tab=<?php echo $active_tab; ?>&action=remove&id=<?php echo $row['id']; ?>" style="border: 1px solid #333; padding: 6px 12px; font-size: 12px; border-radius: 4px; background-color: #fff; cursor: pointer; color: red; text-decoration: none;" onclick="return confirm('Are you sure you want to remove this item?');">Remove</a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div style="padding: 20px; text-align: center; color: #777; font-size: 14px;">No items found under <?php echo ucfirst($active_tab); ?>.</div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
</body>
</html>
