<?php
include 'config/db.php';

echo "<h2>Database Debug - Address Data</h2>";

echo "<h3>1. Checking customer_addresses table structure:</h3>";
$desc = mysqli_query($conn, "DESCRIBE customer_addresses");
if ($desc) {
    echo "<pre>";
    while($row = mysqli_fetch_assoc($desc)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
    echo "</pre>";
} else {
    echo "<strong style='color:red;'>ERROR: Table does not exist!</strong><br>";
    echo "Error: " . mysqli_error($conn);
}

echo "<h3>2. All addresses in database:</h3>";
$addresses = mysqli_query($conn, "SELECT * FROM customer_addresses");
if ($addresses && mysqli_num_rows($addresses) > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Customer ID</th><th>Full Name</th><th>Phone</th><th>Address</th><th>City</th></tr>";
    while($row = mysqli_fetch_assoc($addresses)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['customer_id'] . "</td>";
        echo "<td>" . $row['full_name'] . "</td>";
        echo "<td>" . $row['phone'] . "</td>";
        echo "<td>" . $row['address_line'] . "</td>";
        echo "<td>" . $row['city'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<strong style='color:orange;'>NO ADDRESSES SAVED YET!</strong><br>";
    echo "This means customers haven't submitted their addresses.";
}

echo "<h3>3. All customers (users):</h3>";
$users = mysqli_query($conn, "SELECT id, name, email FROM users WHERE role='customer' LIMIT 5");
if ($users && mysqli_num_rows($users) > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th></tr>";
    while($row = mysqli_fetch_assoc($users)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['email'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<h3>4. Sample request with JOIN test:</h3>";
$test = mysqli_query($conn, "
SELECT 
    u.name,
    u.id as user_id,
    r.id as request_id,
    r.customer_id,
    a.full_name,
    a.phone
FROM requests r
JOIN users u ON r.customer_id = u.id
LEFT JOIN customer_addresses a ON r.customer_id = a.customer_id
LIMIT 3
");

if ($test) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>User ID</th><th>Request ID</th><th>Customer ID</th><th>Name</th><th>Delivery Name</th><th>Phone</th></tr>";
    while($row = mysqli_fetch_assoc($test)) {
        echo "<tr>";
        echo "<td>" . $row['user_id'] . "</td>";
        echo "<td>" . $row['request_id'] . "</td>";
        echo "<td>" . $row['customer_id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . ($row['full_name'] ?? 'NULL') . "</td>";
        echo "<td>" . ($row['phone'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<br><br><a href='index.php'>← Back</a>";
?>
