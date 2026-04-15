<?php
include 'config/db.php';

$query = "
SELECT 
    u.id,
    u.name,
    r.id as req_id,
    a.full_name,
    a.phone,
    a.address_line,
    a.city
FROM requests r
JOIN users u ON r.customer_id = u.id
LEFT JOIN customer_addresses a ON r.customer_id = a.customer_id
ORDER BY r.id DESC
LIMIT 5
";

$result = mysqli_query($conn, $query);

echo "<table border='1' cellpadding='10' style='width:100%;'>";
echo "<tr><th>Customer</th><th>Request ID</th><th>Delivery Name</th><th>Phone</th><th>Address</th><th>City</th></tr>";

while($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['name']) . " (ID: " . $row['id'] . ")</td>";
    echo "<td>" . $row['req_id'] . "</td>";
    echo "<td>" . ($row['full_name'] ? htmlspecialchars($row['full_name']) : '<span style="color:red;">NULL</span>') . "</td>";
    echo "<td>" . ($row['phone'] ? htmlspecialchars($row['phone']) : '<span style="color:red;">NULL</span>') . "</td>";
    echo "<td>" . ($row['address_line'] ? htmlspecialchars($row['address_line']) : '<span style="color:red;">NULL</span>') . "</td>";
    echo "<td>" . ($row['city'] ? htmlspecialchars($row['city']) : '<span style="color:red;">NULL</span>') . "</td>";
    echo "</tr>";
}

echo "</table>";
?>
