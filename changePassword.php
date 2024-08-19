<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Immunization Tracker</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom right, #d3eaf2, #c0c0c0);
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        header {
            width: 100%;
            background: #d3eaf2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        header .logo img {
            width: 50px;
            height: 50px;
        }
        header nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #333;
            font-size: 16px;
        }
        header nav a:hover {
            text-decoration: underline;
        }
        .main-content {
            text-align: center;
            margin-top: 50px;
        }
        .main-content h1 {
            font-size: 36px;
            color: #333;
        }
        .main-content p {
            font-size: 18px;
            color: #666;
            margin-top: 10px;
        }
        .form-container {
            background-color: #e0f4fc;
            border: 3px solid #78c1e3;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px; /* Optional: Set a maximum width for better alignment */
            margin: 0 auto; /* Center the form */
        }
        .form-container input {
            width: calc(100% - 20px); /* Adjust width to fit within padding */
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box; /* Ensures padding and border are included in the width */
        }
        .form-container button {
            width: calc(100% - 20px); /* Match button width to inputs */
            padding: 10px;
            background-color: #78c1e3;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #56a0d3;
        }
        .form-container a {
            display: block;
            margin-top: 20px;
            color: #007acc;
            text-decoration: none;
        }
        .form-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="symbol.png" alt="Logo">
        </div>
        <nav>
            <a href="home.php" class="active">Home</a>
            <a href="healthcareProviders.php">Healthcare Providers</a>
            <a href="statistics.php">Statistics</a>
            <a href="profile.php">Profile Page</a>
            <a href="contact.php">Contact Us</a>
        </nav>
    </header>
    <div class="main-content">
        <div class="form-container">
            <h1>Change Password</h1>
            <form method="post" action="">
                <input type="password" name="current_password" placeholder="Current Password" required>
                <input type="password" name="new_password" placeholder="New Password" required>
                <input type="password" name="confirm_new_password" placeholder="Confirm New Password" required>
                <button type="submit">Change Password</button>
            </form>
            <a href="profile.php">Back to Profile</a>
        </div>
    </div>
</body>
</html>

<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "ITDB");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get form data
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];
    $user_id = $_SESSION['user_id'];

    // Validate new passwords
    if ($new_password !== $confirm_new_password) {
        echo "New passwords do not match!";
        $conn->close();
        exit();
    }

    // Query the user from the database
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();
    
    // Check if user exists
    if ($stmt->num_rows === 0) {
        echo "User not found!";
        $stmt->close();
        $conn->close();
        exit();
    }

    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->free_result();
    $stmt->close();

    // Verify current password
    if (!password_verify($current_password, $hashed_password)) {
        echo "Current password is incorrect!";
        $conn->close();
        exit();
    }

    // Hash the new password
    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Update the password in the database
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("si", $hashed_new_password, $user_id);
    if ($stmt->execute()) {
        echo "Password changed successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

