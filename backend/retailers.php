<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Fetch retailers from retailers table (NOT users table)
$result = mysqli_query($conn, "SELECT * FROM retailers ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Retailers</title>
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

<h2>Retailers</h2>

<table>
<tr>
    <th>ID</th>
    <th>Company Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Registered On</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['company_name']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td><?= htmlspecialchars($row['phone']) ?></td>
    <td><?= $row['created_at'] ?></td>
</tr>
<?php } ?>

</table>

<br>
<a href="dashboard.php">← Back</a>

</body>
</html>