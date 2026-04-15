<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['customer_id'])) {
    echo json_encode(["status"=>"error","msg"=>"Unauthorized - No session"]);
    exit;
}

$cid = $_SESSION['customer_id'];

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["status"=>"error","msg"=>"No data received"]);
    exit;
}

$full_name = mysqli_real_escape_string($conn, $data['full_name'] ?? '');
$phone     = mysqli_real_escape_string($conn, $data['phone'] ?? '');
$address   = mysqli_real_escape_string($conn, $data['address'] ?? '');
$city      = mysqli_real_escape_string($conn, $data['city'] ?? '');
$state     = mysqli_real_escape_string($conn, $data['state'] ?? '');
$pincode   = mysqli_real_escape_string($conn, $data['pincode'] ?? '');

// Validate data
if (!$full_name || !$phone || !$address || !$city || !$state || !$pincode) {
    echo json_encode(["status"=>"error","msg"=>"All fields are required"]);
    exit;
}

/* UPSERT = Update if exists, Insert if new */
$check = mysqli_query($conn,"SELECT id FROM customer_addresses WHERE customer_id=$cid");

if(!$check) {
    echo json_encode(["status"=>"error","msg"=>"Database error: " . mysqli_error($conn)]);
    exit;
}

if(mysqli_num_rows($check) > 0){
    // UPDATE
    $update_query = "
        UPDATE customer_addresses 
        SET full_name='$full_name', phone='$phone', address_line='$address',
            city='$city', state='$state', pincode='$pincode'
        WHERE customer_id=$cid
    ";
    
    if(!mysqli_query($conn, $update_query)) {
        echo json_encode(["status"=>"error","msg"=>"Update failed: " . mysqli_error($conn)]);
        exit;
    }

}else{
    // INSERT
    $insert_query = "
        INSERT INTO customer_addresses
        (customer_id, full_name, phone, address_line, city, state, pincode)
        VALUES
        ($cid,'$full_name','$phone','$address','$city','$state','$pincode')
    ";
    
    if(!mysqli_query($conn, $insert_query)) {
        echo json_encode(["status"=>"error","msg"=>"Insert failed: " . mysqli_error($conn)]);
        exit;
    }
}

echo json_encode(["status"=>"success","msg"=>"Address saved successfully"]);
?>
