<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$result = mysqli_query($conn, "
    SELECT 
        u.id,
        u.name,
        u.email,
        a.phone,
        a.full_name,
        a.address_line,
        a.city,
        a.state,
        a.pincode
    FROM users u
    LEFT JOIN customer_addresses a ON u.id = a.customer_id
    WHERE u.role='customer'
    ORDER BY u.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customers</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #0d6efd;
            color: white;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        h2 {
            color: #333;
        }
        a {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 15px;
            background: #0d6efd;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background: #084298;
        }
    </style>
</head>
<body>

<h2>Customers List</h2>
<table border="1" cellpadding="10">
<tr>
    <th>Customer ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Delivery Name</th>
    <th>Address</th>
    <th>City</th>
    <th>State</th>
    <th>Pincode</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td><?= $row['phone'] ? htmlspecialchars($row['phone']) : 'N/A' ?></td>
    <td><?= $row['full_name'] ? htmlspecialchars($row['full_name']) : 'N/A' ?></td>
    <td><?= $row['address_line'] ? htmlspecialchars($row['address_line']) : 'N/A' ?></td>
    <td><?= $row['city'] ? htmlspecialchars($row['city']) : 'N/A' ?></td>
    <td><?= $row['state'] ? htmlspecialchars($row['state']) : 'N/A' ?></td>
    <td><?= $row['pincode'] ? htmlspecialchars($row['pincode']) : 'N/A' ?></td>
</tr>
<?php } ?>

</table>

<br>
<a href="dashboard.php">Back</a>

</body>
</html>
