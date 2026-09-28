<?php
session_start();

if(!isset($_SESSION["userid_email"])) {
    header("Location: login.php");
    exit();
}

include("db.php");

// 1. Handle Quotation Submission
$success_message = "";
if(isset($_POST['send_quote'])) {
    $request_id = (int)$_POST['request_id'];
    $price = (float)$_POST['quoted_price'];
    $note = mysqli_real_escape_string($conn, $_POST['note']);
    $tech_email = mysqli_real_escape_string($conn, $_SESSION["userid_email"]); 
    
    $insert_query = "INSERT INTO quotations (request_id, technician_email, price, note) 
                     VALUES ($request_id, '$tech_email', $price, '$note')";
    
    if(mysqli_query($conn, $insert_query)) {
        $success_message = "Quotation of ৳" . number_format($price, 0) . " sent successfully for RQ-" . str_pad($request_id, 4, '0', STR_PAD_LEFT) . "!";
    } else {
        $success_message = "Error sending quote: " . mysqli_error($conn);
    }
}

// 2. Initialize filter variables
$cat_filter = $_POST['cat_filter'] ?? 'all';
$show_filter = $_POST['show_filter'] ?? 'open';

// 3. Build dynamic SQL query for requests (changed 'open' to 'pending' to match your database)
$sql = "SELECT * FROM repair_requests WHERE status = 'pending'";

if($cat_filter !== 'all' && !empty($cat_filter)) {
    $cat_esc = mysqli_real_escape_string($conn, $cat_filter);
    $sql .= " AND category = '$cat_esc'";
}

if($show_filter === 'urgent') {
    $sql .= " AND is_urgent = 1";
}

$sql .= " ORDER BY created_at DESC";
$requests_result = mysqli_query($conn, $sql);

