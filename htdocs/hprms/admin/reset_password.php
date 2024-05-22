<?php
require_once('../config.php');

// Check if the token is provided in the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Fetch user data based on the provided token
    $query = $conn->query("SELECT * FROM users WHERE reset_token = '$token'");
    $user = $query->fetch_assoc();

    if ($user) {
        // Check if the token is still valid
        if (strtotime($user['reset_token_expires']) > time()) {
            // Token is valid, allow the user to reset the password
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Check if password and confirm password are set and match
                if (isset($_POST['password']) && !empty($_POST['password']) && isset($_POST['confirm_password']) && !empty($_POST['confirm_password'])) {
                    $password = $_POST['password'];
                    $confirm_password = $_POST['confirm_password'];

                    if ($password == $confirm_password) {
                        // Hash the password
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                        // Update the user's password and remove the reset token
                        $conn->query("UPDATE users SET password = '$hashed_password', reset_token = NULL, reset_token_expires = NULL WHERE id = {$user['id']}");

                        // Redirect to the login page
                        header("Location: login.php");
                        exit();
                    } else {
                        $error = "Password and confirm password do not match";
                    }
                } else {
                    $error = "Please enter and confirm your new password";
                }
            }
        } else {
            // Token has expired, display error message
            $error = "The password reset link has expired";
        }
    } else {
        // Token is not valid, display error message
        $error = "Invalid password reset link";
    }
} else {
    // Token is not provided in the URL, redirect to forgot password page
    header("Location: forgot_password.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h1>Reset Password</h1>
    <?php if (isset($error)) : ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="" method="post">
        <label for="password">New Password</label>
        <input type="password" id="password" name="password" required>
        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
