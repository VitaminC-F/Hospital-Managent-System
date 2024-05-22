<?php
require_once('../config.php');

function generateBackupCodes($numberOfCodes = 8) {
    $backupCodes = [];
    for ($i = 0; $i < $numberOfCodes; $i++) {
        $backupCode = bin2hex(random_bytes(4)); // Generates an 8-character code
        $backupCodes[] = md5($backupCode); // Hash the backup code using MD5 before adding to the array
    }
    return $backupCodes;
}

if (isset($_POST['create_user'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $phone_number = $_POST['phone_number'];
    $email = $_POST['email'];
    $ic_number = $_POST['ic_number'];
    $type = $_POST['type'];
    $password = md5($_POST['password']); // Hash the password using MD5
    $security_question = $_POST['security_question'];
    $security_answer = md5($_POST['security_answer']); // Hash the security answer using MD5
    $backup_codes = generateBackupCodes();
    $serialized_backup_codes = serialize($backup_codes); // Store as serialized array

    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, username, phone_number, email, ic_number, type, password, security_question, security_answer, backup_codes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $firstname, $lastname, $username, $phone_number, $email, $ic_number, $type, $password, $security_question, $security_answer, $serialized_backup_codes);

    if ($stmt->execute()) {
        echo "User registered successfully. Here are your backup codes:";
        foreach ($backup_codes as $code) {
            echo "<p>$code</p>";
        }
        header("Location: list.php"); // Redirect to the list.php page
        exit(); // Make sure no other output is sent before redirection
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register User</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center">Register New User</h1>
        <form action="" method="post">
            <div class="form-group">
                <label for="firstname">First Name</label>
                <input type="text" id="firstname" name="firstname" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="lastname">Last Name</label>
                <input type="text" id="lastname" name="lastname" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
                <small id="password_help" class="form-text text-muted">Password must contain at least 1 symbol, 1 capital letter, and 1 number.</small>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Re-enter your password" required>
                <span id="password_error" style="color: red;"></span>

                <script>
                    document.getElementById('confirm_password').addEventListener('input', validatePassword);

                    function validatePassword() {
                        var password = document.getElementById('password').value;
                        var confirm_password = document.getElementById('confirm_password').value;
                        var password_error = document.getElementById('password_error');

                        if (password != confirm_password) {
                            password_error.textContent = 'Passwords do not match';
                        } else {
                            password_error.textContent = '';
                        }
                    }
                </script>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="ic_number">IC Number</label>
                <input type="text" id="ic_number" name="ic_number" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="type">User Type</label>
                <select id="type" name="type" class="form-control" required>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="security_question">Security Question</label>
                <select name="security_question" id="security_question" required>
                    <option value="">Select a security question</option>
                    <option value="What is your favorite book?">What is your favorite book?</option>
                    <option value="What is your favorite color?">What is your favorite color?</option>
                    <option value="In what city did you meet your spouse/significant other?">In what city did you meet your spouse/significant other?</option>
                    <option value="What is the name of your favorite teacher?">What is the name of your favorite teacher?</option>
                    <option value="What is your favorite food?">What is your favorite food?</option>
                    <option value="What is your dream job?">What is your dream job?</option>
                    <option value="What is the name of the street you grew up on?">What is the name of the street you grew up on?</option>
                    <option value="What is your favorite sports team?">What is your favorite sports team?</option>
                    <option value="What is your favorite holiday destination?">What is your favorite holiday destination?</option>
                    <option value="What is the name of your favorite childhood toy?">What is the name of your favorite childhood toy?</option>
                    <option value="What is the name of your first grade teacher?">What is the name of your first grade teacher?</option>
                    <option value="What is the name of your favorite musician?">What is the name of your favorite musician?</option>
                    <option value="What was the model of your first cellphone?">What was the model of your first cellphone?</option>
                    <option value="What is the name of your favorite fictional character?">What is the name of your favorite fictional character?</option>
                    <option value="What is the name of the hospital where you were born?">What is the name of the hospital where you were born?</option>
                </select>
            </div>
            <div class="form-group">
                <label for="security_answer">Answer</label>
                <input type="text" id="security_answer" name="security_answer" class="form-control" required>
            </div>
            <button type="submit" name="create_user" class="btn btn-primary">Create</button>
        </form>
    </div>

    <script>
        document.getElementById('confirm_password').addEventListener('input', validatePassword);

        function validatePassword() {
            var password = document.getElementById('password').value;
            var confirm_password = document.getElementById('confirm_password').value;
            var password_error = document.getElementById('password_error');
            var password_help = document.getElementById('password_help');

            var symbolRegex = /[$&+,:;=?@#|'<>.^*()%!-]/;
            var capitalRegex = /[A-Z]/;
            var numberRegex = /[0-9]/;

            if (password !== confirm_password) {
                password_error.textContent = 'Passwords do not match';
            } else if (!symbolRegex.test(password) || !capitalRegex.test(password) || !numberRegex.test(password)) {
                password_error.textContent = 'Password must contain at least 1 symbol, 1 capital letter, and 1 number';
            } else {
                password_error.textContent = '';
            }
        }
    </script>
</body>
</html>
