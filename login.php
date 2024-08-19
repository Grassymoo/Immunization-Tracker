<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Immunization Tracker</title>
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
            width: 290px;
            height: 100px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="logo-no-background.png" alt="Logo">
        </div>
        <form method="post" action="login.php">
            <input type="email" name="Email" placeholder="Email" required>
            <input type="password" name="Password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <a href="http://localhost/forgotPassword.php">Forgot Password?</a>
        <a href="signup.php">Don't have an account? Sign Up</a>
    </div>
</body>
</html>


<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $conn = new mysqli("localhost", "root", "", "ITDB");

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Get form data
    $email = $_POST['Email'];
    $password = $_POST['Password'];

    // Query the user from the database
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            header("Location: home.php");
            exit(); // Ensure no further code runs after redirection
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No user found with that email!";
    }

    $stmt->close();
    $conn->close();
}
?>


