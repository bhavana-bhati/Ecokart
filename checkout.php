<?php
session_start();
include("db_connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$userStmt = $conn->prepare("SELECT username, phone, address FROM users WHERE id=?");
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();

$orderItems = [];
$total = 0;

if (isset($_POST['product_id'])) {
    $pid = intval($_POST['product_id']);
    $qty = max(1, intval($_POST['quantity']));

    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id=?");
    $stmt->bind_param("i", $pid);
    $stmt->execute();
    $prod = $stmt->get_result()->fetch_assoc();

    if ($prod) {
        $subtotal = $prod['price'] * $qty;
        $orderItems[] = [
            "product_id" => $pid,
            "name" => $prod['name'],
            "image" => $prod['image'],
            "price" => $prod['price'],
            "quantity" => $qty,
            "subtotal" => $subtotal
        ];
        $total += $subtotal;
    }
}

else {
    $stmt = $conn->prepare("
        SELECT c.product_id, p.name, p.image, p.price, c.quantity
        FROM cart c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.user_id=?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
        $subtotal = $row['price'] * $row['quantity'];
        $orderItems[] = [
            "product_id" => $row['product_id'],
            "name" => $row['name'],
            "image" => $row['image'],
            "price" => $row['price'],
            "quantity" => $row['quantity'],
            "subtotal" => $subtotal
        ];
        $total += $subtotal;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout</title>
<style>
body { font-family: Arial, sans-serif; margin:0; background: beige; }
.container { max-width: 900px; margin: 30px auto; background: #fff; padding: 25px; border-radius: 12px; box-shadow:0 4px 10px rgba(0,0,0,.1); }
h2 { margin-bottom:20px; color: olive; }
.order-summary { margin-bottom: 25px; }
.item { display:flex; align-items:center; gap:15px; margin-bottom:15px; border-bottom:1px solid #eee; padding-bottom:10px; }
.item img { width:80px; height:80px; object-fit:cover; border-radius:8px; }
.item .info { flex:1; }
.total { text-align:right; font-weight:bold; font-size:18px; margin-top:10px; }

form label { display:block; margin:10px 0 5px; font-weight:bold; }
form input, form textarea { width:100%; padding:10px; border:1px solid #ccc; border-radius:6px; }

.payment { margin:20px 0; }
.payment label { font-weight:normal; }

button { background: olive; color:white; padding:12px 20px; border:none; border-radius:8px; cursor:pointer; font-size:16px; }
button:hover { background: darkolivegreen; }

.continue { margin-top:20px; text-align:center; }
.continue button { background:#888; }

.error { color: red; font-size: 14px; margin-top: 4px; display: none; }
</style>
</head>
<body>
<div class="container">
    <h2>🧾 Checkout</h2>

    <div class="order-summary">
        <h3>Order Summary</h3>
        <?php if (empty($orderItems)): ?>
            <p>Your cart is empty!</p>
        <?php else: foreach ($orderItems as $item): ?>
            <div class="item">
                <img src="images/<?php echo basename($item['image']); ?>" alt="">
                <div class="info">
                    <strong><?php echo $item['name']; ?></strong><br>
                    Qty: <?php echo $item['quantity']; ?> × ₹<?php echo $item['price']; ?>
                </div>
                <div>₹<?php echo $item['subtotal']; ?></div>
            </div>
        <?php endforeach; ?>
        <div class="total">Total: ₹<?php echo $total; ?></div>
        <?php endif; ?>
    </div>

    <form id="checkoutForm" action="place_order.php" method="POST">
        <input type="hidden" name="from" value="<?php echo isset($_POST['product_id']) ? 'buy' : 'cart'; ?>">
        <?php foreach ($orderItems as $i => $item): ?>
            <input type="hidden" name="items[<?php echo $i; ?>][product_id]" value="<?php echo $item['product_id']; ?>">
            <input type="hidden" name="items[<?php echo $i; ?>][quantity]" value="<?php echo $item['quantity']; ?>">
            <input type="hidden" name="items[<?php echo $i; ?>][subtotal]" value="<?php echo $item['subtotal']; ?>">
        <?php endforeach; ?>
        <input type="hidden" name="total" value="<?php echo $total; ?>">

        <h3>Shipping Info</h3>

        <label>Name</label>
        <input type="text" name="username" value="<?php echo $user['username']; ?>" required>
        <div class="error" id="errName">Please enter your name</div>

        <label>Phone</label>
        <input type="text" name="phone" value="<?php echo $user['phone']; ?>" required>
        <div class="error" id="errPhone">Please enter your phone number</div>

        <label>Address</label>
        <textarea name="address" required><?php echo $user['address']; ?></textarea>
        <div class="error" id="errAddress">Please enter your address</div>

        <label>City</label>
        <input type="text" name="city" required>
        <div class="error" id="errCity">Please enter your city</div>

        <label>Pincode</label>
        <input type="text" name="pincode" required>
        <div class="error" id="errPincode">Please enter your pincode</div>

        <div class="payment">
            <h3>Payment Method</h3>
            <label><input type="radio" name="payment" value="COD" checked> Cash on Delivery</label>
        </div>

        <button type="submit">✅ Place Order</button>
    </form>

    <div class="continue">
        <button onclick="history.back()">← Continue Shopping</button>
    </div>
</div>

<script>
document.getElementById("checkoutForm").addEventListener("submit", function(e) {
    let valid = true;

    
    document.querySelectorAll(".error").forEach(el => el.style.display = "none");

    const fields = [
        { name: "username", error: "errName" },
        { name: "phone", error: "errPhone" },
        { name: "address", error: "errAddress" },
        { name: "city", error: "errCity" },
        { name: "pincode", error: "errPincode" },
    ];

    fields.forEach(f => {
        let value = document.querySelector(`[name='${f.name}']`).value.trim();
        if (!value) {
            document.getElementById(f.error).style.display = "block";
            valid = false;
        }
    });

    if (!valid) e.preventDefault();
});
</script>
</body>
</html>
