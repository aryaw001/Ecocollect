<?php
include "../config/db.php";
include "../includes/price_engine.php";

$data = json_decode(file_get_contents("php://input"), true);

$price = estimate_price(
    $conn,
    $data['product_name'],
    $data['company'],
    $data['age_months'],
    $data['working_condition'],
    $data['qty']
);

echo json_encode(["price" => $price]);
