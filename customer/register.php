<?php
session_start();
include "../config/db.php";
$error = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Insert user with role as customer
    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$pass', 'customer')";

    if (mysqli_query($conn, $sql)) {

        // Get newly created user ID
        $newUserId = mysqli_insert_id($conn);

        // Create login session immediately
        $_SESSION['customer_id'] = $newUserId;

        // Redirect directly to dashboard
        header("Location: dashboard.php");
        exit;

    } else {
        echo "Registration failed: " . mysqli_error($conn);
    }
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Create Account</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="auth-container">
    <div class="auth-card">

        <h2>Create your account</h2>

        <?php if ($error) { ?>
            <p style="color:#ffb4b4;"><?= $error ?></p>
        <?php } ?>

        <form method="POST">
            <input name="name" placeholder="Full name" required>
            <input type="email" name="email" placeholder="Email address" required>

            <div class="password-box">
                <input type="password" name="password" placeholder="Password" required>
                <span class="toggle-password">👁</span>
            </div>

            <button name="register">Register</button>
        </form>

        <div class="switch-link">
            Already have an account?
            <a href="login.php">Login</a>
        </div>

    </div>
</div>
<script src="auth.js"></script>
</body>
</html>
