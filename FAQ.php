<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQs - Immunization Tracker</title>
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
        .faq-container {
            display: flex;
            flex-direction: column;
            margin-top: 50px;
            background-color: #e0f4fc;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            width: 70%;
            max-width: 900px;
            text-align: left;
        }
        .faq-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .faq-item {
            margin-bottom: 15px;
        }
        .faq-item h3 {
            font-size: 18px;
            color: #007acc;
            margin: 0;
        }
        .faq-item p {
            font-size: 16px;
            color: #333;
            margin: 5px 0;
        }
        .faq-item p:last-child {
            margin-bottom: 0;
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
            <a href="profile.php">Profile Page</a>
            <a href="contact.php">Contact Us</a>
        </nav>
    </header>
    <div class="faq-container">
    <h2>Frequently Asked Questions</h2>
    <div class="faq-item">
        <h3>How do I sign up for an account?</h3>
        <p>To sign up for an account, go to the "Sign Up" page . Enter your email and create a password and other information. You will need to confirm your password by re-entering it to complete the registration process.</p>
    </div>
    <div class="faq-item">
        <h3>How can I reset my password?</h3>
        <p>If you forget your password, click the "Forgot Password" link on the login page. Follow the instructions sent to your email to reset your password securely.</p>
    </div>
    <div class="faq-item">
        <h3>How can I update my profile information?</h3>
        <p>Once logged in, navigate to your profile page. Here, you can view and update your personal information. Click the "Basic info" button to go to the page where you can  make any changes.</p>
    </div>
    <div class="faq-item">
        <h3>Can I schedule a vaccination appointment?</h3>
        <p>Currently, scheduling vaccinations is not available. Please check back for updates or contact support for further assistance.</p>
    </div>
    <div class="faq-item">
        <h3>How can I contact support?</h3>
        <p>If you need help, go to the "Contact Us" page. You can either fill out the contact form or use the provided support email address to reach out to us.</p>
    </div>
</div>

</body>
</html>
