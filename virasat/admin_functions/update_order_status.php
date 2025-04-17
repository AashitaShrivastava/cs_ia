<?php
session_start();
include '../db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && $_SESSION['role'] === 'admin') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

//update order status from admin control panel
    $sql = "UPDATE orders SET status = ? WHERE order_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();

    header("Location: manage_orders.php");
    exit;
} else {
    echo "Unauthorized access.";
}
?>
