<?php
session_start();
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['id'])) {
    $q_id = intval($_POST['id']);
    $status = ($_POST['action'] == 'accept') ? 'Accepted' : 'Rejected';
    
    $update_sql = "UPDATE quotations SET status='$status' WHERE id=$q_id";
    mysqli_query($conn, $update_sql);
    
    header("Location: quotations.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quotations - FixLink</title>
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
            padding: 12px 10px;
            text-align: center;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }

        th {
            background-color: #f8fafc;
            color: #1e293b;
            font-weight: 600;
        }

        td {
            color: #334155;
        }

        .device-col {
            font-weight: 600;
        }

        .cost-col {
            color: #0d9488;
            font-weight: 600;
        }

        /* বাটন স্টাইল */
        .btn-accept {
            background-color: #0d9488;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            margin-right: 4px;
        }

        .btn-accept:hover {
            background-color: #0f766e;
        }

        .btn-reject {
            background-color: #ef4444;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-reject:hover {
            background-color: #dc2626;
        }

        .badge-accepted {
            color: #0d9488;
            font-weight: bold;
        }

        .badge-rejected {
            color: #ef4444;
            font-weight: bold;
        }

        .badge-pending {
            color: #f59e0b;
            font-weight: bold;
        }

        .badge-done {
            color: #94a3b8;
            font-size: 12px;
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

    <h1>Technician Quotations & Responses</h1>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Device/Item</th>
                    <th>Technician</th>
                    <th>Estimated Cost</th>
                    <th>Remarks / Response</th>
                    <th>Est. Time</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $result = mysqli_query($conn, "SELECT * FROM quotations");
            if ($result && mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $device_name = isset($row['device_name']) ? $row['device_name'] : 'Device Repair';
                    $est_time = isset($row['delivery_time']) ? $row['delivery_time'] : '2-3 Days';

                    echo "<tr>
                        <td class='device-col'>" . htmlspecialchars($device_name) . "</td>
                        <td>" . htmlspecialchars($row['technician_name'] ?? 'Technician') . "</td>
                        <td class='cost-col'>৳ " . htmlspecialchars($row['estimated_cost'] ?? '0') . "</td>
                        <td>" . htmlspecialchars($row['tech_message'] ?? 'N/A') . "</td>
                        <td>" . htmlspecialchars($est_time) . "</td>
                        <td>";
                    
                    if ($row['status'] == 'Accepted') {
                        echo "<span class='badge-accepted'>Accepted</span>";
                    } elseif ($row['status'] == 'Rejected') {
                        echo "<span class='badge-rejected'>Rejected</span>";
                    } else {
                        echo "<span class='badge-pending'>Pending</span>";
                    }

                    echo "</td><td>";
                    
                    if ($row['status'] == 'Pending') {
                        ?>
                       
                        <form method="POST" action="quotations.php" style="display:inline;" onsubmit="return confirm('Confirm action?');">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="action" value="accept" class="btn-accept">Accept</button>
                            <button type="submit" name="action" value="reject" class="btn-reject">Reject</button>
                        </form>
                        <?php
                    } else {
                        echo "<span class='badge-done'>Done</span>";
                    }
                    echo "</td></tr>";
                }
            } else {
                echo "<tr><td colspan='7' style='padding: 15px; color: #64748b;'>No quotation offers available yet.</td></tr>";
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