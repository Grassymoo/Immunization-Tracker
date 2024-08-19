<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "ITDB");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_info'])) {
    // Retrieve form data
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];

    // Validate and sanitize input
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $phone_number = htmlspecialchars($phone_number);

    // Update user information
    $stmt = $conn->prepare("UPDATE users SET email = ?, phone_number = ? WHERE id = ?");
    $stmt->bind_param("ssi", $email, $phone_number, $user_id);
    
    if ($stmt->execute()) {
        $message = "Information updated successfully.";
    } else {
        $message = "Error updating information: " . $conn->error;
    }

    $stmt->close();
}

// Prepare and execute query to get user details
$stmt = $conn->prepare("SELECT first_name, last_name, email, phone_number FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $email, $phone_number);
$stmt->fetch();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page - Immunization Tracker</title>
    <style>
        html, body {
            margin: 0;
            height: 100%;
        }
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom right, #d3eaf2, #c0c0c0) no-repeat fixed;
            background-size: cover;
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
        header nav a.active {
            color: #007acc;
        }
        .info-container {
            display: flex;
            flex-direction: column;
            margin-top: 5px;
            background-color: #e0f4fc;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            width: 70%;
            max-width: 900px;
            text-align: center;
        }
        .info-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .info-container .info-item {
            margin: 10px 0;
            font-size: 18px;
            color: #333;
        }
        .info-container form label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        .info-container form input {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .info-container form button {
            background-color: #78c1e3;
            border: none;
            color: white;
            font-size: 16px;
            cursor: pointer;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .info-container form button:hover {
            background-color: #56a0d3;
        }
        .info-container form p {
            margin-top: 15px;
            color: #333;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="symbol.png" alt="Logo">
        </div>
        <nav>
            <a href="home.php">Home</a>
            <a href="healthcareProviders.php">Healthcare Providers</a>
            <a href="statistics.php">Statistics</a>
            <a href="profile.php" class="active">Profile Page</a>
            <a href="contact.php">Contact Us</a>
        </nav>
    </header>

    <div class="info-container">
        <h2>Basic Information</h2>
        <div class="info-item">First Name: <?php echo htmlspecialchars($first_name); ?></div>
        <div class="info-item">Last Name: <?php echo htmlspecialchars($last_name); ?></div>
        <div class="info-item">Email: <?php echo htmlspecialchars($email); ?></div>
        <div class="info-item">Phone Number: <?php echo htmlspecialchars($phone_number); ?></div>
        
        <h2>Update Information</h2>
        <form method="post" action="">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            
            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($phone_number); ?>" required>
            
            <button type="submit" name="update_info">Update Information</button>
        </form>
        <?php if (isset($message)): ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
