<?php
include("db.php");

$fullname = "";
$email = "";
$phone = "";
$address = "";
$password = "";
$role = "";

$fullnameError = "";
$emailError = "";
$phoneError = "";
$addressError = "";
$passwordError = "";
$roleError = "";
$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $fullname = $_POST["fullname"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $password = $_POST["password"];
    $role = $_POST["role"] ?? "";

    if(empty($fullname)) {
        $fullnameError = "Enter Name";
    }

    if(empty($email)) {
        $emailError = "Enter Email";
    }

    if(empty($phone)) {
        $phoneError = "Enter Phone";
    }

    if(empty($address)) {
        $addressError = "Enter Address";
    }

    if(empty($password)) {
        $passwordError = "Enter Password";
    }

    if(empty($role)) {
        $roleError = "Select a role";
    }

    if($fullnameError == "" && $emailError == "" && $phoneError == "" && $addressError == "" && $passwordError == "" && $roleError == "") {
        
        $check = "SELECT * FROM users WHERE email='$email'"; 
        $result = mysqli_query($conn, $check);

        if(mysqli_num_rows($result) > 0) {
            $message = "User Already Exists";
        } else {
            $sql = "INSERT INTO users (role, name, email, pass, phone, address, status) 
            VALUES ('$role', '$fullname', '$email', '$password', '$phone', '$address', 'pending')";

            if(mysqli_query($conn, $sql)) {
                $message = "Registration Successful";
                $fullname = $email = $phone = $address = $role = "";
            } else {
                $message = "Registration Failed";
            }
        }
    }
}
?>

<!DOCTYPE html>
<head>
    <title>FixLink Registration</title>
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

        .register-card {
            background: #fff;
            width: 100%;
            max-width: 480px;
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

        .status-message {
            display: block;
            margin-top: 15px;
            padding: 10px;
            border-radius: 4px;
            font-size: 13px;
            text-align: center;
        }

        .status-message.success {
            border: 1px solid green;
            background: #f0fff0;
            color: green;
        }

        .status-message.error {
            border: 1px solid red;
            background: #fff0f0;
            color: red;
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
            outline: none;
        }

        .input-group input:focus {
            border-color: #333;
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

        .role-section {
            margin-bottom: 25px;
        }

        .role-section label.section-title {
            display: block;
            font-size: 14px;
            color: #333;
            margin-bottom: 8px;
        }

        .role-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .role-option {
            border: 1px solid #ccc;
            background-color: #fff;
            border-radius: 4px;
            padding: 8px 12px;
            font-size: 13px;
            color: #333;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .role-option:hover {
            border-color: #333;
        }

        .role-option input[type="radio"] {
            accent-color: #333;
            cursor: pointer;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            border: 1px solid #333;
            border-radius: 4px;
            background: #fff;
            color: #000;
            font-size: 15px;
            font-weight: normal;
            cursor: pointer;
            transition: background 0.2s;
        }

        .submit-btn:hover {
            background: #eee;
        }

        .submit-btn:active {
            transform: scale(0.99);
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: #555;
        }

        .login-link a {
            color: #000;
            text-decoration: underline;
        }

        .login-link a:hover {
            color: #333;
        }
    </style>
</head>
<body>

    <div class="register-card">
        
        <div class="brand">
            <h2>FixLink</h2>
            <p>Create your account</p>
            
            <?php if (!empty($message)): ?>
                <span class="status-message <?php echo ($message == 'Registration Successful') ? 'success' : 'error'; ?>">
                    <?php echo $message; ?>
                </span>
            <?php endif; ?>
        </div>

        <form method="POST" action="">
            
            <div class="input-group">
                <label>Name</label>
                <input type="text" name="fullname" value="<?php echo htmlspecialchars($fullname); ?>">
                <?php if(!empty($fullnameError)): ?>
                    <span class="error-message"><?php echo $fullnameError; ?></span>
                <?php endif; ?>
            </div>

            <div class="input-group">
                <label>Email</label>
                <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <?php if(!empty($emailError)): ?>
                    <span class="error-message"><?php echo $emailError; ?></span>
                <?php endif; ?>
            </div>

            <div class="input-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>">
                <?php if(!empty($phoneError)): ?>
                    <span class="error-message"><?php echo $phoneError; ?></span>
                <?php endif; ?>
            </div>

            <div class="input-group">
                <label>Address</label>
                <input type="text" name="address" value="<?php echo htmlspecialchars($address); ?>">
                <?php if(!empty($addressError)): ?>
                    <span class="error-message"><?php echo $addressError; ?></span>
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
            
            <div class="role-section">
                <label class="section-title">I am a</label>
                <div class="role-grid">
                    <label class="role-option">
                        <input type="radio" name="role" value="customer" <?php if($role == 'customer') echo 'checked'; ?>> Customer
                    </label>

                    <label class="role-option">
                        <input type="radio" name="role" value="technician" <?php if($role == 'technician') echo 'checked'; ?>> Technician
                    </label>

                    <label class="role-option">
                        <input type="radio" name="role" value="supplier" <?php if($role == 'supplier') echo 'checked'; ?>> Parts Supplier
                    </label>

                    <label class="role-option">
                        <input type="radio" name="role" value="equipment" <?php if($role == 'equipment') echo 'checked'; ?>> Equipment Owner
                    </label>
                </div>
                <?php if(!empty($roleError)): ?>
                    <span class="error-message"><?php echo $roleError; ?></span>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="submit-btn">Create Account</button>
      
            <div class="login-link">
                Already have an account? <a href="login.php">Log in</a>
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