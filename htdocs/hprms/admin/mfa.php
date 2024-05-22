<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Multi-Factor Authentication</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center">Multi-Factor Authentication</h1>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Choose Verification Method</h5>
                        <p class="card-text">Please select the verification method:</p>
                        <a href="mfa_backup_codes.php" class="btn btn-primary btn-block">Backup Codes</a>
                        <a href="mfa_security_question.php" class="btn btn-primary btn-block">Security Question</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

