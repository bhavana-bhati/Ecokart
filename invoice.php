<?php
session_start();
include("db_connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['order_id'])) {
    die("No order selected.");
}

$order_id = intval($_GET['order_id']);


$stmt = $conn->prepare("SELECT o.*, p.name, p.price, u.username, u.phone, u.address 
                        FROM orders o
                        JOIN products p ON o.product_id = p.product_id
                        JOIN users u ON o.user_id = u.id
                        WHERE o.order_id = ? AND o.user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("Order not found.");
}

$orderItems = [];
$userInfo = [];
$total = 0;

while ($row = $res->fetch_assoc()) {
    $orderItems[] = $row;
    $total += $row['total_price'];
    $userInfo = [
        "name" => $row['username'],
        "phone" => $row['phone'],
        "address" => $row['address']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice</title>
<style>
body { font-family: Arial, sans-serif; background: #f5f5dc; margin:0; padding:20px; }
.invoice-box {
    max-width: 800px;
    margin: auto;
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
h2 { color: olive; text-align:center; }
table { width:100%; border-collapse: collapse; margin-top:20px; }
table, th, td { border:1px solid #ddd; }
th, td { padding:10px; text-align:center; }
.total { font-weight:bold; }
.header { display:flex; justify-content:space-between; align-items:center; }
button { margin-top:20px; padding:10px 16px; border:none; border-radius:6px; background:olive; color:white; cursor:pointer; }
button:hover { background:darkolivegreen; }
</style>
</head>
<body>
<div class="invoice-box">
    <div class="header">
        <h2>🧾 Invoice</h2>
        <p><strong>Date:</strong> <?php echo date("d M Y"); ?></p>
    </div>

    <h3>Customer Info</h3>
    <p><strong>Name:</strong> <?php echo $userInfo['name']; ?></p>
    <p><strong>Phone:</strong> <?php echo $userInfo['phone']; ?></p>
    <p><strong>Address:</strong> <?php echo $userInfo['address']; ?></p>

    <h3>Order Details</h3>
    <table>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>
        <?php foreach ($orderItems as $item): ?>
        <tr>
            <td><?php echo $item['name']; ?></td>
            <td><?php echo $item['quantity']; ?></td>
            <td>₹<?php echo $item['price']; ?></td>
            <td>₹<?php echo $item['total_price']; ?></td>
        </tr>
        <?php endforeach; ?>
        <tr class="total">
            <td colspan="3">Grand Total</td>
            <td>₹<?php echo $total; ?></td>
        </tr>
    </table>

    <p><strong>Payment Method:</strong> Cash on Delivery</p>

    <button onclick="window.print()">🖨 Print / Save PDF</button>
    <button onclick="window.location.href='products.php'">← Continue Shopping</button>
</div>
</body>
</html>
