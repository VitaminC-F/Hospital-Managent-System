<?php
session_start();
require_once('config.php');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $backup_code = $_POST['backup_code'];
    $user_id = $_SESSION['user_id'];

    // Hash the input backup code to match the database
    $hashed_backup_code = md5($backup_code);

    // Check if the hashed backup code matches any of the user's backup codes
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND FIND_IN_SET(?, backup_codes)");
    $stmt->bind_param("is", $user_id, $hashed_backup_code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Backup code matches, grant access to index.php
        header("Location: index.php");
        exit;
    } else {
        // Invalid backup code, redirect back to mfa_backup_codes.php with error message
        $_SESSION['error'] = "Invalid backup code. Please try again.";
        header("Location: mfa_backup_codes.php");
        exit;
    }

    $stmt->close();
}

// Redirect to mfa_backup_codes.php if accessed directly
header("Location: mfa_backup_codes.php");
exit;
?>
