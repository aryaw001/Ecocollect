<?php
session_start();
include "../config/db.php";

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize user input
    $email = trim(mysqli_real_escape_string($conn, $_POST['email'] ?? ''));
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $error = "Please enter both email and password.";
    } else {
        // Prepare statement to avoid SQL injection
        $stmt = mysqli_prepare($conn, "SELECT id, password FROM users WHERE email = ? AND role = 'customer' LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if ($res && mysqli_num_rows($res) === 1) {
            $user = mysqli_fetch_assoc($res);

            // Verify password against hashed value stored during registration.
            // Also allow plain-text match for legacy entries.
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                $_SESSION['customer_id'] = $user['id'];
                header("Location: dashboard.php");
                exit;
            }
        }

        $error = "Invalid email or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-card">

        <h2>Welcome back</h2>

        <?php if ($error) { ?>
            <p style="color:#ffb4b4;"><?= $error ?></p>
        <?php } ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Email address" required>

            <div class="password-box">
                <input type="password" name="password" placeholder="Password" required>
                <span class="toggle-password">👁</span>
            </div>

            <button name="login">Login</button>
        </form>

        <div class="switch-link">
            Don’t have an account?
            <a href="register.php">Register</a>
        </div>

    </div>
</div>
<script src="auth.js"></script>
</body>
</html>
