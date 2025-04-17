<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$cart_id = $_POST['cart_id'];

// ✅ Remove item
$delete_sql = "DELETE FROM cart WHERE cart_id = ? AND user_id = ?";
$stmt = $conn->prepare($delete_sql);
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();

echo json_encode(["success" => true]);
?>
