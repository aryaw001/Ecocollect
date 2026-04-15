<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - E-Waste</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 40px;
        }
        .box {
            background: white;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
        }
        a {
            display: block;
            text-decoration: none;
            padding: 12px;
            margin: 10px 0;
            background: #0d6efd;
            color: white;
            text-align: center;
            border-radius: 5px;
        }
        a:hover {
            background: #084298;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Admin Dashboard</h2>

    <a href="customers.php">Manage Customers</a>
    <a href="requests.php">View Customer Requests</a>
    <a href="view_requests.php" class="btn">View Addresses of Requested Customers</a>
    <a href="retailers.php">Manage Retailers</a>
    <a href="retailer_requests.php">View Retailer Submissions</button></a>
    <a href="logout.php" style="background:#dc3545;">Logout</a>
</div>

</body>
</html>
