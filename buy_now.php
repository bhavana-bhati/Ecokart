<?php
session_start();
include("db_connect.php");

// ✅ Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Fetch user info
$stmt = $conn->prepare("SELECT username, phone, address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
    body {
        font-family: Arial, sans-serif;
        background: beige; /* ✅ beige theme */
        margin: 0;
        padding: 0;
    }
    .container {
        max-width: 700px;
        margin: 40px auto;
        padding: 25px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 6px 14px rgba(0,0,0,0.15);
    }
    h2 {
        text-align: center;
        color: olive;
        margin-bottom: 20px;
    }
    form {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }
    label {
        font-weight: bold;
        color: #444;
    }
    input, textarea, select {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 15px;
        transition: border 0.2s;
    }
    input:focus, textarea:focus, select:focus {
        outline: none;
        border-color: olive;
        box-shadow: 0 0 6px rgba(128,128,0,0.3);
    }
    textarea { resize: vertical; }

    .btn {
        padding: 12px;
        background: olive;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s;
    }
    .btn:hover { background: darkolivegreen; }

    .back-btn {
        background: #ccc;
        color: #333;
        margin-top: 10px;
    }
    .back-btn:hover { background: #bbb; }

    .field-group {
        display: flex;
        gap: 20px;
    }
    .field-group .half {
        flex: 1;
    }

    /* 🛒 Payment options as styled cards */
    .payment-options {
        display: flex;
        gap: 20px;
        margin-top: 10px;
    }
    .payment-card {
        flex: 1;
        border: 2px solid #ccc;
        padding: 15px;
        border-radius: 10px;
        text-align: center;
        cursor: pointer;
        transition: 0.3s;
    }
    .payment-card:hover {
        border-color: olive;
        background: #f6f6e8;
    }
    .payment-card input {
        display: none;
    }
    .payment-card.selected {
        border-color: olive;
        background: #f6f6e8;
    }
</style>
</head>
<body>
<div class="container">
    <h2>Checkout</h2>
    <form method="POST" action="place_order.php">
        <!-- Name & Phone -->
        <div class="field-group">
            <div class="half">
                <label for="username">Full Name</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="half">
                <label for="phone">Phone</label>
                <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
        </div>

        <!-- Address -->
        <label for="address">Address</label>
        <textarea name="address" rows="3" required><?php echo htmlspecialchars($user['address']); ?></textarea>

        <!-- City + Pincode -->
        <div class="field-group">
            <div class="half">
                <label for="city">City</label>
                <input type="text" name="city" required>
            </div>
            <div class="half">
                <label for="pincode">Pincode</label>
                <input type="text" name="pincode" required>
            </div>
        </div>

        <!-- Payment method -->
        <label>Payment Method</label>
        <div class="payment-options">
            <label class="payment-card">
                <input type="radio" name="payment" value="COD" required>
                <i class="fa fa-money-bill-wave"></i><br> Cash on Delivery
            </label>
            <label class="payment-card">
                <input type="radio" name="payment" value="UPI" required>
                <i class="fa fa-mobile-alt"></i><br> UPI / Wallet
            </label>
        </div>

        <!-- Buttons -->
        <button type="submit" class="btn">Place Order</button>
        <button type="button" class="btn back-btn" onclick="history.back()">← Back</button>
    </form>
</div>

<script>
// Highlight selected payment card
document.querySelectorAll('.payment-card').forEach(card => {
    card.addEventListener('click', function() {
        document.querySelectorAll('.payment-card').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        this.querySelector("input").checked = true;
    });
});
</script>
</body>
</html>
