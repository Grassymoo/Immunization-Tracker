<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Directory where files will be uploaded
$target_dir = __DIR__ . '/uploads/'; // Absolute path
$target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);

// Check if the upload directory exists
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

// Check if file was uploaded
if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
    header("Location: profile.php");
    echo "The file ". htmlspecialchars(basename($_FILES["profile_picture"]["name"])) . " has been uploaded.";
    
    // Update the database with the new profile picture path
    $conn = new mysqli("localhost", "root", "", "ITDB");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $user_id = $_SESSION['user_id'];
    $profile_picture_path = basename($_FILES["profile_picture"]["name"]);
    $sql = "UPDATE users SET profile_picture = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $profile_picture_path, $user_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

} else {
    echo "Error moving the uploaded file.";
}
?>
