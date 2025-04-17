<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$sql = "SELECT p.name, p.price, c.quantity 
        FROM cart c 
        JOIN products p ON c.product_id = p.product_id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$subtotal = 0;
$items = [];

while ($row = $result->fetch_assoc()) {
    $line_total = $row['price'] * $row['quantity'];
    $subtotal += $line_total;
    $items[] = $row;
}

$tax = round($subtotal * 0.05);
$shipping = 10;
$total = $subtotal + $tax + $shipping;
?>


<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #fefbf6;
        }
        .container {
            width: 50%;
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }
        h2 {
            color: #2d6a4f;
            text-align: center;
            margin-bottom: 30px;
        }
        .section h3 {
            color: #2d6a4f;
            margin-bottom: 10px;
        }
        .section {
            margin-bottom: 35px;
        }
        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 12px 15px;
            background-color: #f0f6ff;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-top: 6px;
            margin-bottom: 14px;
        }
        .summary {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 30px;
        }
        .summary p {
            margin: 6px 0;
        }
        .total {
            color: #e63946;
            font-weight: bold;
            font-size: 1.1em;
        }
        .btn {
            width: 100%;
            background-color: #e63946;
            color: white;
            padding: 15px;
            border: none;
            border-radius: 30px;
            font-size: 16px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #d62828;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Checkout</h2>

    <div class="section summary">
        <h3>Order Summary</h3>
        <?php if (count($items) === 0): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <p><?= htmlspecialchars($item['name']) ?> × <?= $item['quantity'] ?> = ₹<?= $item['price'] * $item['quantity'] ?></p>
            <?php endforeach; ?>
            <p>Subtotal: ₹<?= $subtotal ?></p>
            <p>Tax (5%): ₹<?= $tax ?></p>
            <p>Shipping: ₹<?= $shipping ?></p>
            <p class="total">Total: ₹<?= $total ?></p>
        <?php endif; ?>
    </div>

    <form method="POST" action="process_checkout.php">
        <div class="section">
            <h3>Shipping Information</h3>
            <input type="text" name="fullname" placeholder="Full Name" required>
            <input type="text" name="address" placeholder="Address" required>
            <input type="text" name="city" placeholder="City" required>
            <input type="text" name="postal" placeholder="Postal Code" required>
            <input type="text" name="phone" placeholder="Phone" required>
            <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="section">
            <h3>Payment Information</h3>
            <input type="text" name="card" placeholder="Card Number" required>
            <input type="text" name="expiry" placeholder="MM/YY" required>
            <input type="text" name="cvv" placeholder="CVV" required>
        </div>

        <!-- This passes total price securely -->
        <input type="hidden" name="total_price" value="<?= $total ?>">

        <button type="submit" class="btn">Place Order</button>
    </form>
</div>
</body>
</html>
