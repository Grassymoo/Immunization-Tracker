<?php
session_start();

// Check if the provider is logged in
if (!isset($_SESSION['provider_id'])) {
    // Redirect to login page if not logged in
    header("Location: provider_login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "ITDB");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get provider ID from session
$provider_id = $_SESSION['provider_id'];

// Fetch provider data from the database
$stmt = $conn->prepare("SELECT first_name FROM healthcare_providers_login WHERE id = ?");
$stmt->bind_param("i", $provider_id);
$stmt->execute();
$stmt->bind_result($first_name);
$stmt->fetch();
$stmt->close();
$conn->close();

// Get current hour
$hour = date('H');
if ($hour >= 05 && $hour < 12) {
    $greeting = "Good Morning, Dr. $first_name!";
} elseif ($hour >= 12 && $hour < 17) {
    $greeting = "Good Afternoon, Dr. $first_name!";
} else {
    $greeting = "Good Evening, Dr. $first_name!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Healthcare Provider Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom right, #d3eaf2, #c0c0c0);
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        header {
            width: 100%;
            background: #d3eaf2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: absolute;
            top: 0;
            z-index: 10; /* Make sure the header is above the particles */
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

        .main-content {
            text-align: center;
            z-index: 10;
        }

        .main-content h1 {
            font-size: 36px;
            color: #333;
            animation: fadeIn 2s ease-in-out;
        }

        .main-content p {
            font-size: 18px;
            color: #666;
            margin-top: 10px;
            animation: fadeIn 2s ease-in-out .005s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Particle Effects */
        canvas {
            position: fixed;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 0; /* Ensure the canvas is behind the header */
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="symbol.png" alt="Logo">
        </div>
        <nav>
            <a href="providerHome.php" class="active">Home</a>
            <a href="appointments.php">Appointments</a>
            <a href="providerLogout.php">Logout</a>
        </nav>
    </header>
    
    <div class="main-content">
        <h1><?php echo $greeting; ?></h1>
        <h1>Welcome to Your Dashboard!</h1>
        <p>Manage your appointments, track immunization records, and provide better healthcare!</p>
    </div>

    <canvas id="particleCanvas"></canvas>

    <script>
        // Particle effect
        const canvas = document.getElementById('particleCanvas');
        const ctx = canvas.getContext('2d');
        let particlesArray = [];

        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        window.addEventListener('resize', function() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });

        class Particle {
            constructor() {
                this.x = Math.random() * canvas.width;
                this.y = Math.random() * canvas.height;
                this.size = Math.random() * 5 + 1;
                this.speedX = (Math.random() * 1 - 0.5); // Slower speed
                this.speedY = (Math.random() * 1 - 0.5); // Slower speed
                this.color = 'rgba(255, 255, 255, 0.8)';
            }

            update() {
                this.x += this.speedX;
                this.y += this.speedY;

                // Wrap particles when they go off-screen
                if (this.x > canvas.width) this.x = 0;
                if (this.x < 0) this.x = canvas.width;
                if (this.y > canvas.height) this.y = 0;
                if (this.y < 0) this.y = canvas.height;

                this.size *= 0.99; // Shrink the particle over time
                if (this.size < 0.5) this.size = Math.random() * 5 + 1; // Reset size after it shrinks too much
            }

            draw() {
                ctx.fillStyle = this.color;
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                ctx.fill();
            }
        }

        function createParticles() {
            for (let i = 0; i < 100; i++) { // Adjust number of particles here
                particlesArray.push(new Particle());
            }
        }

        function handleParticles() {
            for (let i = 0; i < particlesArray.length; i++) {
                particlesArray[i].update();
                particlesArray[i].draw();
            }
        }

        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            handleParticles();
            requestAnimationFrame(animate);
        }

        createParticles(); // Create initial set of particles
        animate();
    </script>
</body>
</html>
