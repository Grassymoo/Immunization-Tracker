<?php
// Start session
session_start();

$conn = new mysqli("localhost", "root", "", "ITDB");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email'])) {
        // Process the forgot password request
        $email = $_POST['email'];

        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Generate a unique token
            $token = bin2hex(random_bytes(50));
            $stmt->bind_result($user_id);
            $stmt->fetch();

            // Store token and expiration time in the database
            $expires = date("U") + 1800; // 30 minutes from now
            $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $user_id, $token, $expires);
            $stmt->execute();

            // Send email with password reset link
            $reset_link = "http://localhost/forgot_password.php?token=" . $token;
            $subject = "Password Reset Request";
            $message = "Click the following link to reset your password: " . $reset_link;
            $headers = "From: no-reply@yourdomain.com\r\n";
            mail($email, $subject, $message, $headers);

            echo "A password reset link has been sent to your email.";
        } else {
            echo "No user found with that email address.";
        }

        $stmt->close();
    } elseif (isset($_POST['token'])) {
        // Process the reset password request
        $token = $_POST['token'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            echo "Passwords do not match!";
            exit();
        }

        // Validate token
        $stmt = $conn->prepare("SELECT user_id, expires FROM password_resets WHERE token = ? AND expires > ?");
        $stmt->bind_param("si", $token, date("U"));
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $expires);
            $stmt->fetch();

            // Update password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $hashed_password, $user_id);
            $stmt->execute();

            // Remove the reset token
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();

            echo "Password has been reset successfully.";
        } else {
            echo "Invalid or expired token.";
        }

        $stmt->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Immunization Tracker</title>
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
            max-width: 400px; /* Optional: Set a maximum width for better alignment */
        }
        .container h1 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #5c5c5c;
        }
        .container input, .container button {
            width: calc(100% - 20px); /* Adjust width to fit within padding */
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box; /* Ensures padding and border are included in the width */
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
            width: 250px;
            height: 100px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="logo-no-background.png" alt="Logo">
        </div>
        <?php if (!isset($_GET['token'])): ?>
            <!-- Forgot Password Form -->
            <form method="post" action="">
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit">Send Reset Link</button>
            </form>
            <a href="login.php">Back to Login</a>
        <?php else: ?>
            <!-- Reset Password Form -->
            <form method="post" action="">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">
                <input type="password" name="password" placeholder="New Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit">Reset Password</button>
            </form>
            <a href="login.php">Back to Login</a>
        <?php endif; ?>
    </div>
</body>
</html>
