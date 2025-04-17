<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<?php include 'db_connect.php'; ?> 
<?php include 'header.php'; ?> <!-- Include header -->
<!DOCTYPE html>
<html lang="en">
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virasat | Home</title>
    <link rel="stylesheet" href="../admin/styles.css"> <!-- Add this line here -->
</head>
<body>
    <header>
        <nav>
            <a href="index.php">Home</a>
            <a href="about.php">About Us</a>
            <a href="products.php">Products</a>
            <a href="cart.php">Cart</a>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <a href="admin_dashboard.php">Admin Panel</a>
<?php endif; ?>

        </nav>
    </header>
</body>
</html>

<section class="hero">
    <div class="hero-text">
        <h1>Welcome to Virasat</h1>
        <p>Discover the heritage of hand-painted art and traditional craftsmanship.</p>
        <a href="products.php" class="cta-button">Shop Now</a>
    </div>
</section>

<section id="intro" class="section-card">
    <h2>Our Story</h2>
    <p>At Viraasat, we celebrate art and tradition through our hand-painted and embroidered masterpieces.</p>
    <p>Each creation tells a story of culture, heritage, and timeless elegance.</p>
    <p>From intricate paintings to exquisite garments, our collection is designed to bring the beauty of</p>
    <p>traditional art to modern lives.</p>
</section>

<section class="features">
    <div class="feature">
    <img src="../assets/images/handcrafted.png" alt="Handcrafted Art">
        <h3>Handcrafted Art</h3>
        <p>Each piece is uniquely crafted by skilled artisans.</p>
    </div>
    <div class="feature">
    <img src="../assets/images/traditional techniques.png" alt="Traditional Techniques">
        <h3>Traditional Techniques</h3>
        <p>Rooted in heritage, created with care.</p>
    </div>
    <div class="feature">
    <img src="../assets/images/unique designs.png" alt="Unique Designs">
        <h3>Unique Designs</h3>
        <p>Exclusive, one-of-a-kind creations.</p>
    </div>
</section>

<?php include 'footer.php'; ?> <!-- Include footer -->
