<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Healthcare Providers - Immunization Tracker</title>
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

/* Search Bar Styling */
.search-bar-container {
    margin-bottom: 20px;
}

.search-bar-container input[type="text"] {
    width: 80%;
    padding: 15px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    margin-right: 10px;
}

.search-bar-container button {
    padding: 15px 30px;
    font-size: 16px;
    background-color: #78c1e3;
    border: none;
    color: white;
    cursor: pointer;
    border-radius: 5px;
}

.search-bar-container button:hover {
    background-color: #56a0d3;
}

#results {
    margin-top: 20px;
    background-color: #e0f4fc;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
    text-align: left;
}

#results h3 {
    margin-top: 0;
    font-size: 20px;
    color: #333;
}

#results p {
    font-size: 16px;
    color: #555;
    margin: 5px 0;
}

.provider-list {
    list-style: none;
    padding: 0;
}

.provider-list li {
    margin-bottom: 10px;
    padding: 10px;
    background-color: #ffffff;
    border: 1px solid #ccc;
    border-radius: 5px;
}

.provider-list li h4 {
    margin: 0;
    font-size: 18px;
    color: #007acc;
}

.provider-list li p {
    margin: 5px 0;
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

#scrollUpBtn:hover {
    background-color: #56a0d3;
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
            <a href="healthcareProviders.php" class="active">Healthcare Providers</a>
            <a href="statistics.php">Statistics</a>
            <a href="profile.php">Profile Page</a>
            <a href="contact.php">Contact Us</a>
        </nav>
    </header>

    <div class="info-container">
        <h2>List of Certified Healthcare Providers</h2>

        <!-- Search Bar -->
        <div class="search-bar-container">
            <form method="GET" action="healthcareProviders.php">
                
                <input type="text" name="search_query" placeholder="Search by provider name..." value="<?php echo isset($_GET['search_query']) ? htmlspecialchars($_GET['search_query']) : ''; ?>" required>
                <button type="submit">Search</button>
            </form>
        </div>

        <!-- Results will be displayed here -->
        <div id="results">
        <?php
            // Database connection
            $conn = new mysqli("localhost", "root", "", "ITDB");

            // Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Retrieve search query
            $search_query = isset($_GET['search_query']) ? $conn->real_escape_string($_GET['search_query']) : '';

            // Display results only if there's a search query
            if ($search_query) {
                // Fetch data from the "healthcare_providers" table
                $sql = "SELECT * FROM healthcare_providers WHERE provider_name LIKE '%$search_query%'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo "<h3>Search Results:</h3>";
                    echo "<ul class='provider-list'>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<li>";
                        echo "<h4>" . htmlspecialchars($row['provider_name']) . "</h4>";
                        echo "<p><strong>Address:</strong> " . htmlspecialchars($row['address']) . "</p>";
                        echo "<p><strong>Phone:</strong> " . htmlspecialchars($row['phone_number']) . "</p>";
                        echo "<p><strong>Email:</strong> " . htmlspecialchars($row['email']) . "</p>";

                        echo "</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No providers found matching your search query.</p>";
                }
            } else {
                // Display a default message or nothing if no search query
                echo "<p>Please enter a search query to see results.</p>";
            }

            $conn->close();
        ?>
        </div>
    </div>
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
