<?php
session_start();
include('db.php');
$cust_id = $_SESSION["id"] ?? 1;

$sql = "SELECT * FROM repair_history WHERE customer_id = $cust_id ORDER BY repair_date DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Repair History - FixLink</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            width: 820px;
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
            background-color: #f1f5f9;
            color: #1e293b;
            font-weight: 600;
        }
        tbody tr:hover {
            background-color: #f8fafc;
        }
        .badge-done {
            background-color: #e6f7ec;
            color: #155724;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 12px;
            display: inline-block;
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
            margin-top: 20px;
            transition: background 0.3s ease;
        }
        .btn-back:hover {
            background-color: #475569;
        }
    </style>
</head>
<body>

    <h1>Repair History</h1>

    <div class="container">
        <table>
            <thead>
                <tr>
                    <th>Device Name</th>
                    <th>Problem Fixed</th>
                    <th>Technician</th>
                    <th>Completion Date</th>
                    <th>Total Cost</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $problem = isset($row['problem_desc']) ? $row['problem_desc'] : 'General Service';
                        
                        echo "<tr>
                            <td><b>" . htmlspecialchars($row['device_name']) . "</b></td>
                            <td>" . htmlspecialchars($problem) . "</td>
                            <td>" . htmlspecialchars($row['technician_name']) . "</td>
                            <td>" . htmlspecialchars($row['repair_date']) . "</td>
                            <td style='color:#0d9488; font-weight:600;'>৳ " . htmlspecialchars($row['cost']) . "</td>
                            <td><span class='badge-done'>" . htmlspecialchars($row['status']) . "</span></td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='color:#64748b; padding: 20px;'>No previous repair records available.</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <a href="customerdashboard.php" class="btn-back">Back</a>
    </div>

    <p align="center" style="color: #64748b; font-size: 13px; margin-top: 30px;">
        Copyright &copy; <?php echo date("Y"); ?> FixLink
    </p>

</body>
</html>