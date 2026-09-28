<?php
session_start();

if(!isset($_SESSION["userid_email"])) {
    header("Location: login.php");
    exit();
}

include("db.php");

// 1. Handle Part Request Submission
// CHANGED: this now writes into the Supplier group's own tables
// (supplier_parts / supplier_part_requests) instead of the admin
// catalog, so requests actually reach the Supplier dashboard's
// Accept/Reject page.
$success_message = "";
if(isset($_POST['request_part'])) {
    $requested_part_id = (int)$_POST['part_id'];
    $quantity = (int)$_POST['quantity'];
    $tech_email = mysqli_real_escape_string($conn, $_SESSION["userid_email"]);

    // ADDED: optionally link this request back to the customer's
    // original free-text part request, if the technician picked one
    // from the dropdown. NULL if they left it on "Not linked".
    $customer_request_id = !empty($_POST['customer_request_id']) ? (int)$_POST['customer_request_id'] : null;

    // CHANGED: supplier_part_requests.technician_id is a real integer
    // (a users.ID), not a free-text email, so resolve it first.
    $tech_id = null;
    $tech_lookup = mysqli_query($conn, "SELECT ID FROM users WHERE email = '$tech_email' AND role = 'technician'");
    if($tech_row = mysqli_fetch_assoc($tech_lookup)) {
        $tech_id = (int) $tech_row['ID'];
    }

    // CHANGED: fetch from supplier_parts (column `price`, plus the
    // owning supplier_id) instead of the admin catalog's `parts`.
    $part_query = mysqli_query($conn, "SELECT part_name, price, supplier_id FROM supplier_parts WHERE id = $requested_part_id");
    $part_data = mysqli_fetch_assoc($part_query);

    if($part_data && $tech_id) {
        $part_name = mysqli_real_escape_string($conn, $part_data['part_name']);
        $supplier_id = (int) $part_data['supplier_id'];
        $total_price = $part_data['price'] * $quantity;

        // ADDED: customer_request_id column, NULL-safe.
        $customer_request_sql = $customer_request_id ? $customer_request_id : "NULL";

        // CHANGED: supplier_part_requests doesn't store part_name/
        // total_price directly -- those are always looked up live via
        // the part_id join on the Supplier dashboard side.
        $insert_query = "INSERT INTO supplier_part_requests (supplier_id, technician_id, part_id, customer_request_id, quantity, status, request_date)
                         VALUES ($supplier_id, $tech_id, $requested_part_id, $customer_request_sql, $quantity, 'Pending', NOW())";

        if(mysqli_query($conn, $insert_query)) {
            $success_message = "Successfully requested $quantity x " . htmlspecialchars($part_data['part_name']) . " (Total: ৳" . number_format($total_price, 0) . ")";
        } else {
            $success_message = "Error requesting part: " . mysqli_error($conn);
        }
    } else if(!$tech_id) {
        $success_message = "Error: your account isn't recognized as a technician in the users table.";
    }
}

