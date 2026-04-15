<?php
session_start();
include "../config/db.php";

if(isset($_POST['register'])) {

    $company = mysqli_real_escape_string($conn,$_POST['company_name']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $phone = mysqli_real_escape_string($conn,$_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO retailers (company_name,email,phone,password)
            VALUES ('$company','$email','$phone','$password')";

    if(mysqli_query($conn,$sql)){
        header("Location: login.php");
        exit;
    } else {
        echo "Error: ".mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Retailer Register</title>
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
    width:460px;
    background:#fff;
    padding:40px;
    border-radius:20px;
    box-shadow:0 20px 50px rgba(0,0,0,0.08);
}
h2{margin-bottom:25px;}
input{
    width:100%;
    padding:14px;
    margin-bottom:15px;
    border-radius:12px;
    border:1px solid #ddd;
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
a{color:#16a34a;text-decoration:none;font-weight:600;}
</style>
</head>
<body>
<div class="card">
    <h2>Create Retailer Account</h2>

    <form method="POST">
        <input name="company_name" placeholder="Company Name" required>
        <input type="email" name="email" placeholder="Official Email" required>
        <input name="phone" placeholder="Contact Number">
        <input type="password" name="password" placeholder="Password" required>
        <button name="register">Register</button>
    </form>

    <p style="margin-top:15px;font-size:14px;">
        Already registered? <a href="login.php">Login</a>
    </p>
</div>
</body>
</html>