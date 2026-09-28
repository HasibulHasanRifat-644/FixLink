<?php
session_start();
include('db.php');

$cust_id = $_SESSION["id"] ?? 1;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_id'])) {
    $del_id = intval($_POST['cancel_id']);
   
    
    $sql_cancel = "DELETE FROM repair_requests WHERE id=$del_id AND customer_id=$cust_id AND status='Pending'";
    mysqli_query($conn, $sql_cancel);
    
    header("Location: trackrepair.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Track Repair - FixLink</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f7f9;
            margin: 0;
            padding: 40px 20px;
        }

        h1 {
            color: #1e3a8a;
            text-align: center;
            margin-bottom: 25px;
            font-size: 26px;
        }

        .container {
            width: 860px;
            margin: auto;
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            padding: 12px 14px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
            color: #334155;
        }

        th {
            background-color: #f8fafc;
            color: #1e293b;
            font-weight: 600;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 12px;
            display: inline-block;
        }

        .badge-pending {
            background-color: #fef3c7;
            color: #d97706; 
        }

        .badge-progress {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        .badge-completed {
            background-color: #e6f7ec;
            color: #155724;
        }

        .urgent-tag {
            background-color: #fee2e2;
            color: #dc2626;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
            margin-top: 4px;
        }

        .btn-cancel {
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-cancel:hover {
            background-color: #dc2626;
        }

        .btn-back {
            display: inline-block;
            background-color: #64748b;
            color: white;
            text-decoration: none;
            padding: 9px 18px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 25px;
        }

        .btn-back:hover {
            background-color: #475569;
        }

        .footer-text {
            color: #64748b;
            font-size: 13px;
            margin-top: 30px;
            text-align: center;
        }
    </style>
</head>
<body>

    <h1>Track Repair Status</h1>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Device</th>
                    <th>Problem Description</th>
                    <th>Location</th>
                    <th>Current Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM repair_requests WHERE customer_id=$cust_id ORDER BY id DESC");
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $status = $row['status'];
                    $is_urgent = isset($row['is_urgent']) && $row['is_urgent'] == 1;
                    
                    $badge_class = "badge-pending";
                    if ($status == 'In Progress' || $status == 'Under Repair') {
                        $badge_class = "badge-progress";
                    } elseif ($status == 'Completed') {
                        $badge_class = "badge-completed";
                    }

                    echo "<tr>
                        <td>
                            <b>" . htmlspecialchars($row['device_name']) . "</b>";
                            if ($is_urgent) {
                                echo "<br><span class='urgent-tag'>URGENT</span>";
                            }
                    echo "</td>
                        <td>" . htmlspecialchars($row['problem_desc']) . "</td>
                        <td>" . htmlspecialchars($row['location'] ?? 'N/A') . "</td>
                        <td><span class='status-badge {$badge_class}'>" . htmlspecialchars($status) . "</span></td>
                        <td>";
                    
                    if ($status == 'Pending') {
                        ?>
                       
                        <form method="POST" action="trackrepair.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this request?');">
                            <input type="hidden" name="cancel_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn-cancel">Cancel</button>
                        </form>
                        <?php
                    } else {
                        echo "<span style='color:#94a3b8; font-size:12px;'>Locked</span>";
                    }

                    echo "</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='color:#64748b; padding: 15px;'>No ongoing repair requests found.</td></tr>";
            }
            ?>
            </tbody>
        </table>

        <a href="customerdashboard.php" class="btn-back">Back</a>
    </div>

    <p class="footer-text">
        Copyright &copy; <?php echo date("Y"); ?> FixLink
    </p>

</body>
</html>