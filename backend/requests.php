<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

/*
 Fetch complete request + product details
*/
$query = "
SELECT 
    r.id,
    r.quantity,
    r.status,
    r.estimated_price,
    r.actual_price,
    r.price_status,
    r.product_name,
    r.company,
    r.age_months,
    r.working_condition,
    u1.name AS customer_name,
    u2.name AS retailer_name
FROM requests r
JOIN users u1 ON r.customer_id = u1.id
LEFT JOIN users u2 ON r.retailer_id = u2.id
ORDER BY r.id DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Requests</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #333;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #f0f0f0;
        }

        h2 {
            margin-top: 20px;
        }
    </style>
</head>

<body>

<h2>All Requests</h2>

<table>
<tr>
    <th>Customer</th>
    <th>Retailer</th>
    <th>Product</th>
    <th>Company</th>
    <th>Age</th>
    <th>Condition</th>
    <th>Qty (kg)</th>
    <th>Estimated Price</th>
    <th>Actual Price</th>
    <th>Request Status</th>
    <th>Price Status</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?= htmlspecialchars($row['customer_name']) ?></td>

    <td>
        <?= $row['retailer_name']
            ? htmlspecialchars($row['retailer_name'])
            : 'Not Assigned' ?>
    </td>

    <td><?= htmlspecialchars($row['product_name']) ?></td>

    <td><?= htmlspecialchars($row['company']) ?></td>

    <td><?= round($row['age_months'] / 12, 1) ?> yrs</td>

    <td><?= $row['working_condition'] === 'Y' ? 'Working' : 'Not Working' ?></td>

    <td><?= $row['quantity'] ?></td>

    <td>₹<?= number_format($row['estimated_price'] ?? 0, 2) ?></td>

    <td>
        <?= $row['actual_price'] !== null
            ? '₹' . number_format($row['actual_price'], 2)
            : '-' ?>
    </td>

    <!-- REQUEST STATUS -->
    <td><?= ucfirst($row['status']) ?></td>

    <!-- PRICE STATUS -->
    <td><?= ucfirst($row['price_status']) ?></td>
</tr>
<?php } ?>

</table>

<br>
<a href="dashboard.php">Back</a>

</body>
</html>
