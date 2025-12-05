<?php
session_start();
include("db_connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $items   = $_POST['items'];
    $total   = floatval($_POST['total']);
    $name    = $_POST['username'];
    $phone   = $_POST['phone'];
    $address = $_POST['address'];
    $city    = $_POST['city'] ?? '';      // optional
    $pincode = $_POST['pincode'] ?? '';  // optional
    $payment = $_POST['payment'];

    $order_ids = [];

    foreach ($items as $item) {
        $pid      = intval($item['product_id']);
        $qty      = intval($item['quantity']);
        $subtotal = floatval($item['subtotal']);

        $stmt = $conn->prepare("
            INSERT INTO orders 
                (user_id, product_id, quantity, total_price, order_status, 
                 customer_name, customer_phone, customer_address, customer_city, customer_pincode, payment_method) 
            VALUES (?, ?, ?, ?, 'Pending', ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "iiidssssss", 
            $user_id, $pid, $qty, $subtotal, 
            $name, $phone, $address, $city, $pincode, $payment
        );
        $stmt->execute();
        $order_ids[] = $stmt->insert_id;
    }

    // 🧹 Clear cart if from cart
    if ($_POST['from'] === "cart") {
        $conn->query("DELETE FROM cart WHERE user_id=$user_id");
    }

    // Redirect to success page
    $_SESSION['last_orders'] = $order_ids;
    header("Location: order_success.php");
    exit();
}
?> 