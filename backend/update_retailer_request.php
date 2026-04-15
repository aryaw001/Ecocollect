<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$id = $_POST['id'];
$status = $_POST['status'];
$collector = $_POST['collector'];
$notes = $_POST['notes'];

$stmt = $conn->prepare("
UPDATE retailer_requests
SET status=?, assigned_collector=?, recycler_notes=?
WHERE id=?
");

$stmt->bind_param("sssi", $status, $collector, $notes, $id);
$stmt->execute();

header("Location: retailer_requests.php");
exit;