<?php
session_start();
include '../db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

$sql = "SELECT o.order_id, o.total_price, o.status, o.tracking_id, o.estimated_delivery, d.fullname, d.email 
        FROM orders o 
        JOIN order_details d ON o.order_id = d.order_id 
        ORDER BY o.order_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #fefbf6;
        margin: 0;
        padding: 0;
    }
    h2 {
        text-align: center;
        margin: 40px 0 20px;
        color: #2d6a4f;
    }
    table {
        width: 95%;
        margin: 0 auto 40px;
        border-collapse: collapse;
        background-color: white;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        border-radius: 12px;
        overflow: hidden;
    }
    th, td {
        padding: 14px 18px;
        border-bottom: 1px solid #eee;
        text-align: left;
    }
    th {
        background-color: #2d6a4f;
        color: white;
    }
    tr:hover {
        background-color: #f9f9f9;
    }
    select {
        padding: 6px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }
    button {
        background-color: #2d6a4f;
        color: white;
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
    }
    button:hover {
        background-color: #1b4332;
    }
</style>

</head>
<body>
<h2 style="text-align:center;">Admin Order Management</h2>
<table>
    <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Email</th>
        <th>Total</th>
        <th>Status</th>
        <th>Tracking ID</th>
        <th>Delivery ETA</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <form action="update_order_status.php" method="POST">
                <td><?= $row['order_id'] ?></td>
                <td><?= $row['fullname'] ?></td>
                <td><?= $row['email'] ?></td>
                <td>₹<?= $row['total_price'] ?></td>
                <td>
                    <select name="status">
                        <?php
                        $statuses = ['Processing', 'Out for Delivery', 'Delivered'];
                        foreach ($statuses as $status) {
                            $selected = $row['status'] === $status ? 'selected' : '';
                            echo "<option value='$status' $selected>$status</option>";
                        }
                        ?>
                    </select>
                </td>
                <td><?= $row['tracking_id'] ?></td>
                <td><?= $row['estimated_delivery'] ?></td>
                <td>
                    <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                    <button type="submit">Update</button>
                </td>
            </form>
        </tr>
    <?php endwhile; ?>
</table>
</body>
<p style="text-align: center; margin-top: 20px;">
    <a href="../pages/admin_dashboard.php" style="color: #2d6a4f; text-decoration: none; font-weight: bold;">← Back to Admin Dashboard</a>
</p>

</html>
