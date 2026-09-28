<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - Technician Portal</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; display: flex; justify-content: center;">

    <!-- Main Dashboard Container -->
    <div style="background-color: #fff; width: 100%; max-width: 1000px; border: 1px solid #333; display: flex; flex-direction: column; min-height: 650px;">
        
        <!-- Top Navigation Bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #333;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <h2 style="margin: 0; font-size: 18px; font-weight: normal;">Technician Portal</h2>
            </div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <a href="Logout.php" style="font-size: 12px; color: red; text-decoration: none;">Logout</a>
            </div>
        </div>

        <!-- Layout Split -->
        <div style="display: flex; flex: 1;">
            
            <!-- Sidebar -->
            <div style="width: 200px; border-right: 1px solid #333; padding: 20px; display: flex; flex-direction: column; gap: 15px;">
                <a href="TechnicianController.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                    Profile
                </a>
                <a href="Technician_parts.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                    Find Parts
                </a>
                <a href="Technician_repair.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                    Repair Jobs
                </a>
                <a href="Technician_equipment.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">
                    Equipment
                </a>
                <a href="TechnicianMessagesController.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px; background-color: #f0f0f0;">
                    Messages ✓
                </a>
            </div>

            <!-- Main Content Area -->
            <div style="flex: 1; padding: 30px; display: flex; flex-direction: column;">
                <h3 style="margin-top: 0; font-weight: normal; margin-bottom: 20px;">Customer Messages</h3>
                
                <!-- Customer Selection Buttons -->
                <div style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
                    <span style="font-size: 13px; font-weight: bold; align-self: center;">Select Customer:</span>
                    <?php
                    if ($customers_query && mysqli_num_rows($customers_query) > 0) {
                        while ($cust = mysqli_fetch_assoc($customers_query)) {
                            $cust_id_val = $cust['id'] ?? $cust['ID'];
                            $is_active = ($selected_cust_id == $cust_id_val);
                            $bg_col = $is_active ? '#475569' : '#f1f5f9';
                            $txt_col = $is_active ? '#ffffff' : '#334155';
                            echo '<a href="TechnicianMessagesController.php?customer_id=' . $cust_id_val . '">';
                            echo '<input type="button" value="' . htmlspecialchars($cust['name']) . '" style="background-color: ' . $bg_col . '; color: ' . $txt_col . '; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 4px; cursor: pointer;">';
                            echo '</a>';
                        }
                    } else {
                        echo '<span style="font-size: 13px; color: #777;">No customers found.</span>';
                    }
                    ?>
                </div>

                <?php if ($selected_cust_id > 0): ?>
                    <!-- Chat Box Container -->
                    <div style="border: 1px solid #cbd5e1; border-radius: 8px; background: #ffffff; display: flex; flex-direction: column; flex: 1;">
                        
                        <!-- Chat Header -->
                        <div style="background: #f8fafc; padding: 12px 15px; border-bottom: 1px solid #e2e8f0; font-weight: bold; color: #1e3a8a; font-size: 14px;">
                            Chat with: <?php echo htmlspecialchars($selected_cust_name); ?>
                        </div>

                        <!-- Messages History Area -->
                        <div style="padding: 15px; height: 280px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; background: #fff;">
                            <?php
                            if ($result_msg && mysqli_num_rows($result_msg) > 0) {
                                while ($row = mysqli_fetch_assoc($result_msg)) {
                                    $is_me = ($row['sender_role'] == 'Technician' && $row['sender_id'] == $tech_id);
                                    
                                    if ($is_me) {
                                        echo "<div align='right' style='margin: 4px 0;'>";
                                        echo "<span style='background-color: #f1f5f9; color: #1e3a8a; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; display: inline-block; max-width: 75%; text-align: left;'>";
                                        echo "<b>You:</b> " . htmlspecialchars($row['message']);
                                        echo "</span></div>";
                                    } else {
                                        echo "<div align='left' style='margin: 4px 0;'>";
                                        echo "<span style='background-color: #ffffff; color: #334155; border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 6px; display: inline-block; max-width: 75%; text-align: left;'>";
                                        echo "<b>" . htmlspecialchars($selected_cust_name) . ":</b> " . htmlspecialchars($row['message']);
                                        echo "</span></div>";
                                    }
                                }
                            } else {
                                echo "<p align='center' style='color: #64748b; margin-top: 80px; font-size: 14px;'>No conversation yet with this customer. Send a message below!</p>";
                            }
                            ?>
                        </div>

                        <!-- Message Input Form -->
                        <div style="background: #f8fafc; padding: 12px 15px; border-top: 1px solid #e2e8f0;">
                            <form method="POST" action="TechnicianMessagesController.php?customer_id=<?php echo $selected_cust_id; ?>" style="display: flex; gap: 10px;">
                                <input type="hidden" name="receiver_id" value="<?php echo $selected_cust_id; ?>">
                                <input type="text" name="message_text" placeholder="Type your message here..." style="flex: 1; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; font-size: 14px;" required>
                                <input type="submit" name="send_message" value="Send" style="background-color: #475569; color: #ffffff; border: none; padding: 8px 18px; border-radius: 4px; cursor: pointer; font-size: 14px;">
                            </form>
                        </div>

                    </div>
                <?php else: ?>
                    <div style="padding: 40px; text-align: center; border: 1px dashed #cbd5e1; border-radius: 8px; color: #64748b; background: #fafafa;">
                        Please select a customer from the buttons above to start or view your conversation.
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

</body>
</html>