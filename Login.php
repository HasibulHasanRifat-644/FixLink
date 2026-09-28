<?php
session_start();
include("db.php");

$userid_email = "";
$password = "";

$userid_emailError = "";
$passwordError = "";
$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $userid_email = $_POST["userid_email"];
    $password = $_POST["password"];

    if(empty($userid_email)) {
        $userid_emailError = "Enter your User ID or Email";
    }

    if(empty($password)) {
        $passwordError = "Enter your Password";
    }

    if($userid_emailError == "" && $passwordError == "") {
        
        $check = "SELECT * FROM users WHERE (email='$userid_email' OR ID='$userid_email') AND pass='$password'";
        $result = mysqli_query($conn, $check);

        if(mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $_SESSION["userid_email"] = $userid_email;
            $_SESSION["role"] = $row['role']; 
            
            if ( $row['ID'] == '1') {
                header("Location: Admin.php");
            } elseif ( $row['role'] == 'technician') {
                header("Location: Technician.php");
            } elseif ( $row['role'] == 'customer') {
                header("Location: Customer.php");
            } elseif ( $row['role'] == 'equipment') {
                header("Location: Equipment_Owner.php");
            } elseif ( $row['role'] == 'supplier') {
                header("Location: Part_Supplier.php");
            } else {
                header("Location: Registration.php"); 
            }
          
        } else { 
            $message = "Incorrect Credentials";
        }
    }
}
?>
<!DOCTYPE html>
<head>
    <title>FixLink Login</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f4f4f9;
            color: #000;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
      
        .login-card {
            background: #fff;
            width: 100%;
            max-width: 400px;
            padding: 40px 30px;
            border: 1px solid #333;
            border-radius: 5px;
        }

        .brand {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-placeholder {
            border: 1px solid #999;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: inline-block;
            text-align: center;
            font-size: 12px;
            line-height: 45px;
            margin-bottom: 15px;
            color: #555;
        }

        .brand h2 {
            font-size: 22px;
            font-weight: normal;
            margin-bottom: 5px;
        }

        .brand p {
            font-size: 14px;
            color: #555;
        }
  
        .server-error {
            display: block;
            margin-top: 15px;
            padding: 10px;
            border: 1px solid red;
            background: #fff0f0;
            color: red;
            font-size: 13px;
            border-radius: 4px;
        }
       
        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            display: block;
            font-size: 14px;
            color: #333;
            margin-bottom: 8px;
        }

        .input-group input[type="text"],
        .input-group input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            background-color: #fff;
        }

        .input-group input:focus {
            border-color: #333;
            outline: none;
        }

        .show-pass-container {
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .show-pass-container input[type="checkbox"] {
            cursor: pointer;
            accent-color: #333;
        }

        .show-pass-container label {
            font-size: 13px;
            color: #555;
            cursor: pointer;
            margin-bottom: 0 !important;
        }

        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
            display: block;
        }

        .forgot-link {
            text-align: right;
            margin-bottom: 25px;
        }

        .forgot-link a {
            color: #555;
            font-size: 13px;
            text-decoration: none;
        }

        .forgot-link a:hover {
            color: #000;
            text-decoration: underline;
        }
      
        .login-btn {
            width: 100%;
            padding: 12px;
            border: 1px solid #333;
            border-radius: 4px;
            background: #fff;
            color: #000;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .login-btn:hover {
            background: #eee;
        }

        .login-btn:active {
            transform: scale(0.99);
        }
    
        .register-link {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: #555;
        }

        .register-link a {
            color: #000;
            text-decoration: underline;
        }

        .register-link a:hover {
            color: #333;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="brand">
            <h2>FixLink</h2>
            <p>Log in to your account</p>
            <?php if(!empty($message)): ?>
                <span class="server-error"><?php echo $message; ?></span>
            <?php endif; ?>
        </div>

        <form method="POST" action="">
            <div class="input-group">
                <label>User ID / Email</label>
                <input type="text" name="userid_email" value="<?php echo htmlspecialchars($userid_email); ?>" autocomplete="off">
                <?php if(!empty($userid_emailError)): ?>
                    <span class="error-message"><?php echo $userid_emailError; ?></span>
                <?php endif; ?>
            </div>

            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" id="passwordInput">
                
                <div class="show-pass-container">
                    <input type="checkbox" id="showPassCheckbox" onclick="togglePasswordVisibility()">
                    <label for="showPassCheckbox">Show password</label>
                </div>

                <?php if(!empty($passwordError)): ?>
                    <span class="error-message"><?php echo $passwordError; ?></span>
                <?php endif; ?>
            </div>

            <div class="forgot-link">
                <a href="forgot_password.php">Forgot password?</a>
            </div>

            <button type="submit" class="login-btn">Log in</button>

            <div class="register-link">
                No account? <a href="registration.php">Register</a>
            </div>
        </form>
    </div>

    <script>
        function togglePasswordVisibility() {
            var passwordField = document.getElementById("passwordInput");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>

</body>
</html>