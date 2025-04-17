<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);


session_start();
include '../db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];


$sql = "SELECT cart.cart_id, cart.product_id, products.name, products.price, cart.quantity, products.image_url, products.category 
        FROM cart 
        JOIN products ON cart.product_id = products.product_id 
        WHERE cart.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_product_ids = [];
$cart_items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $row['item_total'] = $row['price'] * $row['quantity'];
    $total += $row['item_total'];
    $cart_items[] = $row;
    $cart_product_ids[] = $row['product_id'];
}

// 💡 Product Recommendations
$recommendations = [];
$recommendations = [];
$cart_product_names = array_column($cart_items, 'name');
$recommend_names = [];

if (!empty($cart_product_names)) {
    $placeholders = implode(',', array_fill(0, count($cart_product_names), '?'));

    // Fetch recommended product names from product_pairs table
    $sql = "
        SELECT DISTINCT recommended_product 
        FROM product_pairs 
        WHERE base_product IN ($placeholders) 
        AND recommended_product NOT IN ($placeholders)
        LIMIT 2
    ";
    $stmt = $conn->prepare($sql);
    $types = str_repeat('s', count($cart_product_names) * 2);
    $stmt->bind_param($types, ...array_merge($cart_product_names, $cart_product_names));
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $recommend_names[] = $row['recommended_product'];
    }

    // Now fetch actual product details
    if (!empty($recommend_names)) {
        $placeholders = implode(',', array_fill(0, count($recommend_names), '?'));
        $sql = "SELECT * FROM products WHERE name IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $types = str_repeat('s', count($recommend_names));
        $stmt->bind_param($types, ...$recommend_names);
        $stmt->execute();
        $recommendations = $stmt->get_result();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../admin/styles.css">
    <style>
      .recommend-section {
    margin-top: 50px;
    padding-top: 20px;
    border-top: 1px solid #ddd;
}

.recommend-section h2 {
    color: #2d6a4f;
    font-size: 24px;
    text-align: center;
    margin-bottom: 30px;
}

.recommend-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 25px;
}

.recommend-box {
    width: 200px;
    padding: 15px;
    background: #fff;
    border: 1px solid #eee;
    border-radius: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.recommend-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
}

.recommend-box img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 12px;
    margin-bottom: 10px;
}

.recommend-box h4 {
    font-size: 16px;
    color: #333;
    margin: 5px 0;
}

.recommend-box p {
    color: #444;
    font-weight: bold;
    margin: 4px 0;
}

.recommend-box a {
    color: #e63946;
    font-weight: bold;
    font-size: 14px;
    text-decoration: none;
}

    </style>
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Home</a>
            <a href="about.php">About Us</a>
            <a href="products.php">Products</a>
            <a href="cart.php">Cart</a>
        </nav>
    </header>

    <section class="cart">
        <h1>Your Shopping Cart</h1>
        <div id="cart-items">
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <img src="../<?= htmlspecialchars($item['image_url']); ?>" class="cart-image">
                    <div class="cart-details">
                        <h3><?= htmlspecialchars($item['name']); ?></h3>
                        <p>₹<?= number_format($item['price'], 2); ?> 
                        x <span id="qty-<?= $item['cart_id']; ?>"><?= $item['quantity']; ?></span> = 
                        ₹<span id="total-<?= $item['cart_id']; ?>"><?= number_format($item['item_total'], 2); ?></span>
                        </p>
                        <button class="qty-btn" onclick="updateQuantity(<?= $item['cart_id']; ?>, -1)">➖</button>
                        <button class="qty-btn" onclick="updateQuantity(<?= $item['cart_id']; ?>, 1)">➕</button>
                        <button class="remove-button" onclick="removeFromCart(<?= $item['cart_id']; ?>)">Remove</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <h2>Total: ₹<span id="cart-total"><?= number_format($total, 2); ?></span></h2>
        <button onclick="clearCart()" class="clear-cart-button">Clear Cart</button>
        <button class="checkout-button" onclick="window.location.href='checkout.php'">Proceed to Checkout</button>

        <!-- ✨ Customization message -->
        <p class="customization-note">
            Want to personalize your order? 💬 For customizations, call 
            <a href="tel:9876543210" style="color: #2d6a4f;">xxxxxxxxxx</a>
        </p>

        <!-- ✨ Recommendations Section -->
        <?php if ($recommendations && $recommendations->num_rows > 0): ?>
            <div class="recommend-section">
                <h2>You might also like</h2>
                <div class="recommend-grid">
                    <?php while ($rec = $recommendations->fetch_assoc()): ?>
                        <div class="recommend-box">
                            <img src="../<?= $rec['image_url']; ?>" alt="<?= $rec['name']; ?>">
                            <h4><?= $rec['name']; ?></h4>
                            <p>₹<?= $rec['price']; ?></p>
                            <a href="product.php?id=<?= $rec['product_id']; ?>" style="color: #e63946;">View</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <script>
        function updateQuantity(cartId, change) {
            fetch("update_cart.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "cart_id=" + cartId + "&change=" + change
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById("qty-" + cartId).innerText = data.quantity;
                    document.getElementById("total-" + cartId).innerText = "₹" + data.itemTotal;
                    document.getElementById("cart-total").innerText = "₹" + data.cartTotal;
                } else {
                    alert("Failed to update quantity.");
                }
            });
        }

        function removeFromCart(cartId) {
            fetch("remove_cart.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "cart_id=" + cartId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert("Failed to remove item.");
                }
            });
        }
        
    </script>
</body>
</html>

<?php $conn->close(); ?>
