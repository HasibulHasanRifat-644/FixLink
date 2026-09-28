<?php
session_start();

if(!isset($_SESSION["userid_email"])) {
    header("Location: login.php");
    exit();
}

include("db.php"); 
if(isset($_POST['update_status'])) {
    $target_id = $_POST['target_id'];
    $new_status = $_POST['new_status'];
    
    $update_sql = "UPDATE users SET status='$new_status' WHERE ID='$target_id'";
    mysqli_query($conn, $update_sql);
}
if(isset($_POST['is_ajax'])) {
    $tab = $_POST['tab'] ?? 'all';
    $value = trim($_POST["search_value"] ?? '');
    $role_filter = $_POST["role_filter"] ?? '';
    $status_filter = $_POST["status_filter"] ?? '';

    $sql = "SELECT * FROM users WHERE role != 'admin'";

    if(!empty($value)) {
        $sql .= " AND (name LIKE '%$value%' OR ID LIKE '%$value%')";
    }
    if(!empty($role_filter)) {
        $sql .= " AND role = '$role_filter'";
    }
    if(!empty($status_filter)) {
        $sql .= " AND status = '$status_filter'";
    } else {
        if($tab == 'pending') {
            $sql .= " AND status = 'pending'";
        }
    }

    $result = mysqli_query($conn, $sql);

    if($result && mysqli_num_rows($result) > 0) {
        while($row = mysqli_fetch_assoc($result)) {
            $current_status = !empty($row['status']) ? $row['status'] : 'pending'; 
            ?>
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; border-bottom: 1px solid #ccc;">
                <span style="font-size: 14px;">
                    <?php echo $row["ID"] . " - " . $row["name"] . " - " . ucfirst($row["role"]); ?>
                </span>
                
                <div style="display: flex; gap: 10px; align-items: center;">
                    <span style="border: 1px solid #999; padding: 4px 10px; font-size: 12px; border-radius: 4px; color: #555; text-transform: capitalize;">
                        <?php echo $current_status; ?>
                    </span>
                    <form method="POST" style="margin: 0; display: inline;">
                        <input type="hidden" name="target_id" value="<?php echo $row['ID']; ?>">
                        
                        <?php if($current_status == 'pending' || $current_status == 'suspended') { ?>
                            <input type="hidden" name="new_status" value="verified">
                            <button type="submit" name="update_status" style="border: 1px solid #333; padding: 4px 10px; font-size: 12px; border-radius: 4px; background-color: #fff; cursor: pointer; color: green;">Verify</button>
                        <?php } else { ?>
                            <input type="hidden" name="new_status" value="suspended">
                            <button type="submit" name="update_status" style="border: 1px solid #333; padding: 4px 10px; font-size: 12px; border-radius: 4px; background-color: #fff; cursor: pointer; color: red;">Suspend</button>
                        <?php } ?>
                    </form>
                </div>
            </div>
            <?php
        }
    } else {
        ?>
        <div style="padding: 15px; text-align: center; color: #555; font-size: 14px;">
            No users found.
        </div>
        <?php
    }
    exit();
}

$tab = $_GET['tab'] ?? 'all';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Users</title>
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
                <a href="admin_users.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px; background-color: #f0f0f0;">
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
                <a href="admin_settings.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                     Settings
                </a>
            </div>

            <div style="flex: 1; padding: 30px;">
                
                <div style="display: flex; gap: 10px; margin-bottom: 25px;">
                    <a href="?tab=all" style="text-decoration: none; color: #000; padding: 8px 16px; border: 1px solid <?php echo ($tab == 'all') ? '#333' : '#ccc'; ?>; background-color: <?php echo ($tab == 'all') ? '#eee' : '#fff'; ?>; border-radius: 20px; font-size: 14px;">All users</a>
                    <a href="?tab=pending" style="text-decoration: none; color: #000; padding: 8px 16px; border: 1px solid <?php echo ($tab == 'pending') ? '#333' : '#ccc'; ?>; background-color: <?php echo ($tab == 'pending') ? '#eee' : '#fff'; ?>; border-radius: 20px; font-size: 14px;">Pending verifications</a>
                </div>

                <div style="border: 1px solid #ccc; padding: 15px; border-radius: 5px; margin-bottom: 25px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center; background-color: #fafafa;">
                    <input type="text" id="search_value" onkeyup="filterUsers()" placeholder="Search by name or user ID" style="flex: 1; min-width: 200px; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                    <select id="role_filter" onchange="filterUsers()" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="">Role</option>
                        <option value="customer">Customer</option>
                        <option value="technician">Technician</option>
                        <option value="supplier">Supplier</option>
                        <option value="equipment">Equipment Owner</option>
                    </select>
                    <select id="status_filter" onchange="filterUsers()" style="padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
                        <option value="">Status</option>
                        <option value="pending">Pending</option>
                        <option value="verified">Verified</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>

                <div id="user_list" style="border: 1px solid #333; border-radius: 5px;">
                </div>

            </div>
        </div>
    </div>

<script>
function filterUsers()
{
    var searchValue = document.getElementById("search_value").value;
    var roleFilter = document.getElementById("role_filter").value;
    var statusFilter = document.getElementById("status_filter").value;
    var tab = "<?php echo $tab; ?>";

    var xhr = new XMLHttpRequest();

    xhr.onreadystatechange = function()
    {
        if(xhr.readyState == 4 && xhr.status == 200)
        {
            document.getElementById("user_list").innerHTML = xhr.responseText;
        }
    };
    xhr.open("POST", "admin_users.php", true);

    xhr.setRequestHeader(
        "Content-type",
        "application/x-www-form-urlencoded"
    );

    xhr.send("is_ajax=1" +
             "&search_value=" + encodeURIComponent(searchValue) + 
             "&role_filter=" + encodeURIComponent(roleFilter) + 
             "&status_filter=" + encodeURIComponent(statusFilter) + 
             "&tab=" + encodeURIComponent(tab));
}
window.onload = function() {
    filterUsers();
};
</script>

</body>
</html>