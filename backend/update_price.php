<?php
include "../config/db.php";

$id = $_POST['request_id'];
$actual_price = $_POST['actual_price'];

mysqli_query($conn,"
UPDATE requests 
SET actual_price=$actual_price, price_status='final'
WHERE id=$id
");

/* FEEDBACK LEARNING */
$r = mysqli_fetch_assoc(mysqli_query($conn,"
SELECT product_name, company, quantity, estimated_price, actual_price
FROM requests WHERE id=$id
"));

$error_ratio = $r['actual_price'] / max(1,$r['estimated_price']);

mysqli_query($conn,"
UPDATE pricing_model
SET weight_factor = weight_factor * $error_ratio,
    last_updated = NOW()
WHERE product_name='{$r['product_name']}'
AND company='{$r['company']}'
");

header("Location: requests.php");
