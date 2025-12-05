<?php
session_start();
include("db_connect.php");

if (!isset($_GET['id'])) {
    echo "Product not found!";
    exit;
}

$product_id = intval($_GET['id']);

// Fetch product
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

// Fetch related products (same category, excluding current one)
$relatedStmt = $conn->prepare("SELECT * FROM products WHERE category = ? AND product_id != ? LIMIT 4");
$relatedStmt->bind_param("si", $product['category'], $product_id);
$relatedStmt->execute();
$related = $relatedStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo $product['name']; ?> - Product Details</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
body { font-family: Arial, sans-serif; margin: 0; background: #f9f9f9; }
.container { max-width: 1000px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
.product-detail { display: flex; gap: 30px; }
.product-detail img { width: 400px; height: auto; border-radius: 10px; }
.info { flex: 1; }
.info h2 { margin: 0 0 10px; }
.info .price { font-size: 24px; color: green; margin: 10px 0; }
.info p { font-size: 16px; color: #444; }
.related { margin-top: 40px; }
.related h3 { margin-bottom: 20px; }
.related-products { display: flex; gap: 20px; flex-wrap: wrap; }
.related-products .card { width: 220px; background: #fff; border-radius: 8px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); padding: 10px; text-align: center; }
.related-products img { width: 100%; height: 150px; object-fit: cover; border-radius: 6px; }
</style>
</head>
<body>
<div class="container">
    <div class="product-detail">
<?php $imgPath = "images/" . basename($product['image']); ?>
<img src="<?php echo $imgPath; ?>" alt="<?php echo $product['name']; ?>">
        <div class="info">
            <h2><?php echo $product['name']; ?></h2>
            <div class="price">₹<?php echo $product['price']; ?></div>
            <p><?php echo $product['description']; ?></p>
            
            <!-- Example Reviews & Ratings -->
            <div class="reviews">
                <p><i class="fa fa-star" style="color:gold;"></i> 4.5 / 5 (23 reviews)</p>
            </div>

            <!-- Add to Cart -->
            <form method="POST" action="cart.php">
                <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                <button type="submit">Add to Cart</button>
            </form>
        </div>
    </div>

    <!-- Related Products -->
    <div class="related">
        <h3>Related Products</h3>
        <div class="related-products">
            <?php while ($rel = $related->fetch_assoc()): ?>
                <div class="card">
<?php $relImgPath = "images/" . basename($rel['image']); ?>
<img src="<?php echo $relImgPath; ?>" alt="<?php echo $rel['name']; ?>">
                    <p><?php echo $rel['name']; ?></p>
                    <span>₹<?php echo $rel['price']; ?></span>
                    <a href="product_detail.php?id=<?php echo $rel['product_id']; ?>">View</a>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>
</body>
</html>
