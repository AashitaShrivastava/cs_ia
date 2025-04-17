<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cart_id = $_POST['cart_id'];
    $change = $_POST['change'];

    // ✅ Get current quantity
    $sql = "SELECT quantity FROM cart WHERE cart_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        echo json_encode(["success" => false, "error" => "Item not found"]);
        exit;
    }

    $new_quantity = $row['quantity'] + $change;
    if ($new_quantity <= 0) {
        $sql = "DELETE FROM cart WHERE cart_id = ?";
    } else {
        $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
    }

    $stmt = $conn->prepare($sql);
    if ($new_quantity <= 0) {
        $stmt->bind_param("i", $cart_id);
    } else {
        $stmt->bind_param("ii", $new_quantity, $cart_id);
    }

    if ($stmt->execute()) {
        // ✅ Get updated cart total
        $sql = "SELECT SUM(products.price * cart.quantity) AS total FROM cart JOIN products ON cart.product_id = products.product_id WHERE cart.user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $totalRow = $result->fetch_assoc();
        $cartTotal = $totalRow['total'] ?? 0;

        echo json_encode(["success" => true, "quantity" => $new_quantity, "cartTotal" => number_format($cartTotal, 2)]);
    } else {
        echo json_encode(["success" => false, "error" => "Database update failed"]);
    }
}
?>
