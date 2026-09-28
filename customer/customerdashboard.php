<?php
session_start();

if (!isset($_SESSION["id"]) && !isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>FixLink Customer Console</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #0b1120;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .console-container {
            width: 1050px;
            background-color: #111a2e;
            border-radius: 12px;
            border: 1px solid #1e293b;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .console-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 30px;
            border-bottom: 1px solid #1e293b;
        }

        .console-header h2 {
            color: #38bdf8;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-text {
            color: #94a3b8;
            font-size: 13px;
        }

        .user-text span {
            color: #38bdf8;
            font-weight: 600;
        }

        .logout-btn {
            background-color: rgba(239, 68, 68, 0.15);
            color: #f87171;
            text-decoration: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid rgba(239, 68, 68, 0.3);
            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            background-color: #dc2626;
            color: #ffffff;
            border-color: #dc2626;
        }

        .console-body {
            display: flex;
            min-height: 520px;
        }

        .sidebar {
            width: 220px;
            background-color: #0d1527;
            border-right: 1px solid #1e293b;
            padding: 15px 0;
        }

        .sidebar ul {
            list-style: none;
        }

        .sidebar li a {
            display: block;
            padding: 11px 24px;
            color: #94a3b8;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }

        .sidebar li a:hover, .sidebar li a.active {
            color: #38bdf8;
            background-color: #111e38;
            border-left-color: #38bdf8;
        }

        .main-panel {
            flex: 1;
            padding: 28px 30px;
        }

        .section-title {
            color: #ffffff;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 18px;
        }

        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
            margin-bottom: 28px;
        }

        .metric-card {
            background-color: #0b1120;
            border: 1px solid #1e293b;
            border-radius: 8px;
            padding: 18px 14px;
            text-align: center;
        }

        .metric-card .label {
            color: #94a3b8;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: block;
        }

        .metric-card .value {
            color: #38bdf8;
            font-size: 24px;
            font-weight: 700;
        }

        .table-container {
            background-color: #0b1120;
            border: 1px solid #1e293b;
            border-radius: 8px;
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 13px;
        }

        th {
            background-color: #0e172a;
            color: #94a3b8;
            padding: 12px 18px;
            font-weight: 600;
            border-bottom: 1px solid #1e293b;
        }

        td {
            padding: 13px 18px;
            color: #e2e8f0;
            border-bottom: 1px solid #142036;
        }

        tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 600;
        }

        .status-unresolved {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .status-review {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .status-progress {
            background: rgba(56, 189, 248, 0.15);
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.3);
        }
    </style>
</head>
<body>

    <div class="console-container">
        
        <div class="console-header">
            <h2>FixLink Customer Console</h2>
            <div class="header-actions">
                <div class="user-text">
                    Logged in as: <span><?php echo $_SESSION["username"] ?? "Customer"; ?></span>
                </div>
                <a href="./logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <div class="console-body">
            
            <div class="sidebar">
                <ul>
                    <li><a href="./dashboard.php" class="active">Overview</a></li>
                    <li><a href="./createrequest.php">Create Repair Request</a></li>
                    <li><a href="./quotations.php">Quotations & Offers</a></li>
                    <li><a href="./trackrepair.php">Track Repair Status</a></li>
                    <li><a href="./part_request.php">Part Requests</a></li>
                    <li><a href="./search_technician.php">Find Technicians</a></li>
                    <li><a href="./filter_technician.php">Filter Technicians</a></li>
                    <li><a href="./chat.php">Live Chat</a></li>
                    <li><a href="./repair_history.php">Repair History</a></li>
                    <li><a href="./profile.php">Manage Profile</a></li>
                </ul>
            </div>

            <div class="main-panel">
                
                <div class="section-title">Overview</div>

                <div class="metrics-grid">
                    <div class="metric-card">
                        <span class="label">Total Requests</span>
                        <div class="value">08</div>
                    </div>
                    <div class="metric-card">
                        <span class="label">Pending Action</span>
                        <div class="value">02</div>
                    </div>
                    <div class="metric-card">
                        <span class="label">Open Quotations</span>
                        <div class="value">03</div>
                    </div>
                    <div class="metric-card">
                        <span class="label">In Progress</span>
                        <div class="value">01</div>
                    </div>
                </div>

                <div class="section-title">Recent Repair Requests</div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Device / Problem</th>
                                <th>Location</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Samsung Galaxy Display Replacement</td>
                                <td>Dhanmondi, Dhaka</td>
                                <td><span class="status-badge status-unresolved">Unresolved</span></td>
                            </tr>
                            <tr>
                                <td>HP Pavilion Laptop Motherboard Issue</td>
                                <td>Mirpur, Dhaka</td>
                                <td><span class="status-badge status-review">In Review</span></td>
                            </tr>
                            <tr>
                                <td>Gree 1.5 Ton AC Cooling Gas Leak</td>
                                <td>Uttara, Dhaka</td>
                                <td><span class="status-badge status-progress">In Progress</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <p align="center" style="color:white; margin-top: 15px;">
            Copyright &copy; <?php echo date("Y"); ?> FixLink
        </p>

    </div>

</body>
</html>