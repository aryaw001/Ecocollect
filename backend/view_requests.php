<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$query = mysqli_query($conn, "
SELECT 
    u.name,
    u.email,
    r.product_name,
    r.quantity,
    r.estimated_price,
    a.full_name,
    a.phone,
    a.address_line,
    a.city,
    a.state,
    a.pincode
FROM requests r
JOIN users u ON r.customer_id = u.id
LEFT JOIN customer_addresses a ON r.customer_id = a.customer_id
ORDER BY r.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>All Requests</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f4f6fb;
}

.container {
    width: 95%;
    margin: 30px auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    font-size: 14px;
}

th {
    background: #2563eb;
    color: white;
}

tr:nth-child(even) {
    background: #f8fafc;
}

h2 {
    text-align: center;
}

.back {
    margin-top: 20px;
    display: inline-block;
    padding: 10px 15px;
    background: #2563eb;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}

.back:hover {
    background: #1d4ed8;
}
</style>
</head>

<body>

<div class="container">
<h2>All Customer Requests</h2>

<table>
<tr>
<th>Name</th>
<th>Email</th>
<th>Product</th>
<th>Qty (kg)</th>
<th>Price</th>
<th>Delivery Name</th>
<th>Phone</th>
<th>Address</th>
<th>City</th>
<th>State</th>
<th>Pincode</th>
</tr>

<?php while($row = mysqli_fetch_assoc($query)) { ?>
<tr>
<td><?= htmlspecialchars($row['name'] ?? '') ?></td>
<td><?= htmlspecialchars($row['email'] ?? '') ?></td>
<td><?= htmlspecialchars($row['product_name'] ?? '') ?></td>
<td><?= $row['quantity'] ?></td>
<td>₹<?= $row['estimated_price'] ?></td>
<td><?= htmlspecialchars($row['full_name'] ?? 'N/A') ?></td>
<td><?= htmlspecialchars($row['phone'] ?? 'N/A') ?></td>
<td><?= htmlspecialchars($row['address_line'] ?? 'N/A') ?></td>
<td><?= htmlspecialchars($row['city'] ?? 'N/A') ?></td>
<td><?= htmlspecialchars($row['state'] ?? 'N/A') ?></td>
<td><?= htmlspecialchars($row['pincode'] ?? 'N/A') ?></td>
</tr>
<?php } ?>

</table>

<a class="back" href="dashboard.php">⬅ Back to Dashboard</a>

</div>

</body>
</html>
