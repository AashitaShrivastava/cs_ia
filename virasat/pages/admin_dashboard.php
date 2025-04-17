<?php
session_start();
include '../db_connect.php';

// ✅ Protect access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You must be an admin to view this page.");
}

// ✅ Fetch all products
$sql = "SELECT * FROM products ORDER BY product_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fefbf6;
        }
        h1 {
            text-align: center;
            color: #2d6a4f;
            margin-top: 40px;
        }
        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
        }
        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #2d6a4f;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .badge-low {
            color: white;
            background-color: #e63946;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 8px;
        }
        .delete-btn {
            background-color: #d62828;
            color: white;
            padding: 6px 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #b91c1c;
        }
    </style>
</head>
<body>

<h1>Admin Dashboard - Product Management</h1>
<?php if ($_SESSION['role'] === 'admin'): ?>
    <p style="text-align: center;">
        <a href="index.php" style="color: #2d6a4f; text-decoration: underline;">← Back to Website</a>
        <a href="../admin_functions/manage_order.php" style="color: #2d6a4f; text-decoration: underline;">Manage Orders</a>

    </p>
<?php endif; ?>

<table>
    <tr>
        <th>ID</th>
        <th>Product</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Image</th>
        <th>Action</th>
    </tr>
    <?php while ($product = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $product['product_id'] ?></td>
            <td><?= $product['name'] ?></td>
            <td>₹<?= $product['price'] ?></td>
            <td>
                <?= $product['stock'] ?>
                <?php if ($product['stock'] < 3): ?>
                    <span class="badge-low">Low Stock</span>
                <?php endif; ?>
            </td>
            <td>
                <img src="../<?= $product['image_url'] ?>" alt="<?= $product['name'] ?>" width="60" height="60" style="border-radius: 8px;">
            </td>
            <td>
                <form method="POST" action="delete_product.php" onsubmit="return confirm('Are you sure you want to delete this product?');">
                    <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                    <button type="submit" class="delete-btn">Delete</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
    <p style="text-align: center; font-size: 14px; color: #888;">
    Logged in as <strong>Admin</strong>
</p>

</table>

</body>
</html>
