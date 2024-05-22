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
    $security_answer = $_POST['security_answer'];
    $user_id = $_SESSION['user_id'];

    // Hash the input security answer to match the database
    $hashed_security_answer = md5($security_answer);

    // Retrieve the user's security question and hashed answer from the database
    $stmt = $conn->prepare("SELECT security_question, security_answer FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    // Check if the hashed security answer matches the stored answer
    if ($row && $row['security_answer'] === $hashed_security_answer) {
        // Correct security answer, grant access to index.php
        header("Location: index.php");
        exit;
    } else {
        // Invalid security answer, redirect back to mfa_security_question.php with error message
        $_SESSION['error'] = "Invalid security answer. Please try again.";
        header("Location: mfa_security_question.php");
        exit;
    }

    $stmt->close();
}

// Redirect to mfa_security_question.php if accessed directly
header("Location: mfa_security_question.php");
exit;
?>
