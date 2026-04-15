<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['customer_id'])) {
    die("Not logged in!");
}

$cid = $_SESSION['customer_id'];

// Get current customer info
$user = mysqli_query($conn, "SELECT * FROM users WHERE id=$cid");
$user_data = mysqli_fetch_assoc($user);

// Get current address
$addr = mysqli_query($conn, "SELECT * FROM customer_addresses WHERE customer_id=$cid");
$addr_data = mysqli_fetch_assoc($addr);

// Get current requests
$reqs = mysqli_query($conn, "SELECT COUNT(*) as count FROM requests WHERE customer_id=$cid");
$req_data = mysqli_fetch_assoc($reqs);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Address Test Page</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; box-shadow: 0 0 5px #ccc; }
        input, textarea { width: 100%; padding: 8px; margin: 5px 0; box-sizing: border-box; }
        button { padding: 10px 20px; background: #0071e3; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #0051cc; }
        .status { margin: 10px 0; padding: 10px; border-radius: 3px; }
        .success { background: #d4edda; color: #155724; }
        .error { background: #f8d7da; color: #721c24; }
        .info { background: #d1ecf1; color: #0c5460; }
        h3 { color: #333; }
    </style>
</head>
<body>

<h1>🔍 Address Test & Debug Page</h1>

<div class="box">
    <h3>👤 Current Customer Info</h3>
    <p><strong>Customer ID:</strong> <?= $cid ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars($user_data['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user_data['email']) ?></p>
    <p><strong>Total Requests:</strong> <?= $req_data['count'] ?></p>
</div>

<div class="box">
    <h3>📍 Current Address Status</h3>
    <?php if ($addr_data) { ?>
        <p style="color:green;"><strong>✓ Address Found in Database</strong></p>
        <table border="1" cellpadding="10" style="width:100%;">
            <tr><td><strong>Full Name:</strong></td><td><?= htmlspecialchars($addr_data['full_name']) ?></td></tr>
            <tr><td><strong>Phone:</strong></td><td><?= htmlspecialchars($addr_data['phone']) ?></td></tr>
            <tr><td><strong>Address:</strong></td><td><?= htmlspecialchars($addr_data['address_line']) ?></td></tr>
            <tr><td><strong>City:</strong></td><td><?= htmlspecialchars($addr_data['city']) ?></td></tr>
            <tr><td><strong>State:</strong></td><td><?= htmlspecialchars($addr_data['state']) ?></td></tr>
            <tr><td><strong>Pincode:</strong></td><td><?= htmlspecialchars($addr_data['pincode']) ?></td></tr>
        </table>
    <?php } else { ?>
        <p style="color:orange;"><strong>⚠ NO ADDRESS FOUND - Need to save one</strong></p>
    <?php } ?>
</div>

<div class="box">
    <h3>📝 Test Address Submission</h3>
    <form id="testAddressForm">
        <input name="full_name" placeholder="Full Name" value="<?= htmlspecialchars($addr_data['full_name'] ?? 'Test User') ?>" required>
        <input name="phone" placeholder="Phone" value="<?= htmlspecialchars($addr_data['phone'] ?? '9876543210') ?>" required>
        <textarea name="address_line" placeholder="Full Address" required><?= htmlspecialchars($addr_data['address_line'] ?? 'Test Address') ?></textarea>
        <input name="city" placeholder="City" value="<?= htmlspecialchars($addr_data['city'] ?? 'Test City') ?>" required>
        <input name="state" placeholder="State" value="<?= htmlspecialchars($addr_data['state'] ?? 'Test State') ?>" required>
        <input name="pincode" placeholder="Pincode" value="<?= htmlspecialchars($addr_data['pincode'] ?? '123456') ?>" required>
        <button type="button" onclick="testSaveAddress()">Test Save Address</button>
    </form>
    <div id="testStatus"></div>
</div>

<div class="box">
    <h3>📊 Backend Query Test</h3>
    <button onclick="testQuery()">Test Query Results</button>
    <div id="queryResult" style="margin-top:10px; padding:10px; background:#f9f9f9; border:1px solid #ddd;"></div>
</div>

<script>
async function testSaveAddress() {
    const form = document.getElementById('testAddressForm');
    const statusDiv = document.getElementById('testStatus');
    
    const data = {
        full_name: form.querySelector("input[name='full_name']").value,
        phone: form.querySelector("input[name='phone']").value,
        address: form.querySelector("textarea[name='address_line']").value,
        city: form.querySelector("input[name='city']").value,
        state: form.querySelector("input[name='state']").value,
        pincode: form.querySelector("input[name='pincode']").value
    };
    
    console.log("Sending:", data);
    
    try {
        const res = await fetch('customer/save_address.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await res.json();
        console.log("Response:", result);
        
        if (result.status === 'success') {
            statusDiv.innerHTML = '<div class="status success">✓ SUCCESS: ' + result.msg + '</div>';
            setTimeout(() => location.reload(), 2000);
        } else {
            statusDiv.innerHTML = '<div class="status error">✗ ERROR: ' + result.msg + '</div>';
        }
    } catch (error) {
        statusDiv.innerHTML = '<div class="status error">✗ FETCH ERROR: ' + error.message + '</div>';
    }
}

async function testQuery() {
    try {
        const res = await fetch('api_test_query.php');
        const result = await res.text();
        document.getElementById('queryResult').innerHTML = result;
    } catch (error) {
        document.getElementById('queryResult').innerHTML = '<div class="status error">Error: ' + error.message + '</div>';
    }
}
</script>

<div class="box">
    <h3>🔗 Debug Links</h3>
    <ul>
        <li><a href="debug_addresses.php">View All Addresses in Database</a></li>
        <li><a href="customer/dashboard.php">Back to Dashboard</a></li>
    </ul>
</div>

</body>
</html>
