<?php
require_once('../config.php');
require_once('PHPMailer-master/src/PHPMailer.php');
require_once('PHPMailer-master/src/SMTP.php');
require_once('PHPMailer-master/src/Exception.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if email is set and not empty
    if (isset($_POST['email']) && !empty($_POST['email'])) {
        $email = $_POST['email'];

        // Fetch user data based on the provided email
        $query = $conn->query("SELECT * FROM users WHERE email = '$email'");
        $user = $query->fetch_assoc();

        if ($user) {
            // Generate a unique token
            $token = bin2hex(random_bytes(32));

            // Set token and expiration time
            $expires_at = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Store the token and expiration time in the database
            $conn->query("UPDATE users SET reset_token = '$token', reset_token_expires = '$expires_at' WHERE id = {$user['id']}");

            // Send email with password reset link
            $mail = new PHPMailer(true);
            try {
                //Server settings
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       = $_settings->info('smtp_host');           // Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = $_settings->info('smtp_username');       // SMTP username
                $mail->Password   = $_settings->info('smtp_password');       // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                $mail->Port       = $_settings->info('smtp_port');           // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
                
                //Recipients
                $mail->setFrom($_settings->info('smtp_username'), $_settings->info('name'));
                $mail->addAddress($email);     // Add a recipient
                
                // Content
                $reset_link = "http://localhost/hprms/admin/reset_password.php?token=$token";
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Reset your password';
                $mail->Body    = "Please click the following link to reset your password: $reset_link";
                
                $mail->send();
                
                // Redirect to login page
                header("Location: login.php");
                exit();
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } else {
            // User not found, display error message
            $error = "User not found with this email address";
        }
    } else {
        // Email is empty, display error message
        $error = "Please enter your email address";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>
<body>
    <h1>Forgot Password</h1>
    <?php if (isset($error)) : ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>
    <form action="" method="post">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" placeholder="Enter your email address" required>
        <button type="submit">Submit</button>
        <a href="login.php"><button type="button">Cancel</button></a>
    </form>
</body>
</html>
