<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Default values
$selectedChartType = isset($_POST['chart-type']) ? $_POST['chart-type'] : 'pie';
$selectedYear = isset($_POST['year']) ? $_POST['year'] : '2024';

// Database connection
$conn = new mysqli("localhost", "root", "", "ITDB");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data
$sql = "SELECT data FROM statistics WHERE chart_type = ? AND year = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $selectedChartType, $selectedYear);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$conn->close();

// Prepare data for JavaScript
$chartData = $data ? $data['data'] : '{}';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics - Immunization Tracker</title>
    <style>
    body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: linear-gradient(to bottom right, #d3eaf2, #c0c0c0);
    height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    overflow-x: hidden;
}

/* Scroll Up Button Styling */
#scrollUpBtn {
    position: fixed;
    bottom: 20px;
    right: 20px;
    display: none; /* Hidden by default */
    background-color: #78c1e3;
    color: white;
    border: none;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
    transition: opacity 0.3s;
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
            padding-bottom: 170px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            width: 70%;
            max-width: 900px;
            text-align: center;
            max-height: 50vh; /* Set a max-height */
            margin-bottom: 50px;
            
        }
        .info-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .info-container img {
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin-top: 20px;
        }
        .controls {
            margin-bottom: 30px;
        }
        .controls label {
            font-size: 16px;
            color: #333;
            margin-right: 10px;
        }
        .controls select {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 20px;
        }
        .controls button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            background-color: #007acc;
            color: white;
            cursor: pointer;
        }
        .controls button:hover {
            background-color: #005f8a;
        }
        #myChart {
            height: 100px; /* Set a fixed height for the chart */
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
            <a href="http://localhost/statistics.php" class="active">Statistics</a>
            <a href="http://localhost/profile.php">Profile Page</a>
            <a href="http://localhost/contact.php">Contact Us</a>
        </nav>
    </header>
    <div class="info-container">
        <h2>Statistics</h2>
        <form method="post">
            <div class="controls">
                <label for="chart-type">Select Chart Type:</label>
                <select id="chart-type" name="chart-type">
                    <option value="pie" <?php echo ($selectedChartType === 'pie') ? 'selected' : ''; ?>>Pie Chart</option>
                    <option value="bar" <?php echo ($selectedChartType === 'bar') ? 'selected' : ''; ?>>Bar Chart</option>
                    <option value="line" <?php echo ($selectedChartType === 'line') ? 'selected' : ''; ?>>Line Chart</option>
                </select>
                
                <label for="year">Select Year:</label>
                <select id="year" name="year">
                    <option value="2024" <?php echo ($selectedYear === '2024') ? 'selected' : ''; ?>>2024</option>
                    <option value="2023" <?php echo ($selectedYear === '2023') ? 'selected' : ''; ?>>2023</option>
                    <option value="2022" <?php echo ($selectedYear === '2022') ? 'selected' : ''; ?>>2022</option>
                </select>
                
                <button type="submit">Update</button>
            </div>
        </form>

        <!-- Canvas for Chart.js -->
        <canvas id="myChart" width="400" height="200"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Get chart data from PHP
        const chartData = <?php echo json_encode($chartData); ?>;

        // Debug: Check if chartData is correctly set
        console.log('Chart data:', chartData);

        // Parse the JSON data
        let data;
        try {
            data = JSON.parse(chartData);
        } catch (e) {
            console.error('Error parsing chart data:', e);
            data = { labels: [], values: [] }; // Default to empty data on error
        }

        // Debug: Check parsed data
        console.log('Parsed data:', data);

        // Get the context of the canvas
        const ctx = document.getElementById('myChart').getContext('2d');

        // Create the chart
        new Chart(ctx, {
            type: '<?php echo $selectedChartType; ?>', // Chart type
            data: {
                labels: data.labels || [],
                datasets: [{
                    label: 'Data',
                    data: data.values || [],
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    <!-- Scroll Up Button -->
<button id="scrollUpBtn" title="Scroll to top">↑</button>
    <script>
    // Get the button
    const scrollUpBtn = document.getElementById('scrollUpBtn');

    // Show or hide the button based on scroll position
    window.onscroll = function() {
        if (document.body.scrollTop > 100 || document.documentElement.scrollTop > 100) {
            scrollUpBtn.style.display = 'block';
        } else {
            scrollUpBtn.style.display = 'none';
        }
    };

    // Scroll to the top when the button is clicked
    scrollUpBtn.onclick = function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };
</script>
</body>
</html>
