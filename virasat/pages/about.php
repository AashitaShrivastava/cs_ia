<?php include 'includes/header.php'; ?> <!-- Include the header -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virasat</title>
    <link rel="stylesheet" href="../admin/styles.css">  <!-- Adjust path if needed -->
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

<section class="about-section">
    <div class="about-content">
        <h1>About Us</h1>
        <p>
            <strong>Virasat</strong>, our clothing line started during the pandemic, has not only provided financial help to the artisans who were struggling to make ends meet but has also given Indian traditional art a worldwide platform.
        </p>
        <p>
            It offers a wide range of sarees, dupattas, and Kurtis, hand-painted and hand-embroidered on certified pure fabrics. These masterpieces are handcrafted by artists from different states of India. Apart from depicting traditional stories in vibrant colors for a classy appeal, they also cater to the chic and young generation.
        </p>
        <p>
            We specialize in customized pieces to meet the demands of every age group, making each piece unique and tailored to your tastes.
        </p>
    </div>

    <hr class="divider">

    <div class="artisan-gallery">
        <h2>Our Artisans</h2>
        <div class="scrolling-gallery">
            <img src="../assets/images/handcrafted.png" alt="Artisan 1">
            <img src="../assets/images/virasat1.png" alt="Artisan 2">
            <img src="../assets/images/traditional techniques.png" alt="Artisan 3">
            <!-- Add more images as needed -->
        </div>
        <p class="gallery-caption">Celebrating the craftsmanship of artisans from across India.</p>
    </div>
</section>

<?php include 'includes/footer.php'; ?> <!-- Include the footer -->
