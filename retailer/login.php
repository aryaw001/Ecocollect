<?php
session_start();
include "../config/db.php";

$error = "";

if(isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $q = "SELECT * FROM retailers WHERE email='$email'";
    $res = mysqli_query($conn, $q);

    if(mysqli_num_rows($res) == 1) {
        $retailer = mysqli_fetch_assoc($res);

        if(password_verify($password, $retailer['password'])) {
            $_SESSION['retailer_id'] = $retailer['id'];
            $_SESSION['retailer_name'] = $retailer['company_name'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid credentials";
        }
    } else {
        $error = "Invalid credentials";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Retailer Login</title>
<link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;600;700&display=swap" rel="stylesheet">
<style>
body{
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:#f5f6f8;
    font-family:'Figtree',sans-serif;
}
.card{
    width:420px;
    background:#fff;
    padding:40px;
    border-radius:20px;
    box-shadow:0 20px 50px rgba(0,0,0,0.08);
}
h2{
    margin-bottom:25px;
    font-weight:700;
}
input{
    width:100%;
    padding:14px;
    margin-bottom:15px;
    border-radius:12px;
    border:1px solid #ddd;
    font-size:14px;
}
button{
    width:100%;
    padding:14px;
    border:none;
    border-radius:12px;
    background:linear-gradient(135deg,#16a34a,#0ea5e9);
    color:white;
    font-weight:600;
    cursor:pointer;
}
button:hover{opacity:.9;}
a{color:#16a34a;text-decoration:none;font-weight:600;}
.error{color:#ef4444;font-size:14px;margin-bottom:10px;}
</style>
</head>
<body>
<div class="card">
    <h2>Retailer Sign In</h2>

    <?php if($error){ ?><div class="error"><?= $error ?></div><?php } ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Official Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button name="login">Login</button>
    </form>

    <p style="margin-top:15px;font-size:14px;">
        New partner? <a href="register.php">Create account</a>
    </p>
</div>
</body>
</html>