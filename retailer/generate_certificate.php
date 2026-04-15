<?php
include "../config/db.php";

$id = $_GET['id'];

$sql = "SELECT rr.*, r.company_name 
        FROM retailer_requests rr
        JOIN retailers r ON rr.retailer_id = r.id
        WHERE rr.id='$id'";

$result = mysqli_query($conn,$sql);
$data = mysqli_fetch_assoc($result);

header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=certificate.pdf");

echo "
CERTIFICATE OF RECYCLING

This certifies that

".$data['company_name']."

has successfully recycled

".$data['quantity']." ".$data['quantity_unit']." of e-waste.

Status: Completed

Date: ".date("d M Y")."

E-Waste Management Authority
";
?>