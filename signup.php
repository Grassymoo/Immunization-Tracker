<?php
if (isset($_POST["s"])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Database connection
        $conn = new mysqli("localhost", "root", "", "ITDB");

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Get form data
        $first_name = $_POST['FirstName'];
        $last_name = $_POST['LastName'];
        $email = $_POST['Email'];
        $phone_number = $_POST['PhoneNumber'];
        $password = $_POST['password'];
        $confirm_password = $_POST['ConfirmPassword'];

        // Validate input
        if ($password != $confirm_password) {
            header('Location: signup.php?fail=password_mismatch');
            exit();
        }

        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Email already exists, redirect with an error
            header('Location:signup.php?fail=email_exists');
            exit();
        }

        $stmt->close();

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, phone_number, password) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $first_name, $last_name, $email, $phone_number, $hashed_password);

        // Execute and check
        if ($stmt->execute()) {
            header('Location:login.php');
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Immunization Tracker</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom right, #d3eaf2, #c0c0c0);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background-color: #e0f4fc;
            border: 3px solid #78c1e3;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }
        .container h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #5c5c5c;
        }
        .container input, .container button {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .container button {
            background-color: #78c1e3;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .container button:hover {
            background-color: #56a0d3;
        }
        .container a {
            display: block;
            margin-top: 20px;
            color: #007acc;
            text-decoration: none;
        }
        .container a:hover {
            text-decoration: underline;
        }
        .logo {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 20px;
        }
        .logo img {
            width: 290px;
            height: 100px;
        }
        .strength-meter-wrapper {
            width: calc(100% - 20px); /* Match the width of the input fields */
            margin: 10px 0;
            text-align: center;
            position: relative;
            margin: 0 auto; /* Center the wrapper */
        }
        .strength-meter {
            height: 8px;
            background-color: #e0e0e0;
            border-radius: 5px;
            overflow: hidden;
            position: relative;
            width: 100%; /* Ensure it takes full width of its parent */
        }
        .strength-meter span {
            display: block;
            height: 100%;
            width: 0;
            border-radius: 5px;
            position: absolute;
            top: 0;
            left: 0;
            transition: width 0.3s ease;
        }
        .strength-meter.weak span {
            background-color: #ff4c4c;
        }
        .strength-meter.fair span {
            background-color: #ffcc00;
        }
        .strength-meter.good span {
            background-color: #4caf50;
        }
        .strength-meter.strong span {
            background-color: #009688;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="logo-no-background.png" alt="Logo">
        </div>
        <form method="post" action="">
        <?php
            if (isset($_GET['fail'])) {
                if ($_GET['fail'] == 'password_mismatch') {
                    echo "<h1>Passwords don't match!</h1>";
                } elseif ($_GET['fail'] == 'email_exists') {
                    echo "<h1>Email already exists!</h1>";
                }
            }
            ?>
            <input type="text" name="FirstName" placeholder="First Name" required>
            <input type="text" name="LastName" placeholder="Last Name" required>
            <input type="email" name="Email" placeholder="Email" required>
            <input type="text" name="PhoneNumber" placeholder="Phone Number" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            <div class="strength-meter-wrapper">
                <div class="strength-meter" id="strength-meter">
                    <span id="strength-bar"></span>
                </div>
            </div>
            <input type="password" name="ConfirmPassword" placeholder="Confirm Password" required>
            <button type="submit" name="s">Sign Up</button>
        </form>
        <a href="login.php">Have an account? Login</a>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const strengthMeter = document.getElementById('strength-meter');
        const strengthBar = document.getElementById('strength-bar');

        const checkPasswordStrength = (password) => {
            let strength = 0;
            if (password.length >= 8) strength += 1;
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[a-z]/.test(password)) strength += 1;
            if (/\d/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;

            return strength;
        };

        passwordInput.addEventListener('input', () => {
            const password = passwordInput.value;
            const strength = checkPasswordStrength(password);

            let strengthClass;
            let width;

            switch (strength) {
                case 1:
                case 2:
                    strengthClass = 'weak';
                    width = '25%';
                    break;
                case 3:
                    strengthClass = 'fair';
                    width = '50%';
                    break;
                case 4:
                    strengthClass = 'good';
                    width = '75%';
                    break;
                case 5:
                    strengthClass = 'strong';
                    width = '100%';
                    break;
                default:
                    strengthClass = '';
                    width = '0';
                    break;
            }

            strengthMeter.className = `strength-meter ${strengthClass}`;
            strengthBar.style.width = width;
        });
    </script>
</body>
</html>








