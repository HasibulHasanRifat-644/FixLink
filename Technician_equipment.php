<?php
session_start();

if(!isset($_SESSION["userid_email"])) {
    header("Location: login.php");
    exit();
}

include("db.php");

// Handle Rental Submission
// CHANGED: this now writes into the Owner group's own tables
// (equipment / rental_requests) instead of the admin catalog's
// (equipments / rentals), so requests reach the Owner dashboard's
// Accept/Reject page and its automatic availability badge.
$success_message = "";
if(isset($_POST['submit_rental'])) {
    $equip_id = (int)$_POST['equipment_id'];
    $days = (int)$_POST['rental_days'];
    $tech_email = mysqli_real_escape_string($conn, $_SESSION["userid_email"]);

    // CHANGED: rental_requests.technician_id is a real integer
    // (a users.ID), not a free-text email, so resolve it first.
    $tech_id = null;
    $tech_lookup = mysqli_query($conn, "SELECT ID FROM users WHERE email = '$tech_email' AND role = 'technician'");
    if($tech_row = mysqli_fetch_assoc($tech_lookup)) {
        $tech_id = (int) $tech_row['ID'];
    }

    // CHANGED: fetch from `equipment` (column equipment_name, rental_price,
    // owner_id), and only allow requesting equipment that is currently
    // computed as Available -- same EXISTS check the Owner dashboard's
    // own list uses, so you can't request something already rented.
    $equip_query = mysqli_query($conn, "
        SELECT equipment_name, rental_price, owner_id
        FROM equipment e
        WHERE id = $equip_id
        AND NOT EXISTS (
            SELECT 1 FROM rental_requests rr
            WHERE rr.equipment_id = e.id AND rr.status = 'Accepted'
        )
    ");
    $equip_data = mysqli_fetch_assoc($equip_query);

    if($equip_data && $tech_id) {
        $owner_id = (int) $equip_data['owner_id'];
        // NOTE: rental_requests has no column for a negotiated rate --
        // the Owner side always computes cost as rental_price * rental_days
        // from the listing itself. The "Alternative Offer" field below is
        // shown for UI parity but doesn't change what gets stored.
        $total_amount = $equip_data['rental_price'] * $days;

        // CHANGED: rental_requests doesn't store equipment_name/rate/total
        // -- those are always looked up live via the equipment_id join
        // on the Owner dashboard side.
        $insert_query = "INSERT INTO rental_requests (owner_id, technician_id, equipment_id, rental_days, status, request_date)
                         VALUES ($owner_id, $tech_id, $equip_id, $days, 'Pending', NOW())";

        if(mysqli_query($conn, $insert_query)) {
            $success_message = "Rental request sent for " . htmlspecialchars($equip_data['equipment_name']) . " for $days days. Estimated total: ৳" . number_format($total_amount, 0);
        } else {
            $success_message = "Error requesting rental: " . mysqli_error($conn);
        }
    } else if(!$tech_id) {
        $success_message = "Error: your account isn't recognized as a technician in the users table.";
    } else {
        $success_message = "Error: Equipment not available.";
    }
}

// CHANGED: list only equipment that is currently Available (same
// computed-availability logic as the Owner dashboard's own list).
$equipment_result = mysqli_query($conn, "
    SELECT * FROM equipment e
    WHERE NOT EXISTS (
        SELECT 1 FROM rental_requests rr
        WHERE rr.equipment_id = e.id AND rr.status = 'Accepted'
    )
    ORDER BY id DESC
");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Equipment - Technician Portal</title>
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
        let standardDailyRate = 0;

        function openRentModal(id, name, price) {
            standardDailyRate = parseFloat(price);
            document.getElementById('modal_equip_id').value = id;
            document.getElementById('modal_equip_name').innerText = name;
            document.getElementById('modal_standard_rate').innerText = "৳" + standardDailyRate + " / day";
            
            // Reset inputs
            document.getElementById('modal_days').value = 1;
            document.getElementById('modal_offer').value = '';
            
            calculateTotal();
            document.getElementById('rentModal').style.display = 'flex';
        }

        function closeRentModal() {
            document.getElementById('rentModal').style.display = 'none';
        }

        function calculateTotal() {
            let days = parseInt(document.getElementById('modal_days').value) || 1;
            let offer = parseFloat(document.getElementById('modal_offer').value);
            
            let effectiveRate = (offer > 0) ? offer : standardDailyRate;
            let total = days * effectiveRate;
            
            document.getElementById('modal_total_display').innerText = "৳" + total.toLocaleString();
        }
    </script>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f9; margin: 0; padding: 20px; display: flex; justify-content: center;">

    <div style="background-color: #fff; width: 100%; max-width: 1000px; border: 1px solid #333; display: flex; flex-direction: column; min-height: 700px;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #333;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="border: 1px solid #999; border-radius: 50%; width: 24px; height: 24px; display: inline-block; text-align: center; font-size: 10px; line-height: 24px; color: #999;">logo</span>
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
                <a href="Technician.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">Profile
                </a>
                <a href="Technician_parts.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">Find Parts
                </a>
                <a href="Technician_repair.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;">Repair Jobs
                </a>
                <a href="Technician_equipment.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px; background-color: #f0f0f0;">Equipment ✓
                </a>
                <a href="Technician_messages.php" style="text-decoration: none; color: #000; border: 1px solid #333; border-radius: 5px; padding: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;"> Messages
                </a>
            </div>

            <!-- Main Content Area: Rent Equipment -->
            <div style="flex: 1; padding: 30px; background-color: #fafafa;">
                <h3 style="margin-top: 0; font-weight: normal; margin-bottom: 25px; font-size: 22px;">Rent equipment</h3>
                
                <?php if(!empty($success_message)) { ?>
                    <div style="background-color: #d4edda; color: #155724; padding: 10px 15px; border-radius: 5px; margin-bottom: 20px; font-size: 14px;">
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php } ?>

                <!-- Available Equipment Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px;">
<?php
if($equipment_result && mysqli_num_rows($equipment_result) > 0) {
    while($row = mysqli_fetch_assoc($equipment_result)) {
        $formatted_id = "E-" . str_pad($row['id'], 4, '0', STR_PAD_LEFT);
        // CHANGED: equipment.equipment_name instead of equipments.item_name
        $safe_name = htmlspecialchars(addslashes($row['equipment_name']));
        $price = $row['rental_price'];
        ?>
        
        <div style="background-color: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);">
            <span style="font-size: 12px; color: #777; display: block; margin-bottom: 5px;"><?php echo $formatted_id; ?></span>
            <h4 style="margin: 0 0 5px 0; font-size: 16px; color: #222;"><?php echo htmlspecialchars($row['equipment_name']); ?></h4>
            <span style="font-size: 12px; color: #999; display: block; margin-bottom: 15px;">Industrial Grade</span>
            
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px; font-size: 12px; color: #555;">
                <span>📍 Nilkhet, Dhaka</span>
                <span>🏬 Rafiq Traders</span>
            </div>
            <p style="font-size: 11px; color: #777; margin: 0 0 20px 0;">Deposit required • Refundable</p>
            
            <div style="display: flex; justify-content: space-between; align-items: flex-end;">
                <div>
                    <span style="display: inline-block; font-size: 18px; font-weight: bold; color: #222;">৳<?php echo number_format($price, 0); ?></span>
                    <span style="font-size: 12px; color: #777;"> / day</span>
                </div>
                
                <button type="button" 
                        onclick="openRentModal(<?php echo $row['id']; ?>, '<?php echo $safe_name; ?>', <?php echo $price; ?>)"
                        style="background-color: #f37021; color: #fff; border: none; padding: 8px 25px; border-radius: 5px; font-size: 13px; cursor: pointer;">
                    Rent
                </button>
            </div>
        </div>
        <?php
    }
} else {
    echo "<p style='color: #777; font-size: 14px;'>No equipment available for rent at the moment.</p>";
}
?>                </div>

            </div>
        </div>
    </div>

    <!-- The Rental Confirmation Modal -->
    <div id="rentModal" class="modal-overlay">
        <div class="modal-content">
            <h3 style="margin-top: 0; color: #333; font-weight: normal; border-bottom: 1px solid #eee; padding-bottom: 10px;">Rent Equipment</h3>
            
            <p style="font-size: 16px; font-weight: bold; margin-bottom: 5px;" id="modal_equip_name"></p>
            <p style="font-size: 13px; color: #777; margin-top: 0;">Standard Rate: <span id="modal_standard_rate"></span></p>

            <form method="POST" style="margin-top: 20px;">
                <input type="hidden" name="equipment_id" id="modal_equip_id">
                
                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 13px; margin-bottom: 5px; color: #555;">Days Required <span style="color:red;">*</span></label>
                        <input type="number" name="rental_days" id="modal_days" min="1" required 
                               style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"
                               oninput="calculateTotal()">
                    </div>
                    <div style="flex: 1;">
                        <label style="display: block; font-size: 13px; margin-bottom: 5px; color: #555;">Alternative Offer (৳/day)</label>
                        <input type="number" name="offered_rate" id="modal_offer" min="1" step="0.01" placeholder="Optional"
                               style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;"
                               oninput="calculateTotal()">
                    </div>
                </div>

                <div style="background-color: #f9f9f9; padding: 15px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; border: 1px solid #eee;">
                    <span style="font-size: 14px; font-weight: bold;">Total Rent Payable:</span>
                    <span id="modal_total_display" style="font-size: 18px; font-weight: bold; color: #f37021;"></span>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeRentModal()" style="padding: 10px 15px; background-color: #fff; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; color: #333;">Cancel</button>
                    <button type="submit" name="submit_rental" style="padding: 10px 20px; background-color: #333; border: none; border-radius: 4px; cursor: pointer; color: #fff;">Confirm Request</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>