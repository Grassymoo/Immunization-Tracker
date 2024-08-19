<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "ITDB");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user details including profile picture URL
$user_id = $_SESSION['user_id'];
$sql = "SELECT first_name, last_name, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($first_name, $last_name, $profile_picture);
$stmt->fetch();
$stmt->close();
$conn->close();

// Set default profile picture URL if none exists
$profilePictureUrl = $profile_picture ? $profile_picture : 'https://via.placeholder.com/150';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Page - Immunization Tracker</title>
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
        header nav a.active {
            color: #007acc;
        }
        .profile-container {
            display: flex;
            margin-top: 50px;
            background-color: #e0f4fc;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            width: 70%;
            max-width: 1000px;
            align-items: flex-start;
        }
        .profile-container .profile-pic {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-right: 2px solid #007acc;
            padding-right: 30px;
        }
        .profile-container .profile-pic img {
            width: 150px;
            height: 150px;          
            object-fit: cover; 
        }
        
        .profile-container .profile-pic .username {
            margin-top: 20px;
            font-size: 22px;
            color: #333;
            font-weight: bold;
        }
        .profile-container .profile-pic form {
            margin-top: 20px;
            text-align: center;
        }
        .profile-container .profile-pic input[type="file"] {
            display: none;
        }
        .profile-container .profile-pic label {
            display: inline-block;
            padding: 12px 25px;
            font-size: 16px;
            border: 2px solid #007acc;
            border-radius: 5px;
            background-color: #007acc;
            color: white;
            cursor: pointer;
            text-align: center;
            margin-bottom: 10px;
        }
        .profile-container .profile-pic label:hover {
            background-color: #005f8a;
        }
        .profile-container .profile-pic button {
            padding: 12px 25px;
            font-size: 16px;
            border: 2px solid #007acc;
            border-radius: 5px;
            background-color: #007acc;
            color: white;
            cursor: pointer;
            display: block;
            margin: 10px auto 0;
        }
        .profile-container .profile-pic button:hover {
            background-color: #005f8a;
        }
        .profile-container .profile-details {
            flex: 2;
            margin-left: 30px;
        }
        .profile-container .profile-details ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
        }
        .profile-container .profile-details ul li {
            margin: 15px 0;
            font-size: 18px;
            border-bottom: 1px solid #d3eaf2;
            padding-bottom: 10px;
        }
        .profile-container .profile-details ul li a {
            text-decoration: none;
            color: #333;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        .profile-container .profile-details ul li a:hover {
            background-color: #d3eaf2;
            color: #007acc;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="symbol.png" alt="Logo">
        </div>
        <nav>
            <a href="http://localhost/home.php">Home</a>
            <a href="http://localhost/healthcareProviders.php">Healthcare Providers</a>
            <a href="http://localhost/statistics.php">Statistics</a>
            <a href="http://localhost/profile.php" class="active">Profile Page</a>
            <a href="http://localhost/contact.php">Contact Us</a>
        </nav>
    </header>
    <div class="profile-container">
        <div class="profile-pic">
            <!-- Display existing profile picture -->
            <img src="<?php echo htmlspecialchars($profilePictureUrl); ?>" alt="Profile Picture" id="profilePicture">
            <!-- Form to upload new profile picture -->
            <form action="uploadProfilePicture.php" method="post" enctype="multipart/form-data">
                <input type="file" name="profile_picture" accept="image/*" required id="fileInput">
                <label for="fileInput">Choose File</label>
                <button type="submit">Upload</button>
            </form>
            <div class="username">
                <?php echo htmlspecialchars($first_name . ' ' . $last_name); ?>
            </div>
        </div>
        <div class="profile-details">
            <ul>
                <li><a href="http://localhost/basicInfo.php">Basic Information</a></li>
                <li><a href="http://localhost/accountSettings.php">Account Settings</a></li>
                <li><a href="http://localhost/upcomingVaccines.php">Received & Upcoming Vaccines</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
