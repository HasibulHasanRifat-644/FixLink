<?php
session_start();
include('db.php');

$cust_id = $_SESSION["id"] ?? 1;
$selected_receiver = $_GET['receiver'] ?? 'Technician';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_message'])) {
    $receiver_type = mysqli_real_escape_string($conn, $_POST['receiver_type']);
    $message_text  = mysqli_real_escape_string($conn, trim($_POST['message_text']));

    if (!empty($message_text)) {
        $sql = "INSERT INTO messages (sender_id, sender_role, receiver_type, message, sent_at) 
                VALUES ('$cust_id', 'Customer', '$receiver_type', '$message_text', NOW())";
        mysqli_query($conn, $sql);
        
        header("Location: chat.php?receiver=" . urlencode($receiver_type));
        exit();
    }
}

$sql_msg = "SELECT * FROM messages 
            WHERE (sender_id=$cust_id AND receiver_type='$selected_receiver') 
               OR (sender_role='$selected_receiver' AND receiver_type='Customer') 
            ORDER BY sent_at ASC";
$result_msg = mysqli_query($conn, $sql_msg);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Live Chat - FixLink</title>
</head>
<body bgcolor="#f4f7f9" style="font-family: Arial, sans-serif;">

<h1 align="center" style="color: #1e3a8a;">Chat Support</h1>
<hr color="#cbd5e1">

<table align="center" width="600" cellpadding="20" bgcolor="#ffffff" style="border: 1px solid #e2e8f0; border-radius: 8px;">
    <tr>
        <td>
            <b>Select Chat With: </b>
            <a href="chat.php?receiver=Technician">
                <input type="button" value="Technician" style="background-color: <?php echo ($selected_receiver == 'Technician') ? '#475569' : '#f1f5f9'; ?>; color: <?php echo ($selected_receiver == 'Technician') ? '#ffffff' : '#334155'; ?>; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 4px; cursor: pointer;">
            </a>
            <a href="chat.php?receiver=Parts Supplier">
                <input type="button" value="Parts Supplier" style="background-color: <?php echo ($selected_receiver == 'Parts Supplier') ? '#475569' : '#f1f5f9'; ?>; color: <?php echo ($selected_receiver == 'Parts Supplier') ? '#ffffff' : '#334155'; ?>; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 4px; cursor: pointer;">
            </a>
            
            <br><br>

            <table border="1" cellpadding="10" width="100%" style="border-collapse: collapse; border-color: #e2e8f0;">
                <tr bgcolor="#f8fafc">
                    <th align="left" style="color: #1e3a8a;">Chat with: <?php echo htmlspecialchars($selected_receiver); ?></th>
                </tr>
                <tr>
                    <td height="250" valign="top" bgcolor="#ffffff">
                        <?php
                        if ($result_msg && mysqli_num_rows($result_msg) > 0) {
                            while ($row = mysqli_fetch_assoc($result_msg)) {
                                $is_me = ($row['sender_role'] == 'Customer');
                                
                                if ($is_me) {
                                    echo "<div align='right' style='margin: 8px 0;'>";
                                    echo "<span style='background-color: #f1f5f9; color: #1e3a8a; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 6px; display: inline-block; max-width: 75%; text-align: left;'>";
                                    echo "<b>You:</b> " . htmlspecialchars($row['message']);
                                    echo "</span>";
                                    echo "</div>";
                                } else {
                                    echo "<div align='left' style='margin: 8px 0;'>";
                                    echo "<span style='background-color: #ffffff; color: #334155; border: 1px solid #e2e8f0; padding: 6px 12px; border-radius: 6px; display: inline-block; max-width: 75%; text-align: left;'>";
                                    echo "<b>" . htmlspecialchars($row['sender_role']) . ":</b> " . htmlspecialchars($row['message']);
                                    echo "</span>";
                                    echo "</div>";
                                }
                            }
                        } else {
                            echo "<p align='center' style='color: #64748b; margin-top: 100px;'>No conversation yet. Send a message below!</p>";
                        }
                        ?>
                    </td>
                </tr>
                <tr bgcolor="#f8fafc">
                    <td>
                        <form method="POST" action="chat.php?receiver=<?php echo urlencode($selected_receiver); ?>">
                            <input type="hidden" name="receiver_type" value="<?php echo htmlspecialchars($selected_receiver); ?>">
                            <input type="text" name="message_text" placeholder="Type your message here..." style="width: 78%; padding: 7px; border: 1px solid #cbd5e1; border-radius: 4px;" required>
                            <input type="submit" name="send_message" value="Send" style="background-color: #475569; color: #ffffff; border: none; padding: 7px 15px; border-radius: 4px; cursor: pointer;">
                        </form>
                    </td>
                </tr>
            </table>

            <br>
            <a href="customerdashboard.php">
                <input type="button" value="Back" style="background-color: #475569; color: #ffffff; border: none; padding: 8px 18px; border-radius: 5px; cursor: pointer;">
            </a>
        </td>
    </tr>
</table>

<br>
<p align="center" style="color: #64748b; font-size: 13px;">
    Copyright &copy; <?php echo date("Y"); ?> FixLink
</p>

</body>
</html>