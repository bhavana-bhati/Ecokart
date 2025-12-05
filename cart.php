<?php
session_start();
include("db_connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === "update") {
        $cart_id = intval($_POST['cart_id']);
        $qty = max(1, intval($_POST['quantity']));
        $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE cart_id=? AND user_id=?");
        $stmt->bind_param("iii", $qty, $cart_id, $user_id);
        $stmt->execute();
        exit("updated");
    }

    if ($_POST['action'] === "remove") {
        $cart_id = intval($_POST['cart_id']);
        $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id=? AND user_id=?");
        $stmt->bind_param("ii", $cart_id, $user_id);
        $stmt->execute();
        exit("removed");
    }
}

// -----------------------------
// ✅ Handle Add to Cart
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;

    $stmt = $conn->prepare("SELECT cart_id, quantity FROM cart WHERE user_id=? AND product_id=?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $newQty = $row['quantity'] + $quantity;
        $update = $conn->prepare("UPDATE cart SET quantity=? WHERE cart_id=?");
        $update->bind_param("ii", $newQty, $row['cart_id']);
        $update->execute();
    } else {
        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert->bind_param("iii", $user_id, $product_id, $quantity);
        $insert->execute();
    }

    header("Location: cart.php");
    exit();
}

// -----------------------------
// ✅ Fetch cart items
// -----------------------------
$stmt = $conn->prepare("
    SELECT c.cart_id, c.product_id, p.name, p.image, p.price, c.quantity,
           (p.price * c.quantity) AS subtotal
    FROM cart c
    JOIN products p ON c.product_id = p.product_id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cartItems = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart</title>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
/* --- your CSS remains same --- */
  
      body { font-family: Arial, sans-serif; background: #f5f5dc; 
        /* beige */ margin: 0; padding: 0; }
         .container { max-width: 900px; margin: 40px auto; background: #fffdf7; padding: 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); } 
         h2 { margin-bottom: 20px; color: #556b2f; } 
         .empty { text-align: center; font-size: 18px; padding: 40px; color: #666; } 
         .cart-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px; } 
      .cart-item { position: relative; background: #fff; border: 1px solid #ddd; border-radius: 12px; padding: 15px; display: flex; flex-direction: column; align-items: center; }
       .cart-item img { width: 200px; height: 200px; object-fit: cover; border-radius: 8px; margin-bottom: 15px; } 
      .details { text-align: center; } 
      .details h3 { margin: 0 0 10px; color: #333; }
       .details p { margin: 5px 0; color: #444; } 
       .qty { display: flex; justify-content: center; align-items: center; gap: 10px; margin: 10px 0; } 
       .qty button { padding: 6px 12px; background: #6b8e23; color: #fff; border: none; border-radius: 6px; cursor: pointer; } 
       .qty button:hover { background: #556b2f; } .qty input { width: 50px; text-align: center; } 
       .remove-btn { position: absolute; top: 12px; right: 10px; background: red; color: #fff; border: none; border-radius: 50%; width: 28px; height: 28px; cursor: pointer; display:flex; align-items:center; justify-content:center; font-weight:bold; }
        .total { text-align: right; font-size: 18px; margin-top: 20px; font-weight: bold; }
         .buy-now { display: block; width: 100%; background: #6b8e23; color: #fff; border: none; padding: 10px; border-radius: 8px; font-size: 15px; cursor: pointer; margin-top: 10px; } 
        .buy-now:hover { background: #556b2f; } .continue { text-align:center; margin:20px 0; }
         .continue button { background:#444; color:#fff; border:none; border-radius:8px; padding:10px 20px; cursor:pointer; } 
         .continue button:hover { background:#222; } @media (max-width: 768px) { 
        .cart-item img { width: 150px; height: 150px; } }
/* ✅ Toast Notification */
#toast {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #333;
    color: #fff;
    padding: 12px 20px;
    border-radius: 8px;
    font-size: 14px;
    display: none;
    opacity: 0;
    transition: opacity 0.5s ease-in-out;
    z-index: 9999;
}
</style>
</head>
<body>

<div class="container">
    <h2>🛒 Your Cart</h2>

    <?php if ($cartItems->num_rows === 0): ?>
        <div class="empty">🛍️ Your cart is empty!</div>
    <?php else: ?>
        <div class="cart-grid">
        <?php 
        $total = 0;
        while ($row = $cartItems->fetch_assoc()):
            $total += $row['subtotal'];
        ?>
            <div class="cart-item" data-id="<?php echo $row['cart_id']; ?>">
                <button class="remove-btn">×</button>
                <img src="images/<?php echo basename($row['image']); ?>" alt="">
                <div class="details">
                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                    <p>Price: ₹<?php echo $row['price']; ?></p>
                    <div class="qty">
                        <button class="updateQty" data-change="-1">−</button>
                        <input type="number" value="<?php echo $row['quantity']; ?>" min="1">
                        <button class="updateQty" data-change="1">+</button>
                    </div>
                    <p class="subtotal">Subtotal: ₹<?php echo $row['subtotal']; ?></p>

                    <form action="checkout.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
                        <input type="hidden" name="quantity" value="<?php echo $row['quantity']; ?>">
                        <button type="submit" class="buy-now">Buy Now</button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
        </div>
        <div class="total">Total: ₹<span id="cartTotal"><?php echo $total; ?></span></div>
    <?php endif; ?>
</div>
<div class="continue">
    <button onclick="history.back()">← Continue Shopping</button>
</div>

<!-- ✅ Toast -->
<div id="toast"></div>

<script>
function updateTotal(){
    let total = 0;
    $(".subtotal").each(function(){
        let val = parseFloat($(this).text().replace("Subtotal: ₹",""));
        if(!isNaN(val)) total += val;
    });
    $("#cartTotal").text(total);
}

// ✅ Show toast
function showToast(message, color="#333") {
    $("#toast").text(message).css("background", color).fadeIn(200).css("opacity", "1");
    setTimeout(function(){
        $("#toast").fadeOut(500).css("opacity", "0");
    }, 2000);
}

// Update Qty
$(document).on("click", ".updateQty", function(){
    let parent = $(this).closest(".cart-item");
    let input = parent.find("input[type=number]");
    let qty = parseInt(input.val()) + parseInt($(this).data("change"));
    if (qty < 1) qty = 1;
    input.val(qty);

    let cart_id = parent.data("id");
    let price = parseFloat(parent.find("p:contains('Price')").text().replace("Price: ₹",""));
    let subtotal = qty * price;
    parent.find(".subtotal").text("Subtotal: ₹" + subtotal);

    updateTotal();

    $.post("cart.php", {action: "update", cart_id: cart_id, quantity: qty}, function(res){
        if(res === "updated") showToast("✅ Quantity updated", "#6b8e23");
    });
});

// Remove Item
$(document).on("click", ".remove-btn", function(){
    let parent = $(this).closest(".cart-item");
    let cart_id = parent.data("id");

    parent.remove();
    updateTotal();

    $.post("cart.php", {action: "remove", cart_id: cart_id}, function(res){
        if(res === "removed") showToast("🗑️ Item removed", "red");
    });
});
</script>

</body>
</html>