// Fetch unique categories from the categories table or repair requests
$categories_result = mysqli_query($conn, "SELECT name AS category FROM categories");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repair Requests - Technician Portal</title>
    <style>
        .modal-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.5); justify-content: center; align-items: center; z-index: 1000;
        }
        .modal-content {
            background-color: #fff; padding: 25px; border-radius: 8px;
            width: 100%; max-width: 450px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
    </style>
    <script>
        function openQuoteModal(id, title) {
            document.getElementById('modal_request_id').value = id;
            document.getElementById('modal_request_title').innerText = title;
            document.getElementById('quoteModal').style.display = 'flex';
        }
        function closeQuoteModal() {
            document.getElementById('quoteModal').style.display = 'none';
        }
    </script>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; display: flex; justify-content: center;">

    <div style="background-color: #fff; width: 100%; max-width: 1000px; border: 1px solid #333; display: flex; flex-direction: column; min-height: 700px;">
        
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
            
            <div style="width: 200px; border-right: 1px solid #333; padding: 20px; display: flex; flex-direction: column; gap: 15px;">
                <a href="Technician.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">Profile
                </a>
                <a href="Technician_parts.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
Find Parts
                </a>
                <a href="Technician_repair.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px; background-color: #f0f0f0;">
Repair Jobs ✓
                </a>
                <a href="Technician_equipment.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">Equipment
                </a>
                <a href="Technician_messages.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
 Messages
                </a>
            </div>

            <div style="flex: 1; padding: 30px; background-color: #fafafa;">
                <h3 style="margin-top: 0; font-weight: normal; margin-bottom: 25px; font-size: 22px;">Repair requests</h3>
                
                <?php if(!empty($success_message)) { ?>
                    <div style="background-color: #d4edda; color: #155724; padding: 10px 15px; border-radius: 5px; margin-bottom: 20px; font-size: 14px;">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php } ?>

                <form method="POST" style="display: flex; gap: 15px; margin-bottom: 30px; align-items: flex-end; flex-wrap: wrap; background: #fff; padding: 15px; border: 1px solid #e0e0e0; border-radius: 8px;">
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; font-size: 12px; color: #555; margin-bottom: 5px;">Category</label>
                        <select name="cat_filter" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;" onchange="this.form.submit()">
                            <option value="all">All categories</option>
                            <?php 
                            if($categories_result) {
                                while($cat = mysqli_fetch_assoc($categories_result)) {
                                    if(!empty($cat['category'])) {
                                        $selected = ($cat_filter == $cat['category']) ? 'selected' : '';
                                        echo "<option value='".htmlspecialchars($cat['category'])."' $selected>".htmlspecialchars($cat['category'])."</option>";
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div style="flex: 1; min-width: 200px;">
                        <label style="display: block; font-size: 12px; color: #555; margin-bottom: 5px;">Show</label>
                        <select name="show_filter" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;" onchange="this.form.submit()">
                            <option value="open" <?php if($show_filter == 'open') echo 'selected'; ?>>All open requests</option>
                            <option value="urgent" <?php if($show_filter == 'urgent') echo 'selected'; ?>>Urgent only</option>
                        </select>
                    </div>
                </form>

                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php
                    if($requests_result && mysqli_num_rows($requests_result) > 0) {
                        while($row = mysqli_fetch_assoc($requests_result)) {
                            $formatted_id = "RQ-" . str_pad($row['id'], 4, '0', STR_PAD_LEFT);
                            // CHANGED: the real repair_requests table calls these
                            // equipment_name and issue_description, not title/description.
                            $safe_title = htmlspecialchars(addslashes($row['equipment_name']));
                            ?>
                            
                            <div style="background-color: #fff; border: 1px solid #e0e0e0; border-left: 4px dashed #ccc; border-radius: 8px; padding: 20px; position: relative;">
                                
                                <div style="position: absolute; top: 20px; right: 20px;">
                                    <?php if($row['is_urgent']) { ?>
                                        <span style="border: 1px solid red; color: red; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold;">URGENT</span>
                                    <?php } else { ?>
                                        <span style="border: 1px solid #f37021; color: #f37021; padding: 4px 10px; border-radius: 4px; font-size: 11px; font-weight: bold;">OPEN</span>
                                    <?php } ?>
                                </div>

                                <span style="font-size: 12px; color: #777; display: block; margin-bottom: 5px;"><?php echo $formatted_id; ?></span>
                                <!-- CHANGED: equipment_name instead of title -->
                                <h4 style="margin: 0 0 10px 0; font-size: 18px; color: #222;"><?php echo htmlspecialchars($row['equipment_name']); ?></h4>
                                <!-- CHANGED: issue_description instead of description -->
                                <p style="font-size: 14px; color: #555; margin: 0 0 15px 0;"><?php echo htmlspecialchars($row['issue_description']); ?></p>
                                
                                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; font-size: 12px; color: #777; flex-wrap: wrap;">
                                    <span>📍 <?php echo htmlspecialchars($row['location']); ?></span>
                                    <span>🛠️ <?php echo htmlspecialchars($row['category']); ?></span>
                                    <span>👤 <?php echo htmlspecialchars($row['customer_name']); ?></span>
                                </div>
                                
                                <button type="button" onclick="openQuoteModal(<?php echo $row['id']; ?>, '<?php echo $safe_title; ?>')" style="background-color: #f37021; color: #fff; border: none; padding: 8px 20px; border-radius: 5px; font-size: 13px; cursor: pointer;">
                                    Send quotation
                                </button>
                            </div>
                            <?php
                        }
                    } else {
                        echo "<p style='color: #777; font-size: 14px;'>No open requests found matching your criteria.</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- The Quotation Modal -->
    <div id="quoteModal" class="modal-overlay">
        <div class="modal-content">
            <h3 style="margin-top: 0; color: #333; font-weight: normal; border-bottom: 1px solid #eee; padding-bottom: 10px;">Send Quotation</h3>
            
            <p style="font-size: 15px; font-weight: bold; margin-bottom: 15px;" id="modal_request_title"></p>

            <form method="POST" style="margin-top: 10px;">
                <input type="hidden" name="request_id" id="modal_request_id">
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; font-size: 14px; margin-bottom: 5px; color: #333;">Quoted Price (৳)</label>
                    <input type="number" name="quoted_price" min="1" step="0.01" required 
                           style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;" placeholder="e.g. 1500">
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-size: 14px; margin-bottom: 5px; color: #333;">Note to customer</label>
                    <textarea name="note" rows="4" required 
                              style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; resize: vertical;" placeholder="Explain what is included in the price..."></textarea>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeQuoteModal()" style="padding: 10px 15px; background-color: #fff; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; color: #333;">Cancel</button>
                    <button type="submit" name="send_quote" style="padding: 10px 20px; background-color: #333; border: none; border-radius: 4px; cursor: pointer; color: #fff;">Submit Quote</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
