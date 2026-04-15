<?php
session_start();
include "../config/db.php";
include "../includes/price_engine.php";

$cid = $_SESSION['customer_id'];

$product = $_POST['product_name'];
$company = $_POST['company'];
$age     = $_POST['age_months'];
$cond    = $_POST['working_condition'];
$qty     = $_POST['qty'];

$estimated_price = estimate_price(
    $conn, $product, $company, $age, $cond, $qty
);

mysqli_query($conn,"
INSERT INTO requests
(customer_id, product_name, company, age_months, working_condition, quantity, estimated_price)
VALUES
($cid,'$product','$company',$age,'$cond',$qty,$estimated_price)
");

header("Location: dashboard.php");
