<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccine Records - Immunization Tracker</title>
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
        .info-container {
            display: flex;
            flex-direction: column;
            margin-top: 50px;
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
        .info-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-container table th, .info-container table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
            font-size: 16px;
        }
        .info-container table th {
            background-color: #d3eaf2;
            color: #333;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="symbol.png"Logo">
        </div>
        <nav>
            <a href="http://localhost/home.php">Home</a>
            <a href="http://localhost/healthcareProviders.php">Healthcare Providers</a>
            <a href="http://localhost/statistics.php">Statistics</a>
            <a href="http://localhost/profile.php" class="active">Profile Page</a>
            <a href="http://localhost/contact.php">Contact Us</a>
        </nav>
    </header>
    <div class="info-container">
        <h2>Vaccine Records</h2>
        <table>
            <tr>
                <th>Vaccine Name</th>
                <th>Date Administered</th>
                <th>Healthcare Provider</th>
            </tr>
            <tr>
                <td>COVID-19 (Pfizer)</td>
                <td>2023-08-01</td>
                <td>Dr. John Doe</td>
            </tr>
            <tr>
                <td>Flu Shot</td>
                <td>2022-11-15</td>
                <td>Health Clinic A</td>
            </tr>
            <tr>
                <td>MMR</td>
                <td>2020-05-21</td>
                <td>Dr. Jane Smith</td>
            </tr>
        </table>
    </div>
</body>
</html>
