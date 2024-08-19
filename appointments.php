<?php
session_start();

// Check if the provider is logged in
if (!isset($_SESSION['provider_id'])) {
    header("Location: provider_login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "ITDB");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle search
$search_results = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_id'])) {
    $search_id = $_POST['search_id'];

    $stmt = $conn->prepare("SELECT id, first_name, last_name, email FROM users WHERE id = ?");
    $stmt->bind_param("i", $search_id);
    $stmt->execute();
    $stmt->bind_result($id, $first_name, $last_name, $email);
    while ($stmt->fetch()) {
        $search_results[] = [
            'id' => $id,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email
        ];
    }
    $stmt->close();
    if (empty($search_results)) {
        $error_message = "User ID not found.";
    }
}

// Handle scheduling appointments
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['schedule_appointment'])) {
    $user_id = $_POST['user_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    
    // Insert appointment into the database
    $stmt = $conn->prepare("INSERT INTO appointments (user_id, provider_id, appointment_date, appointment_time) VALUES (?, ?, ?, ?)");
    $provider_id = $_SESSION['provider_id'];
    $stmt->bind_param("iiss", $user_id, $provider_id, $appointment_date, $appointment_time);
    $stmt->execute();
    $stmt->close();

    // Insert notification for the user
    $notification_message = "Your appointment has been scheduled for " . htmlspecialchars($appointment_date) . " at " . htmlspecialchars($appointment_time) . ".";
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, message) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $notification_message);
    $stmt->execute();
    $stmt->close();

    $success_message = "Appointment scheduled successfully!";
}

// Handle unscheduling appointments
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['unschedule_appointment'])) {
    $appointment_id = $_POST['appointment_id'];

    // Delete appointment from the database
    $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $stmt->close();

    $success_message = "Appointment unscheduled successfully!";
}

// Handle viewing records
$selected_user = null;
$appointments = [];
if (isset($_GET['view_user_id'])) {
    $view_user_id = $_GET['view_user_id'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $view_user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $selected_user = $result->fetch_assoc();
    $stmt->close();

    // Get appointments for the selected user
    $stmt = $conn->prepare("SELECT id, appointment_date, appointment_time FROM appointments WHERE user_id = ? AND provider_id = ?");
    $stmt->bind_param("ii", $view_user_id, $_SESSION['provider_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
    $stmt->close();
}

// Fetch unread notifications count
$user_id = $_SESSION['provider_id'];
$stmt = $conn->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($unread_count);
$stmt->fetch();
$stmt->close();

// Fetch notifications
$notifications = [];
$stmt = $conn->prepare("SELECT id, message, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
$stmt->close();

// Mark notifications as read
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['view_notifications'])) {
    $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments - Healthcare Provider</title>
    <link rel="stylesheet" href="styles.css">
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
            overflow-y: scroll;
        }

        header {
            width: 100%;
            background: #d3eaf2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            z-index: 10;
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

        main {
            width: 80%;
            max-width: 1200px;
            margin-top: 80px;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: auto;
        }

        h1 {
            font-size: 36px;
            color: #333;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-size: 18px;
            margin-bottom: 10px;
        }

        input[type="text"],
        input[type="date"],
        input[type="time"],
        button {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-right: 10px;
        }

        input[type="date"],
        input[type="time"] {
            margin-bottom: 10px;
        }

        button {
            background: #007acc;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background: #005f99;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 12px;
            text-align: left;
        }

        th {
            background: #f4f4f4;
        }

        td img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .user-records img {
            max-width: 150px;
            border-radius: 8px;
        }

        .message {
            color: #007acc;
            font-weight: bold;
            margin: 20px 0;
        }

        .message.error {
            color: #e74c3c;
        }

        .back-to-search {
            margin-top: 20px;
            text-align: center;
        }

        .back-to-search a {
            color: #007acc;
            font-size: 16px;
            text-decoration: none;
            padding: 10px 15px;
            border: 1px solid #007acc;
            border-radius: 4px;
            background: #fff;
        }

        .back-to-search a:hover {
            background: #007acc;
            color: #fff;
        }

        .view-records-btn {
            background: #007acc;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            text-align: center;
            cursor: pointer;
            display: inline-block;
            text-decoration: none;
        }

        .view-records-btn:hover {
            background: #005f99;
        }

        .appointment-form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .appointment-form label {
            margin-bottom: 5px;
        }

        .appointment-form input {
            margin-bottom: 10px;
        }

        /* Notification button */
        .notification-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #007acc;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .notification-button:hover {
            background: #005f99;
        }

        .notification-button .badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background: #e74c3c;
            color: #fff;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .notification-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 20;
        }

        .notification-modal-content {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
            position: relative;
        }

        .notification-modal-content .close {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }
    </style>
    <script>
        function toggleNotifications() {
            const modal = document.getElementById('notificationModal');
            const form = document.getElementById('notificationForm');
            if (modal.style.display === 'flex') {
                modal.style.display = 'none';
            } else {
                modal.style.display = 'flex';
                form.submit();
            }
        }
    </script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="symbol.png" alt="Logo">
        </div>
        <nav>
            <a href="providerHome.php">Home</a>
            <a href="appointments.php" class="active">Appointments</a>
            <a href="providerLogout.php">Logout</a>
        </nav>
    </header>

    <main>
        <h1>Manage Appointments</h1>

        <!-- Search Form -->
        <form method="post" action="appointments.php">
            <label for="search_id">Search User by ID:</label>
            <input type="text" id="search_id" name="search_id" required>
            <button type="submit">Search</button>
        </form>

        <?php if (!empty($search_results)) { ?>
            <h2>Search Results</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($search_results as $result) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($result['id']); ?></td>
                            <td><?php echo htmlspecialchars($result['first_name']); ?></td>
                            <td><?php echo htmlspecialchars($result['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($result['email']); ?></td>
                            <td>
                                <a href="appointments.php?view_user_id=<?php echo $result['id']; ?>" class="view-records-btn">View Records</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>

        <?php if (isset($selected_user)) { ?>
            <h2>Schedule Appointment for <?php echo htmlspecialchars($selected_user['first_name']) . ' ' . htmlspecialchars($selected_user['last_name']); ?></h2>
            <form method="post" action="appointments.php" class="appointment-form">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($selected_user['id']); ?>">
                <label for="appointment_date">Date:</label>
                <input type="date" id="appointment_date" name="appointment_date" required>
                <label for="appointment_time">Time:</label>
                <input type="time" id="appointment_time" name="appointment_time" required>
                <button type="submit" name="schedule_appointment">Schedule Appointment</button>
            </form>

            <h2>Existing Appointments</h2>
            <div class="user-records">
                <?php if (!empty($appointments)) { ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appointment) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                                    <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                                    <td>
                                        <form method="post" action="appointments.php" style="display:inline;">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                                            <button type="submit" name="unschedule_appointment">Unschedule</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p>No appointments scheduled.</p>
                <?php } ?>
            </div>

            <div class="back-to-search">
                <a href="appointments.php">Back to Search</a>
            </div>
        <?php } ?>

        <?php if (!empty($success_message)) { ?>
            <div class="message">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php } ?>

        <?php if (isset($error_message)) { ?>
            <div class="message error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php } ?>
    </main>
</body>
</html>
