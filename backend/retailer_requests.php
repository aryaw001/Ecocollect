<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$sql = "
SELECT rr.*, r.company_name, r.email
FROM retailer_requests rr
JOIN retailers r ON rr.retailer_id = r.id
ORDER BY rr.created_at DESC
";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Retailer Requests</title>
<style>
body{font-family:Arial;background:#f4f6f9;padding:40px;}
table{width:100%;border-collapse:collapse;background:white;}
th,td{padding:12px;border:1px solid #ddd;}
th{background:#16a34a;color:white;}
select,input,textarea{padding:6px;}
button{padding:6px 10px;background:#2563eb;color:white;border:none;}
.badge{padding:4px 10px;border-radius:6px;font-size:12px;}
.pending{background:#fef3c7;color:#92400e;}
.approved{background:#dcfce7;color:#166534;}
.scheduled{background:#e0f2fe;color:#075985;}
.completed{background:#ede9fe;color:#5b21b6;}
.rejected{background:#fee2e2;color:#991b1b;}
</style>
</head>
<body>

<h2>Retailer Submitted Requests</h2>

<table>
<tr>
<th>Request Code</th>
<th>Retailer</th>
<th>Email</th>
<th>Waste Types</th>
<th>Quantity</th>
<th>Status</th>
<th>Collector</th>
<th>Notes</th>
<th>Action</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
<form method="POST" action="update_retailer_request.php">

<td><?= $row['request_code'] ?></td>
<td><?= htmlspecialchars($row['company_name']) ?></td>
<td><?= htmlspecialchars($row['email']) ?></td>
<td><?= $row['waste_types'] ?></td>
<td><?= $row['quantity']." ".$row['quantity_unit'] ?></td>

<td>
<select name="status">
<option value="pending" <?= $row['status']=='pending'?'selected':'' ?>>Pending</option>
<option value="approved" <?= $row['status']=='approved'?'selected':'' ?>>Approved</option>
<option value="scheduled" <?= $row['status']=='scheduled'?'selected':'' ?>>Scheduled</option>
<option value="completed" <?= $row['status']=='completed'?'selected':'' ?>>Completed</option>
<option value="rejected" <?= $row['status']=='rejected'?'selected':'' ?>>Rejected</option>
</select>
</td>

<td>
<input type="text" name="collector" value="<?= $row['assigned_collector'] ?>">
</td>

<td>
<textarea name="notes"><?= $row['recycler_notes'] ?></textarea>
</td>

<td>
<input type="hidden" name="id" value="<?= $row['id'] ?>">
<button type="submit">Update</button>
</td>

</form>
</tr>
<?php } ?>

</table>

<br>
<a href="dashboard.php">⬅ Back</a>

</body>
</html>