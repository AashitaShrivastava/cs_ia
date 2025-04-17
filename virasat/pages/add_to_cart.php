<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $user_id = $_SESSION['user_id'];

    // ✅ Check if the product already exists in the cart
    $sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["success" => false, "error" => "SQL prepare failed: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // ✅ If product exists, increase quantity
        $sql = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(["success" => false, "error" => "SQL prepare failed: " . $conn->error]);
            exit;
        }
        $stmt->bind_param("ii", $user_id, $product_id);
    } else {
        // ✅ If product doesn't exist, insert new entry
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(["success" => false, "error" => "SQL prepare failed: " . $conn->error]);
            exit;
        }
        $stmt->bind_param("ii", $user_id, $product_id);
    }

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);  // ✅ JSON response
    } else {
        echo json_encode(["success" => false, "error" => "Database execution error: " . $stmt->error]);
    }
}

?>
