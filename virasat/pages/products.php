<?php
include '../db_connect.php'; // Connect to database

$sql = "SELECT product_id, name, price, image_url, description, stock FROM products"; // Fetch stock too!
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virasat | Products</title>
    <link rel="stylesheet" href="../admin/styles.css"> 
    <style>
        .low-stock {
            background-color: #e63946;
            color: white;
            font-size: 11px;
            padding: 3px 6px;
            border-radius: 8px;
            margin-left: 6px;
            text-transform: uppercase;
        }

        .modal {
            display: none; 
            position: fixed; 
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0px 0px 10px 0px black;
        }

        .close-button {
            float: right;
            font-size: 20px;
            cursor: pointer;
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

    <section class="products">
        <h1>Our Collection</h1>
        <div class="product-grid">
            <?php 
            while ($row = $result->fetch_assoc()): 
            ?>
                <div class="product-card">
                    <img src="../<?php echo htmlspecialchars($row['image_url']); ?>" 
                         alt="<?php echo htmlspecialchars($row['name']); ?>" 
                         class="product-image">
                    <h3>
                        <?= htmlspecialchars($row['name']); ?>
                        <?php if ($row['stock'] < 5): ?>
                            <span class="low-stock">Running Out!</span>
                        <?php endif; ?>
                    </h3>
                    <p class="price">₹<?php echo number_format($row["price"], 2); ?></p>
                    <p><?php echo htmlspecialchars($row["description"]); ?></p>
                    <button type="button" 
                            class="add-to-cart-button" 
                            onclick="addToCart('<?php echo htmlspecialchars($row["name"]); ?>', 
                                                '<?php echo $row["price"]; ?>', 
                                                '<?php echo htmlspecialchars($row["image_url"]); ?>', 
                                                '<?php echo $row["product_id"]; ?>')">
                        Add to Cart
                    </button>
                </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- Modal Pop-up HTML -->
    <div id="cartModal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal()">&times;</span>
            <p id="modalText"></p>
        </div>
    </div>

    <div class="cart-link">
        <a href="cart.php" class="view-cart-button">View Cart</a>
    </div>

    <footer>
        <p>&copy; 2024 Virasat | Embrace Tradition, Celebrate Art</p>
    </footer>

    <script>
        function addToCart(productName, price, imageUrl, productId) {
            fetch("add_to_cart.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `product_id=${productId}`
            })
            .then(response => response.json())  
            .then(data => {
                if (data.success) {
                    showModal(`${productName} has been added to your cart!`);
                } else {
                    alert("Failed to add item to cart. " + (data.error || ""));
                }
            })
            .catch(error => {
                alert("An error occurred while adding the product.");
            });
        }

        function showModal(message) {
            const modal = document.getElementById("cartModal");
            const modalText = document.getElementById("modalText");
            modalText.innerText = message;
            modal.style.display = "block";
        }

        function closeModal() {
            document.getElementById("cartModal").style.display = "none";
        }

        window.onclick = function(event) {
            const modal = document.getElementById("cartModal");
            if (event.target === modal) {
                closeModal();
            }
        };
    </script>
</body>
</html>

<?php $conn->close(); ?>
