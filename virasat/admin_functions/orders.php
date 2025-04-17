<?php
session_start();
include '../db_connect.php';

// Only allow admins
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Access denied.");
}

// Fetch all orders + details
$sql = "SELECT o.order_id, o.total_price, o.status, o.tracking_id, o.estimated_delivery,
               od.fullname, od.email, od.address, od.city, od.postal_code, od.phone
        FROM orders o
        JOIN order_details od ON o.order_id = od.order_id
        ORDER BY o.order_id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Orders Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #f4f4f4;
            padding: 40px;
        }

        h2 {
            text-align: center;
            color: #2d6a4f;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #2d6a4f;
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        select {
            padding: 6px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        form {
            margin: 0;
        }

        .btn {
            background: #e63946;
            color: white;
            padding: 6px 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn:hover {
            background: #c91d2e;
        }
    </style>
</head>
<body>

<h2>All Orders</h2>

<table>
    <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Email</th>
        <th>Address</th>
        <th>Phone</th>
        <th>Total</th>
        <th>Status</th>
        <th>Tracking ID</th>
        <th>Delivery Date</th>
        <th>Update</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['order_id'] ?></td>
            <td><?= htmlspecialchars($row['fullname']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['address']) . ', ' . $row['city'] . ' - ' . $row['postal_code'] ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td>₹<?= $row['total_price'] ?></td>
            <td><?= $row['status'] ?></td>
            <td><?= $row['tracking_id'] ?></td>
            <td><?= $row['estimated_delivery'] ?></td>
            <td>
                <form method="POST" action="update_status.php">
                    <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                    <select name="new_status">
                        <option value="Pending">Pending</option>
                        <option value="Processing">Processing</option>
                        <option value="Shipped">Shipped</option>
                        <option value="Out for Delivery">Out for Delivery</option>
                        <option value="Delivered">Delivered</option>
                    </select>
                    <button type="submit" class="btn">Update</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>

</table>

</body>
</html>
