<?php
include("db.php");

$email = "";
$emailError = "";
$message = "";
$messageColor = "red";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    
 
    $email = $_POST["email"] ?? "";

 
    if(empty($email)) {
        $emailError = "Please enter your registered email address";
    }

   
    if($emailError == "") {
        
        
        $check = "SELECT * FROM users WHERE email='$email'"; 
        $result = mysqli_query($conn, $check);

        if(mysqli_num_rows($result) > 0) {
            
            $message = "A recovery link has been sent to your email address.";
            $messageColor = "green";
            $email = ""; 
        } else {
            $message = "No account found with that email address.";
            $messageColor = "red";
        }
    }
}
?>

<!DOCTYPE html>
<head>
    <title>FixLink Account Recovery</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f9; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px;">

   
    <div style="background-color: #fff; width: 100%; max-width: 800px; border: 1px solid #ccc; padding: 30px; display: flex; flex-wrap: wrap; gap: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">

        
        <div style="flex: 1; min-width: 300px; border: 1px solid #333; display: flex; justify-content: center; align-items: center; min-height: 400px;">
            <div style="border: 1px solid #333; padding: 5px 15px;">Image</div>
        </div>

        
        <div style="flex: 1; min-width: 300px; display: flex; flex-direction: column; justify-content: center;">
            
            <div style="text-align: center; margin-bottom: 25px;">
                <h2 style="margin: 0 0 5px 0; font-size: 24px; font-weight: normal;">FixLink</h2>
                <p style="margin: 0; font-size: 14px; color: #555;">Account Recovery</p>
                <p style="margin: 10px 0 0 0; font-size: 12px; color: #777;">Enter your registered email address to receive your User ID and a password reset link.</p>
                
          
                <span style="display: block; margin-top: 15px; font-size: 14px; font-weight: bold; color: <?php echo $messageColor; ?>;">
                    <?php echo $message; ?>
                </span>
            </div>

            <form method="POST" action="">
                
               
                <div style="margin-bottom: 25px;">
                    <label style="display: block; margin-bottom: 5px; font-size: 14px;">Email Address</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
                    <span style="color:red; font-size: 12px;"><?php echo $emailError; ?></span>
                </div>

         
                <div style="text-align: center; margin-bottom: 20px;">
                    <button type="submit" style="padding: 8px 30px; border: 1px solid #333; background-color: #fff; cursor: pointer; font-size: 14px;">Send Recovery Link</button>
                </div>

          
                <div style="text-align: center;">
                    <p style="margin: 0; font-size: 13px;">Remember your details? <a href="login.php" style="color: #000; text-decoration: none;">Back to Log in</a></p>
                </div>

            </form>
        </div>
    </div>

</body>
</html>