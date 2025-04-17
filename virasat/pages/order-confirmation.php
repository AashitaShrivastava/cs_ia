<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$tracking_id = $_GET['track'] ?? null;

if (!$tracking_id) {
    die("No tracking ID found.");
}

// Get order details based on tracking ID
$sql = "SELECT o.order_id, o.total_price, o.status, o.estimated_delivery, od.fullname, od.email 
        FROM orders o
        JOIN order_details od ON o.order_id = od.order_id
        WHERE o.tracking_id = ? AND o.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $tracking_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Invalid tracking ID or no matching order.");
}

$order = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            padding: 40px;
        }

        .confirmation-box {
            background-color: #fff;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            border-radius: 20px;
            box-shadow: 0px 6px 20px rgba(0,0,0,0.1);
            text-align: center;
        }

        h2 {
            color: #2d6a4f;
            margin-bottom: 20px;
        }

        .info {
            font-size: 16px;
            margin: 12px 0;
            color: #444;
        }

        .highlight {
            font-weight: bold;
            color: #e63946;
        }

        .cta {
            margin-top: 30px;
        }

        .cta a {
            text-decoration: none;
            background: #2d6a4f;
            color: white;
            padding: 12px 25px;
            border-radius: 8px;
            transition: 0.3s ease;
        }

        .cta a:hover {
            background: #1b4332;
        }
    </style>
</head>
<body>

<div class="confirmation-box">
    <h2>Thank You for Your Order!</h2>
    <p class="info">Order placed successfully under the name <strong><?= htmlspecialchars($order['fullname']) ?></strong>.</p>
    <p class="info">A confirmation email has been sent to <strong><?= htmlspecialchars($order['email']) ?></strong>.</p>
    <p class="info">Your total: <span class="highlight">₹<?= $order['total_price'] ?></span></p>
    <h3>Thank you for your order!</h3>
<p>Tracking ID: <?= htmlspecialchars($tracking_id) ?></p>
    <p class="info">Estimated Delivery Date: <strong><?= $order['estimated_delivery'] ?></strong></p>
    <p class="info">Current Status: <strong><?= $order['status'] ?></strong></p>

    <div class="cta">
        <a href="products.php">Continue Shopping</a>
    </div>
</div>

</body>
</html>
