<?php
session_start();
if(!isset($_SESSION["userid_email"])) {
    header("Location: login.php");
    exit();
}
include("db.php");
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['add_category']) && !empty($_POST['cat_name'])) {
        $name = mysqli_real_escape_string($conn, trim($_POST['cat_name']));
        mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$name')");
    }
    if(isset($_POST['delete_category'])) {
        $id = (int)$_POST['cat_id'];
        mysqli_query($conn, "DELETE FROM categories WHERE id = $id");
    }
    if(isset($_POST['edit_category']) && !empty($_POST['cat_name'])) {
        $id = (int)$_POST['cat_id'];
        $name = mysqli_real_escape_string($conn, trim($_POST['cat_name']));
        mysqli_query($conn, "UPDATE categories SET name = '$name' WHERE id = $id");
    }
    header("Location: admin_catalog.php?tab=categories");
    exit();
}

$tab = $_GET['tab'] ?? 'categories';
?>
<!DOCTYPE html>
<head>
    <title>Admin - Catalog</title>
    <script>
        function toggleCategory(id) {
            var content = document.getElementById('cat_content_' + id);
            content.style.display = content.style.display === 'none' ? 'block' : 'none';
        }
        function editCategory(id, currentName) {
            var newName = prompt("Edit Category Name:", currentName);
            if (newName && newName.trim() !== "") {
                document.getElementById('edit_id_' + id).value = id;
                document.getElementById('edit_name_' + id).value = newName;
                document.getElementById('edit_form_' + id).submit();
            }
        }
    </script>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; display: flex; justify-content: center;">

    <div style="background-color: #fff; width: 100%; max-width: 900px; border: 1px solid #333; display: flex; flex-direction: column; min-height: 600px;">
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #333;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="border: 1px solid #999; border-radius: 50%; width: 24px; height: 24px; display: inline-block; text-align: center; font-size: 10px; line-height: 24px;">logo</span>
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
                <a href="admin_catalog.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px; background-color: #f0f0f0;">
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
                <div style="display: flex; gap: 10px; margin-bottom: 30px;">
                    <a href="?tab=categories" style="text-decoration: none; color: #000; padding: 8px 16px; border: 1px solid <?php echo ($tab == 'categories') ? '#333' : '#ccc'; ?>; background-color: <?php echo ($tab == 'categories') ? '#eee' : '#fff'; ?>; border-radius: 5px; font-size: 14px;">Categories</a>
                    <a href="?tab=parts" style="text-decoration: none; color: #000; padding: 8px 16px; border: 1px solid <?php echo ($tab == 'parts') ? '#333' : '#ccc'; ?>; background-color: <?php echo ($tab == 'parts') ? '#eee' : '#fff'; ?>; border-radius: 5px; font-size: 14px;">Parts</a>
                    <a href="?tab=equipment" style="text-decoration: none; color: #000; padding: 8px 16px; border: 1px solid <?php echo ($tab == 'equipment') ? '#333' : '#ccc'; ?>; background-color: <?php echo ($tab == 'equipment') ? '#eee' : '#fff'; ?>; border-radius: 5px; font-size: 14px;">Equipment</a>
                </div>

                <?php if($tab == 'categories') { ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <span style="font-size: 16px; font-weight: normal; color: #333;">Categories</span>
                        <form method="POST" style="display: flex; gap: 10px; margin: 0;">
                            <input type="text" name="cat_name" placeholder="New Category Name" required style="padding: 6px; border: 1px solid #ccc; border-radius: 4px; font-size: 13px;">
                            <button type="submit" name="add_category" style="padding: 6px 12px; border: 1px solid #333; background-color: #fff; border-radius: 4px; cursor: pointer; font-size: 13px;">+ Add category</button>
                        </form>
                    </div>

                    <div style="border: 1px solid #333; border-radius: 5px;">
                        <?php
                        $cat_query = mysqli_query($conn, "SELECT * FROM categories");
                        while($c = mysqli_fetch_assoc($cat_query)) {
                            $cat_id = $c['id'];
                            ?>
                            <div style="border-bottom: 1px solid #ccc;">
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; cursor: pointer; background-color: #fafafa;" onclick="toggleCategory(<?php echo $cat_id; ?>)">
                                    <span style="font-size: 14px; font-weight: bold;">+ <?php echo htmlspecialchars($c['name']); ?></span>
                                    
                                    <div style="display: flex; gap: 15px; font-size: 16px; color: #555;" onclick="event.stopPropagation();">
                                        <span title="Edit" onclick="editCategory(<?php echo $cat_id; ?>, '<?php echo htmlspecialchars($c['name']); ?>')" style="cursor: pointer;">✏️</span>
                                        <form method="POST" style="margin: 0; display: inline;" onsubmit="return confirm('Delete this category and all related items?');">
                                            <input type="hidden" name="cat_id" value="<?php echo $cat_id; ?>">
                                            <button type="submit" name="delete_category" style="background: none; border: none; padding: 0; cursor: pointer;" title="Delete">🗑️</button>
                                        </form>
                                        <form id="edit_form_<?php echo $cat_id; ?>" method="POST" style="display: none;">
                                            <input type="hidden" name="cat_id" id="edit_id_<?php echo $cat_id; ?>">
                                            <input type="hidden" name="cat_name" id="edit_name_<?php echo $cat_id; ?>">
                                            <input type="hidden" name="edit_category" value="1">
                                        </form>
                                    </div>
                                </div>
                                <div id="cat_content_<?php echo $cat_id; ?>" style="display: none; padding: 15px; background-color: #fff;">
                                    <div style="margin-bottom: 15px;">
                                        <strong style="font-size: 13px; color: #777;">Parts:</strong>
                                        <ul style="margin: 5px 0; padding-left: 20px; font-size: 13px;">
                                            <?php
                                            $parts_q = mysqli_query($conn, "SELECT part_name, price_bdt FROM parts WHERE category_id = $cat_id");
                                            if($parts_q && mysqli_num_rows($parts_q) > 0) {
                                                while($p = mysqli_fetch_assoc($parts_q)) {
                                                    echo "<li>{$p['part_name']} (BDT " . number_format($p['price_bdt'], 2) . ")</li>";
                                                }
                                            } else echo "<li style='color: #aaa;'>No parts in this category</li>";
                                            ?>
                                        </ul>
                                    </div>
                                    <div>
                                        <strong style="font-size: 13px; color: #777;">Equipment:</strong>
                                        <ul style="margin: 5px 0; padding-left: 20px; font-size: 13px;">
                                            <?php
                                            $equip_q = mysqli_query($conn, "SELECT item_name, status FROM equipments WHERE category_id = $cat_id");
                                            if($equip_q && mysqli_num_rows($equip_q) > 0) {
                                                while($e = mysqli_fetch_assoc($equip_q)) {
                                                    echo "<li>{$e['item_name']} (Status: {$e['status']})</li>";
                                                }
                                            } else echo "<li style='color: #aaa;'>No equipment in this category</li>";
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                
                <?php } elseif($tab == 'parts') { ?>
                    <span style="display: block; font-size: 16px; font-weight: normal; color: #333; margin-bottom: 15px;">All Parts</span>
                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #333; font-size: 14px;">
                        <tr style="background-color: #eee;">
                            <th style="padding: 10px; border: 1px solid #ccc; text-align: left;">Part Name</th>
                            <th style="padding: 10px; border: 1px solid #ccc; text-align: left;">Part Number</th>
                            <th style="padding: 10px; border: 1px solid #ccc; text-align: left;">Category</th>
                            <th style="padding: 10px; border: 1px solid #ccc; text-align: left;">Price (BDT)</th>
                            <th style="padding: 10px; border: 1px solid #ccc; text-align: left;">Stock</th>
                        </tr>
                        <?php
                        $res = mysqli_query($conn, "SELECT p.*, c.name as cat_name FROM parts p LEFT JOIN categories c ON p.category_id = c.id");
                        while($row = mysqli_fetch_assoc($res)) {
                            echo "<tr>";
                            echo "<td style='padding: 10px; border: 1px solid #ccc;'>{$row['part_name']}</td>";
                            echo "<td style='padding: 10px; border: 1px solid #ccc;'>{$row['part_number']}</td>";
                            echo "<td style='padding: 10px; border: 1px solid #ccc;'>" . ($row['cat_name'] ?? 'Uncategorized') . "</td>";
                            echo "<td style='padding: 10px; border: 1px solid #ccc;'>BDT " . number_format($row['price_bdt'], 2) . "</td>";
                            echo "<td style='padding: 10px; border: 1px solid #ccc;'>{$row['stock_quantity']}</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                
                <?php } elseif($tab == 'equipment') { ?>
                    <span style="display: block; font-size: 16px; font-weight: normal; color: #333; margin-bottom: 15px;">All Equipment</span>
                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #333; font-size: 14px;">
                        <tr style="background-color: #eee;">
                            <th style="padding: 10px; border: 1px solid #ccc; text-align: left;">ID</th>
                            <th style="padding: 10px; border: 1px solid #ccc; text-align: left;">Item Name</th>
                            <th style="padding: 10px; border: 1px solid #ccc; text-align: left;">Category</th>
                            <th style="padding: 10px; border: 1px solid #ccc; text-align: left;">Status</th>
                        </tr>
                        <?php
                        $res = mysqli_query($conn, "SELECT e.*, c.name as cat_name FROM equipments e LEFT JOIN categories c ON e.category_id = c.id");
                        while($row = mysqli_fetch_assoc($res)) {
                            echo "<tr>";
                            echo "<td style='padding: 10px; border: 1px solid #ccc;'>{$row['id']}</td>";
                            echo "<td style='padding: 10px; border: 1px solid #ccc;'>{$row['item_name']}</td>";
                            echo "<td style='padding: 10px; border: 1px solid #ccc;'>" . ($row['cat_name'] ?? 'Uncategorized') . "</td>";
                            echo "<td style='padding: 10px; border: 1px solid #ccc;'>{$row['status']}</td>";
                            echo "</tr>";
                        }
                        ?>
                    </table>
                <?php } ?>

            </div>
        </div>
    </div>
</body>
</html>