<?php
include 'config/db.php';

echo "=== TABLES ===\n";
$tables = mysqli_query($conn, 'SHOW TABLES');
while($row = mysqli_fetch_row($tables)) {
    echo $row[0] . "\n";
}

echo "\n=== USERS TABLE STRUCTURE ===\n";
$users = mysqli_query($conn, 'DESCRIBE users');
while($row = mysqli_fetch_assoc($users)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

echo "\n=== USERS DATA ===\n";
$users_data = mysqli_query($conn, 'SELECT * FROM users');
echo "Total users: " . mysqli_num_rows($users_data) . "\n";

echo "\n=== CUSTOMER_ADDRESSES TABLE STRUCTURE ===\n";
$addr = mysqli_query($conn, 'DESCRIBE customer_addresses');
if($addr) {
    while($row = mysqli_fetch_assoc($addr)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Table does not exist\n";
}

echo "\n=== REQUESTS TABLE STRUCTURE ===\n";
$req = mysqli_query($conn, 'DESCRIBE requests');
if($req) {
    while($row = mysqli_fetch_assoc($req)) {
        echo $row['Field'] . " - " . $row['Type'] . "\n";
    }
} else {
    echo "Table does not exist\n";
}
?>
