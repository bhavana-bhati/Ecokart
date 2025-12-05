<?php
session_start();
include("db_connect.php");

if (!isset($_GET['id'])) {
    echo "❌ Product not found! (missing ID)";
    exit;
}

$product_id = intval($_GET['id']);

// ✅ Fetch product
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "❌ Product not found in database!";
    exit;
}

// ✅ Check if this product is in wishlist
$in_wishlist = false;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $wishStmt = $conn->prepare("SELECT 1 FROM wishlist WHERE user_id = ? AND product_id = ?");
    $wishStmt->bind_param("ii", $user_id, $product_id);
    $wishStmt->execute();
    $in_wishlist = $wishStmt->get_result()->num_rows > 0;
}

// ✅ Fetch related products
$relatedStmt = $conn->prepare("SELECT * FROM products WHERE category = ? AND product_id != ? LIMIT 4");
$relatedStmt->bind_param("si", $product['category'], $product_id);
$relatedStmt->execute();
$related = $relatedStmt->get_result();

// ✅ Get related products wishlist state
$related_wishlist = [];
if (isset($_SESSION['user_id'])) {
    $rel_ids = [];
    while ($rel_tmp = $related->fetch_assoc()) {
        $rel_ids[] = $rel_tmp['product_id'];
        $related_products[] = $rel_tmp;
    }
    if (!empty($rel_ids)) {
        $id_placeholders = implode(",", array_fill(0, count($rel_ids), "?"));
        $types = str_repeat("i", count($rel_ids) + 1);
        $params = array_merge([$_SESSION['user_id']], $rel_ids);

        $sql = "SELECT product_id FROM wishlist WHERE user_id = ? AND product_id IN ($id_placeholders)";
        $wishRelStmt = $conn->prepare($sql);
        $wishRelStmt->bind_param($types, ...$params);
        $wishRelStmt->execute();
        $wishRes = $wishRelStmt->get_result();
        while ($row = $wishRes->fetch_assoc()) {
            $related_wishlist[$row['product_id']] = true;
        }
    }
} else {
    // reset pointer since we fetched once
    $related->data_seek(0);
    while ($rel_tmp = $related->fetch_assoc()) {
        $related_products[] = $rel_tmp;
    }
}

