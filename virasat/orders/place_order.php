<?php
include 'db_connect.php'; // Database connection

session_start();
$user_id = $_SESSION['user_id']; // Assuming the user is logged in

if (!isset($_POST['cart']) || empty($_POST['cart'])) {
    die(json_encode(["status" => "error", "message" => "Cart is empty."]));
}

$cart = json_decode($_POST['cart'], true); // Decode cart from frontend JSON
$totalAmount = 0;

try {
    // Begin Transaction
    $conn->begin_transaction();

    // Insert order into orders table
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
    $stmt->bind_param("id", $user_id, $totalAmount);
    $stmt->execute();
    $order_id = $conn->insert_id; // Get the last inserted order_id
    $stmt->close();

    // Insert each cart item into order_items and update stock
    foreach ($cart as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];
        $price = $item['price'];
        $totalAmount += $price * $quantity;

        // Insert into order_items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
        $stmt->execute();
        $stmt->close();

        // Reduce stock in products table
        $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");
        $stmt->bind_param("ii", $quantity, $product_id);
        $stmt->execute();
        $stmt->close();
    }

    // Insert payment details (assuming payment is completed)
    $stmt = $conn->prepare("INSERT INTO payments (order_id, user_id, amount, payment_status, payment_method) VALUES (?, ?, ?, 'Completed', 'Credit Card')");
    $stmt->bind_param("iid", $order_id, $user_id, $totalAmount);
    $stmt->execute();
    $stmt->close();

    // Commit Transaction
    $conn->commit();

    echo json_encode(["status" => "success", "message" => "Order placed successfully!", "order_id" => $order_id]);
} catch (Exception $e) {
    $conn->rollback(); // Rollback on error
    echo json_encode(["status" => "error", "message" => "Transaction failed: " . $e->getMessage()]);
}
?>
