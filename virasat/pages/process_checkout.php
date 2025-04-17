<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include '../db_connect.php';

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../mail/PHPMailer.php';
require '../mail/SMTP.php';
require '../mail/Exception.php';

if (!isset($_SESSION['user_id'])) {
    die("Please log in to continue.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname     = $_POST['fullname']     ?? '';
    $address      = $_POST['address']      ?? '';
    $city         = $_POST['city']         ?? '';
    $postal       = $_POST['postal']       ?? '';
    $phone        = $_POST['phone']        ?? '';
    $email        = $_POST['email']        ?? '';
    $card         = $_POST['card']         ?? '';
    $expiry       = $_POST['expiry']       ?? '';
    $total_price  = $_POST['total_price']  ?? 0;

    if (empty($fullname) || empty($email) || empty($address)) {
        die("Please fill this field.");
    }

    // Generate tracking ID & estimated delivery
    $tracking_id = strtoupper(uniqid("TRK"));
    $delivery_date = date('Y-m-d', strtotime('+5 days'));

    // Insert order
    $order_sql = "INSERT INTO orders (user_id, total_price, status, tracking_id, estimated_delivery) 
                  VALUES (?, ?, 'Processing', ?, ?)";
    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param("idss", $user_id, $total_price, $tracking_id, $delivery_date);
    $stmt->execute();
    $order_id = $conn->insert_id;

    // Insert shipping details
    $last4 = substr($card, -4);
    $details_sql = "INSERT INTO order_details 
        (order_id, fullname, address, city, postal_code, phone, email, card_last4, expiry) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($details_sql);
    $stmt->bind_param("issssssss", $order_id, $fullname, $address, $city, $postal, $phone, $email, $last4, $expiry);
    $stmt->execute();

    // Get products for invoice/email
    $products_sql = "SELECT p.name, c.quantity, p.price 
                     FROM cart c 
                     JOIN products p ON c.product_id = p.product_id 
                     WHERE c.user_id = ?";
    $stmt = $conn->prepare($products_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $product_lines = "";
    while ($row = $result->fetch_assoc()) {
        $product_lines .= "{$row['name']} × {$row['quantity']} = ₹" . ($row['quantity'] * $row['price']) . "\n";
    }

    // Clear cart
    $delete_sql = "DELETE FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // ✉️ Send emails
    $mail = new PHPMailer(true);
    try {
        // Server config
        //$mail->SMTPDebug = 2;
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'virasat.orders@gmail.com';
        $mail->Password   = 'piwo fqnb hism pmme';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // ✉️ Send to Customer
        $mail->setFrom('virasat.order@gmail.com', 'Virasat Orders');
        $mail->addAddress($email, $fullname);
        $mail->Subject = 'Order Confirmation - Virasat';
        $mail->Body = "Dear $fullname,\n\nThank you for shopping at Virasat!\n\n💾 Order ID: $order_id\n📦 Tracking ID: $tracking_id\n🗓️ Estimated Delivery: $delivery_date\n\nOrder Summary:\n$product_lines\nTotal: ₹$total_price\n\nWe’ll update you once your order is shipped.\n\nWarm regards,\nVirasat Team";
        $mail->send();

        // ✉️ Send to Admin
        $mail->clearAddresses();
        $mail->addAddress('virasat.order@gmail.com');
        $mail->Subject = "📥 New Order Received - ID $order_id";
        $mail->Body = "A new order has been placed on Virasat.\n\n👤 Customer: $fullname\n📧 Email: $email\n📞 Phone: $phone\n📍 Address: $address, $city, $postal\n\n📎 Order Summary:\n$product_lines\nTotal: ₹$total_price\n\n🔁 Tracking ID: $tracking_id\n🗓 ETA: $delivery_date\n\nLog into the admin dashboard to manage this order.";
        $mail->send();

    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
        exit;
    }

    // ✅ Redirect to order confirmation
    header("Location: order-confirmation.php?track=$tracking_id");
    exit;
} else {
    echo "Please submit the form correctly.";
}
?>
