<?php
session_start();
include '../db_connect.php';

if ($_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

$product_id = $_POST['product_id'] ?? null;

if ($product_id) {
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
}

header("Location: admin_dashboard.php");
exit;
