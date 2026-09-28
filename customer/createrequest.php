<?php
session_start();
include('db.php');

$message = "";
$uploaded_image_tag = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cust_id = $_SESSION["id"] ?? 1; 
    $category = $_POST['device_type'] ?? '';
    $device = $_POST['device_name'] ?? '';
    $problem = $_POST['problem_desc'] ?? '';
    $location = $_POST['location'] ?? '';
    $is_urgent = isset($_POST['is_urgent']) ? 1 : 0;

    if (!empty($category) && !empty($device) && !empty($problem) && !empty($location)) {
        
        $full_device = $category . " - " . $device;
        $image_path = "";

        
        if (isset($_FILES['problem_image']) && $_FILES['problem_image']['error'] == 0) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $file_name = time() . "_" . basename($_FILES["problem_image"]["name"]);
            $image_path = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES["problem_image"]["tmp_name"], $image_path)) {
                
                $uploaded_image_tag = '<img 
                    src="' . htmlspecialchars($image_path) . '" 
                    alt="there is a problem" 
                    title="a pic" 
                    width="700" 
                    height="750" 
                    style="float:right;border:3px solid black;margin:50px 100px;">';
            }
        }

        $sql = "INSERT INTO repair_requests (customer_id, device_name, problem_desc, image_path, location, is_urgent, status) 
                VALUES ('$cust_id', '$full_device', '$problem', '$image_path', '$location', '$is_urgent', 'Pending')";

        if (mysqli_query($conn, $sql)) {
            $message = "<div class='alert success'>Repair request submitted successfully!</div>";
        } else {
            $message = "<div class='alert error'>Failed to submit request. Please try again.</div>";
        }
    } else {
        $message = "<div class='alert error'>Please fill in all required fields.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Repair Request - FixLink</title>
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

        .form-box {
            width: 440px;
            margin: auto;
            background: #ffffff;
            padding: 25px 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }

        .alert {
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .alert.success {
            background-color: #e6f7ec;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background-color: #fce8e6;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        label {
            font-weight: 600;
            color: #334155;
            display: block;
            margin-top: 14px;
            font-size: 14px;
        }

        input[type="text"], input[type="file"], select, textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 10px 12px;
            margin-top: 6px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 14px;
            outline: none;
            background-color: #ffffff;
            font-family: inherit;
            transition: border 0.2s;
        }

        input[type="text"]:focus, select:focus, textarea:focus {
            border-color: #2563eb;
        }

        .btn-container {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 22px;
        }

        .btn-submit {
            flex: 1;
            background-color: #0d9488;
            color: white;
            border: none;
            padding: 10px 0;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background-color: #0f766e;
        }

        .btn-back {
            background-color: #64748b;
            color: white;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            transition: background 0.2s;
        }

        .btn-back:hover {
            background-color: #475569;
        }
    </style>
</head>
<body>

    
    <?php echo $uploaded_image_tag; ?>

    <h1>Create Repair Request</h1>

    <div class="form-box">
        <?php echo $message; ?>

        <form method="POST" enctype="multipart/form-data">
            
            <label>Device Category:</label>
            <select name="device_type" required>
                <option value="">-- Select Category --</option>
                <option value="Laptop">Laptop</option>
                <option value="Smartphone">Smartphone</option>
                <option value="Desktop PC">Desktop PC</option>
                <option value="Tablet">Tablet</option>
                <option value="Others">Others</option>
            </select>

            <label>Device Name:</label>
            <input type="text" name="device_name" placeholder="e.g. HP Laptop, iPhone 11" required>

            <label>Problem Description:</label>
            <textarea name="problem_desc" rows="4" placeholder="Describe the problem..." required></textarea>

            <label>Upload Problem Image (Optional):</label>
            <input type="file" name="problem_image" accept="image/*">

            <label>Set Location:</label>
            <input type="text" name="location" placeholder="e.g. Dhanmondi, Dhaka" required>

            <div style="margin-top: 12px;">
                <label style="display: inline; font-weight: normal; cursor: pointer;">
                    <input type="checkbox" name="is_urgent" value="1"> 
                    <b style="color: #dc2626;">Mark request as Urgent</b>
                </label>
            </div>

            <div class="btn-container">
                <input type="submit" name="submit" value="Submit Request" class="btn-submit">
                <a href="customerdashboard.php" class="btn-back">Back</a>
            </div>
        </form>
    </div>

    <p align="center" style="color: #64748b; font-size: 13px; margin-top: 30px; clear: both;">
        Copyright &copy; <?php echo date("Y"); ?> FixLink
    </p>

</body>
</html>