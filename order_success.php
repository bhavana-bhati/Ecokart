<?php
session_start();
include("db_connect.php");

if (!isset($_SESSION['last_orders'])) {
    header("Location: products.php");
    exit();
}

$order_ids = $_SESSION['last_orders'];
unset($_SESSION['last_orders']);

// Fetch orders
$placeholders = implode(",", array_fill(0, count($order_ids), "?"));
$types = str_repeat("i", count($order_ids));

$stmt = $conn->prepare("SELECT o.order_id, p.name, o.quantity, o.total_price 
                        FROM orders o 
                        JOIN products p ON o.product_id = p.product_id 
                        WHERE o.order_id IN ($placeholders)");
$stmt->bind_param($types, ...$order_ids);
$stmt->execute();
$res = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Success</title>
<style>
body { font-family: Arial, sans-serif; background: beige; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
.box { background:#fff; padding:30px; border-radius:12px; text-align:center; box-shadow:0 4px 12px rgba(0,0,0,.15); max-width:500px; }
h2 { color:green; margin-bottom:20px; }
ul { text-align:left; padding:0; list-style:none; }
li { margin:8px 0; }
 .continue {
    text-align: center;
    margin-top: 25px;
}

.continue button {
    background: olive;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 10px 20px;
    margin: 0 8px;
    cursor: pointer;
    transition: background 0.3s;
}

.continue button:hover {
    background: darkolivegreen;
}

button:hover { background:darkolivegreen; }
</style>
</head>
<body>
<div class="box">
    <h2>✅ Order Placed Successfully!</h2>
    <p>Your order details:</p>
    <ul>
        <?php while($row = $res->fetch_assoc()): ?>
            <li><?php echo $row['name']; ?> × <?php echo $row['quantity']; ?> → ₹<?php echo $row['total_price']; ?></li>
        <?php endwhile; ?>
    </ul>
    <p>Payment Method: Cash on Delivery</p>
<div class="continue">
    <button onclick="window.location.href='product.html'">← Continue Shopping</button>
    <button onclick="window.location.href='my_order.php'">📦 View My Orders</button>
</div>

</div>
</body>
</html>