// ✅ Fetch reviews if table exists
$reviews = [];
if ($conn->query("SHOW TABLES LIKE 'reviews'")->num_rows) {
    $revStmt = $conn->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY created_at DESC LIMIT 5");
    $revStmt->bind_param("i", $product_id);
    $revStmt->execute();
    $reviews = $revStmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo $product['name']; ?> - Product Details</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
    body { 
    font-family: Arial, sans-serif; 
    margin: 0; 
    background: beige; /* ✅ beige background */
}
.container { 
    max-width: 1100px; 
    margin: 30px auto; 
    padding: 20px; 
    background: #fff; 
    border-radius: 12px; 
    box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
}

/* --- Product section (2 columns: left image, right info) --- */
.product-detail { 
    display: flex; 
    gap: 30px; 
    margin-bottom: 40px; 
}
.image-box { position: relative; }
.product-detail img { 
    width: 420px; 
    border-radius: 12px; 
    object-fit: cover; 
}
.wishlist-btn-main { 
    position: absolute; 
    top: 15px; 
    right: 15px; 
    font-size: 26px; 
    color: gray; 
    cursor: pointer; 
}
.wishlist-btn-main.active { color: red; }

.info { 
    flex: 1; 
    display: flex; 
    flex-direction: column; 
    gap: 12px; 
    justify-content: flex-start;
}
.info h2 { margin: 0; font-size: 28px; }
.short-desc { font-size: 15px; color: #555; line-height: 1.5; }
.price { font-size: 22px; color: green; font-weight: bold; }

/* Qty + Subtotal */
.qty { display: flex; align-items: center; gap: 10px; }
.qty button { 
    padding: 6px 12px; 
    font-size: 18px; 
    background: #eee; 
    border: none; 
    border-radius: 6px; 
    cursor: pointer; 
    color: olive; /* ✅ olive for + / - */
    font-weight: bold; 
}
.qty button:hover { background: #ddd; }
.qty input { width: 60px; text-align: center; padding: 5px; font-size: 16px; }

.subtotal { margin: 10px 0; font-weight: bold; }

/* ✅ Buttons in olive */
button { 
    padding: 10px 20px; 
    background: olive; 
    color: #fff; 
    border: none; 
    border-radius: 6px; 
    cursor: pointer; 
}
button:hover { background: darkolivegreen; }
.btn-row { display: flex; gap: 15px; }

/* Separator */
.separator { border-top: 1px solid #ddd; margin: 25px 0; }

/* Long desc, Reviews, Related should use full width */
.long-desc, .reviews, .related { 
    margin-top: 20px; 
}
.description { 
    font-size: 16px; 
    color: #444; 
    margin-bottom: 15px; 
    max-height: 70px; 
    overflow: hidden; 
    transition: max-height 0.3s ease; 
    line-height: 1.6; 
}
.description.expanded { max-height: 500px; }
.toggle-btn { color: olive; cursor: pointer; font-weight: bold; }

/* Related products grid */
.related-products { 
    display: grid; 
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); 
    gap: 25px; 
}
.card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    padding: 15px;
    text-align: center;
    transition: transform 0.2s;
    min-height: 500px;
    position: relative;   /* ⬅️ Important */
    overflow: hidden;     /* ensures icons don’t spill */
}

/* Heart (wishlist) → top-right */
.card .wishlist-btn {
    position: absolute;
    top: 18px;
    right: 16px;
    font-size: 20px;
    color: gray;
    background:white;
    border radius:25px;
    cursor: pointer;
    z-index: 2;
}
.card .wishlist-btn.active { color: red; }

.card .quickview {
    position: absolute;
    top: 30%;   
    left: 50%;  
    transform: translate(-50%, -50%);
    font-size: 28px;
    color: white;   /* ⬅️ white eye */
    border-radius: 50%;
    padding: 10px;
    opacity: 0;
    transition: opacity 0.3s;
    text-decoration: none;
    z-index: 2;
}


.card:hover .quickview {
    opacity: 1;
}


.card img { 
    width: 100%; 
    height: 220px; 
    object-fit: cover; 
    border-radius: 8px; 
}
.card p { margin: 10px 0 5px; font-weight: bold; }
.card span { color: green; font-size: 16px; }

</style>
</head>
<body>
<div class="container">
    <div class="product-detail">
    <!-- Product Image -->
    <div class="image-box">
        <?php $imgPath = "images/" . basename($product['image']); ?>
        <img src="<?php echo $imgPath; ?>" alt="<?php echo $product['name']; ?>">
        <i class="fa fa-heart wishlist-btn-main <?php echo $in_wishlist ? 'active' : ''; ?>" data-id="<?php echo $product['product_id']; ?>"></i>
    </div>

    <!-- Product Info (right side only) -->
    <div class="info">
        <h2><?php echo $product['name']; ?></h2>
        <p class="short-desc"><?php echo $product['description']; ?></p>
        <div class="price">₹<?php echo $product['price']; ?></div>

        <div class="qty">
            <label>Qty:</label>
            <button onclick="changeQty(-1)">−</button>
            <input type="number" id="qty" value="1" min="1" readonly>
            <button onclick="changeQty(1)">+</button>
        </div>
        <div class="subtotal">Subtotal: ₹<span id="subtotal"><?php echo $product['price']; ?></span></div>

        <!-- Buttons -->
        <div class="btn-row">
            <form method="POST" action="cart.php" style="display:inline;">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                <input type="hidden" name="quantity" id="hiddenQty" value="1">
                <button type="submit">Add to Cart</button>
            </form>
          <form method="POST" action="checkout.php" style="display:inline;">
    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
    <input type="hidden" name="quantity" id="buyQty" value="1">
    <button type="submit">Buy Now</button>
</form>

        </div>
    </div>
</div> 


<div class="separator"></div>

<!-- ✅ Long Description -->
<div class="long-desc">
    <h3>Product Details</h3>
<p class="description" id="desc"><?php echo nl2br($product['long_description']); ?></p>
    <span class="toggle-btn" onclick="toggleDesc()">Read More</span>
</div>

<div class="separator"></div>

<!-- ✅ Reviews -->
<div class="reviews">
    <h3>Customer Reviews</h3>
    <div class="review-box"><strong>John Doe</strong>: Amazing product! ⭐⭐⭐⭐☆</div>
    <div class="review-box"><strong>Jane Smith</strong>: Worth the price. ⭐⭐⭐⭐⭐</div>
    <?php if (!empty($reviews)) { while ($rev = $reviews->fetch_assoc()): ?>
        <div class="review-box"><strong><?php echo $rev['username']; ?></strong>: <?php echo $rev['comment']; ?> ⭐<?php echo $rev['rating']; ?></div>
    <?php endwhile; } ?>
</div>

<div class="separator"></div>

<!-- ✅ Related Products -->
<div class="related">
    <h3>Related Products</h3>
    <div class="related-products">
        <?php foreach ($related_products as $rel): ?>
           <div class="card">
    <?php $relImgPath = "images/" . basename($rel['image']); ?>
    <img src="<?php echo $relImgPath; ?>" alt="<?php echo $rel['name']; ?>">  

    <!-- Heart top-right -->
    <i class="fa fa-heart wishlist-btn <?php echo isset($related_wishlist[$rel['product_id']]) ? 'active' : ''; ?>" 
       data-id="<?php echo $rel['product_id']; ?>"></i>

    <!-- Eye center (hover) -->
    <a href="product_detail.php?id=<?php echo $rel['product_id']; ?>" class="quickview">
        <i class="fa fa-eye"></i>
    </a>

    <p><?php echo $rel['name']; ?></p>
    <span>₹<?php echo $rel['price']; ?></span>
</div>
 
        <?php endforeach; ?>
    </div>
</div>
</div>

<script>
function updateSubtotal() {
        let qty = document.getElementById("qty").value;
        let price = <?php echo $product['price']; ?>;
        document.getElementById("subtotal").innerText = qty * price;

        // Update both hidden inputs (cart & Buy Now)
        document.getElementById("hiddenQty").value = qty;
        document.getElementById("buyQty").value = qty;
    }


function changeQty(val) {
    let qtyInput = document.getElementById("qty");
    let qty = parseInt(qtyInput.value) + val;
    if (qty < 1) qty = 1;
    qtyInput.value = qty;
    updateSubtotal();
}

function toggleDesc() {
    let desc = document.getElementById("desc");
    let btn = document.querySelector(".toggle-btn");
    desc.classList.toggle("expanded");
    btn.innerText = desc.classList.contains("expanded") ? "Read Less" : "Read More";
}

// ✅ Wishlist AJAX
document.querySelectorAll('.wishlist-btn-main, .wishlist-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        let productId = this.getAttribute("data-id");
        let heart = this;

        fetch("wishlist_ajax.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "product_id=" + productId
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === "added") {
                heart.classList.add("active");
            } else if (data.status === "removed") {
                heart.classList.remove("active");
            } else {
                alert("⚠️ " + data.message);
            }
        })
        .catch(err => console.error("Wishlist error:", err));
    });
});
</script>
</body>
</html>