// Handle AJAX Request for Live Search/Filtering
if(isset($_POST['is_ajax'])) {
    $search_text = trim($_POST['search_text'] ?? '');
    $cat_filter = $_POST['cat_filter'] ?? 'all';
    $avail_filter = $_POST['avail_filter'] ?? 'all';

    // CHANGED: query supplier_parts directly. It stores category as a
    // plain text column, so there's no separate categories table to
    // join here (unlike the admin catalog's parts table).
    $sql = "SELECT * FROM supplier_parts WHERE 1=1";

    if(!empty($search_text)) {
        $search_text_esc = mysqli_real_escape_string($conn, $search_text);
        $sql .= " AND (part_name LIKE '%$search_text_esc%' OR category LIKE '%$search_text_esc%')";
    }

    if($cat_filter !== 'all' && !empty($cat_filter)) {
        $cat_esc = mysqli_real_escape_string($conn, $cat_filter);
        $sql .= " AND category = '$cat_esc'";
    }

    if($avail_filter === 'in_stock') {
        $sql .= " AND stock > 0";
    } elseif($avail_filter === 'out_stock') {
        $sql .= " AND stock <= 0";
    }

    $parts_result = mysqli_query($conn, $sql);

    if($parts_result && mysqli_num_rows($parts_result) > 0) {
        while($row = mysqli_fetch_assoc($parts_result)) {
            $formatted_id = "P-" . str_pad($row['id'], 4, '0', STR_PAD_LEFT);
            $p_name = $row['part_name'];
            $safe_name = htmlspecialchars(addslashes($p_name));
            // CHANGED: supplier_parts column names (price/stock) instead
            // of the admin catalog's (price_bdt/stock_quantity)
            $price = $row['price'];
            $stock = $row['stock'];
            ?>
            <!-- Part Card -->
            <div style="background-color: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
                <span style="font-size: 12px; color: #777; display: block; margin-bottom: 5px;"><?php echo $formatted_id; ?></span>
                <h4 style="margin: 0 0 10px 0; font-size: 16px; color: #222;"><?php echo htmlspecialchars($p_name); ?></h4>

                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 20px; font-size: 12px; color: #555; flex-wrap: wrap;">
                    <!-- CHANGED: supplier_parts.category is plain text, not a joined category_name -->
                    <span style="background-color: #eee; padding: 4px 8px; border-radius: 4px;"><?php echo htmlspecialchars($row['category'] ?? 'Uncategorized'); ?></span>
                    <span>📍 Elephant Road, Dhaka</span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                    <div>
                        <span style="display: block; font-size: 18px; font-weight: bold; color: #222; margin-bottom: 3px;">৳<?php echo number_format($price, 0); ?></span>
                        <span style="font-size: 12px; color: <?php echo ($stock > 0) ? '#555' : 'red'; ?>;">
                            <?php echo ($stock > 0) ? $stock . ' in stock' : 'Out of stock'; ?>
                        </span>
                    </div>

                    <!-- Trigger Modal Button -->
                    <button type="button" 
                            onclick="openModal(<?php echo $row['id']; ?>, '<?php echo $safe_name; ?>', <?php echo $price; ?>, <?php echo $stock; ?>)"
                            <?php echo ($stock <= 0) ? 'disabled style="background-color: #ccc; cursor: not-allowed;"' : 'style="background-color: #f37021; cursor: pointer;"'; ?> 
                            class="request-btn" style="color: #fff; border: none; padding: 8px 16px; border-radius: 5px; font-size: 13px;">
                        Request part
                    </button>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p style='color: #777; font-size: 14px; grid-column: 1 / -1;'>No parts found matching your criteria.</p>";
    }
    exit(); // Terminate script so it only outputs the grid HTML for AJAX
}

// CHANGED: supplier_parts has no separate categories table -- pull the
// distinct category values that are actually in use directly from it.
$categories_result = mysqli_query($conn, "SELECT DISTINCT category AS id, category AS name FROM supplier_parts WHERE category IS NOT NULL AND category <> ''");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Parts - Technician Portal</title>
    <style>
        /* Modal Base Styles */
        .modal-overlay {
            display: none;
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center; align-items: center; z-index: 1000;
        }
        .modal-content {
            background-color: #fff; padding: 25px; border-radius: 8px;
            width: 100%; max-width: 400px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
    </style>
    <script>
        // Modal Logic
        let currentPrice = 0;

        function openModal(id, name, price, maxStock) {
            currentPrice = price;
            document.getElementById('modal_part_id').value = id;
            document.getElementById('modal_part_name').innerText = name;
            document.getElementById('modal_price_display').innerText = "৳" + price;

            const qtyInput = document.getElementById('modal_quantity');
            qtyInput.max = maxStock;
            qtyInput.value = 1;

            updateTotal();
            document.getElementById('requestModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('requestModal').style.display = 'none';
        }

        function updateTotal() {
            const qty = document.getElementById('modal_quantity').value;
            const total = qty * currentPrice;
            document.getElementById('modal_total_display').innerText = "৳" + total.toLocaleString();
        }

        // AJAX Filter Logic
        function filterParts() {
            var searchText = document.getElementById("search_text").value;
            var catFilter = document.getElementById("cat_filter").value;
            var availFilter = document.getElementById("avail_filter").value;

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if(xhr.readyState == 4) {
                    if(xhr.status == 200) {
                        document.getElementById("parts_grid").innerHTML = xhr.responseText;
                    } else {
                        console.error("AJAX Error: Status " + xhr.status);
                    }
                }
            };
            // Ensure filename matches your actual file name
            xhr.open("POST", "Technician_parts.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("is_ajax=1" +
                     "&search_text=" + encodeURIComponent(searchText) + 
                     "&cat_filter=" + encodeURIComponent(catFilter) + 
                     "&avail_filter=" + encodeURIComponent(availFilter));
        }

        window.onload = function() {
            filterParts();
        };
    </script>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; display: flex; justify-content: center;">

    <div style="background-color: #fff; width: 100%; max-width: 1000px; border: 1px solid #333; display: flex; flex-direction: column; min-height: 700px;">

        <!-- Top Navigation Bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #333;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <h2 style="margin: 0; font-size: 18px; font-weight: normal;">Technician Console</h2>
            </div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <span style="border: 1px solid #333; border-radius: 50%; width: 30px; height: 30px; display: inline-block; text-align: center; font-size: 12px; line-height: 30px; background-color: #eee;">AN</span>
                <a href="logout.php" style="font-size: 12px; color: red; text-decoration: none;">Logout</a>
            </div>
        </div>

        <div style="display: flex; flex: 1;">

            <!-- Sidebar -->
            <div style="width: 200px; border-right: 1px solid #333; padding: 20px; display: flex; flex-direction: column; gap: 15px;">
                <a href="Technician.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">Profile</a>
                <a href="Technician_parts.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px; background-color: #f0f0f0;">Find Parts ✓</a>
                <a href="Technician_repair.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">Repair Jobs</a>
                <a href="Technician_equipment.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">Equipment</a>
                <a href="Technician_messages.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">Messages</a>
            </div>

            <!-- Main Content Area: Find Parts -->
            <div style="flex: 1; padding: 30px; background-color: #fafafa;">
                <h3 style="margin-top: 0; font-weight: normal; margin-bottom: 25px; font-size: 22px;">Find parts</h3>

                <?php if(!empty($success_message)) { ?>
                    <div style="background-color: #d4edda; color: #155724; padding: 10px 15px; border-radius: 5px; margin-bottom: 20px; font-size: 14px;">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php } ?>

                <!-- Filter Section -->
                <div style="display: flex; gap: 15px; margin-bottom: 30px; align-items: flex-end; flex-wrap: wrap;">
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; font-size: 12px; color: #555; margin-bottom: 5px;">Search</label>
                        <input type="text" id="search_text" onkeyup="filterParts()" placeholder="e.g. screen, motherboard" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                    </div>
                    <div style="min-width: 150px;">
                        <label style="display: block; font-size: 12px; color: #555; margin-bottom: 5px;">Category</label>
                        <select id="cat_filter" onchange="filterParts()" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                            <option value="all">All</option>
                            <?php 
                            if(mysqli_num_rows($categories_result) > 0) {
                                while($cat = mysqli_fetch_assoc($categories_result)) {
                                    echo "<option value='{$cat['id']}'>{$cat['name']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div style="min-width: 150px;">
                        <label style="display: block; font-size: 12px; color: #555; margin-bottom: 5px;">Availability</label>
                        <select id="avail_filter" onchange="filterParts()" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;">
                            <option value="all">All</option>
                            <option value="in_stock">In Stock</option>
                            <option value="out_stock">Out of Stock</option>
                        </select>
                    </div>
                </div>

                <!-- Results Grid (populated via AJAX) -->
                <div id="parts_grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
                </div>
            </div>
        </div>
    </div>

    <!-- The Confirmation Modal -->
    <div id="requestModal" class="modal-overlay">
        <div class="modal-content">
            <h3 style="margin-top: 0; color: #333; font-weight: normal; border-bottom: 1px solid #eee; padding-bottom: 10px;">Confirm Request</h3>

            <p style="font-size: 16px; font-weight: bold; margin-bottom: 5px;" id="modal_part_name"></p>
            <p style="font-size: 13px; color: #777; margin-top: 0;">Unit Price: <span id="modal_price_display"></span></p>

            <form method="POST" style="margin-top: 20px;">
                <input type="hidden" name="part_id" id="modal_part_id">

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; margin-bottom: 8px;">Quantity Needed:</label>
                    <input type="number" name="quantity" id="modal_quantity" min="1" required 
                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"
                           oninput="updateTotal()">
                </div>

                <!-- ADDED: optional link back to a customer's original part request -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; margin-bottom: 8px;">Fulfilling a customer request? (optional)</label>
                    <select name="customer_request_id" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                        <option value="">Not linked to a specific customer request</option>
                        <?php
                        $pending_customer_requests = mysqli_query($conn, "SELECT * FROM customer_part_requests WHERE status = 'Pending' ORDER BY id DESC");
                        if($pending_customer_requests && mysqli_num_rows($pending_customer_requests) > 0) {
                            while($cr = mysqli_fetch_assoc($pending_customer_requests)) {
                                $label = htmlspecialchars("#{$cr['id']} - {$cr['device_name']}: {$cr['part_name']} (qty {$cr['quantity']})");
                                echo "<option value=\"{$cr['id']}\">$label</option>";
                            }
                        }
                        ?>
                    </select>
                </div>

                <div style="background-color: #f9f9f9; padding: 15px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                    <span style="font-size: 14px; font-weight: bold;">Total Estimated Cost:</span>
                    <span id="modal_total_display" style="font-size: 18px; font-weight: bold; color: #f37021;"></span>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeModal()" style="padding: 10px 15px; background-color: #fff; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; color: #333;">Cancel</button>
                    <button type="submit" name="request_part" style="padding: 10px 20px; background-color: #333; border: none; border-radius: 4px; cursor: pointer; color: #fff;">Confirm Order</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>