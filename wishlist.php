<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$conn = new mysqli("localhost", "root", "", "bamboo");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT w.wishlist_id, p.product_id, p.name, p.description, p.price, p.image 
        FROM wishlist w
        JOIN products p ON w.product_id = p.product_id
        WHERE w.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Wishlist</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f9f9f9;
      margin: 0;
      padding: 20px;
    }

    .page-title {
      text-align: center;
      margin-bottom: 30px;
      font-size: 28px;
      color: #333;
    }

    .product-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
    }

    .product-card {
      position: relative;
      width: 250px;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: transform 0.2s ease;
    }

    .product-card:hover {
      transform: translateY(-5px);
    }

    .image-container {
      position: relative;
      width: 100%;
      height: 200px;
      overflow: hidden;
    }

    .image-container img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }

    .quick-view {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(0,0,0,0.6);
      color: #fff;
      border: none;
      padding: 10px 15px;
      border-radius: 50%;
      cursor: pointer;
      display: none;
      transition: 0.3s;
    }

    .image-container:hover .quick-view {
      display: block;
    }

    .wishlist-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      background: transparent;
      border: none;
      font-size: 20px;
      color: red;
      cursor: pointer;
      z-index: 2;
    }

    .product-info {
      padding: 15px;
      text-align: left;
    }

    .product-info h3 {
      font-size: 18px;
      margin: 0 0 10px;
      color: #333;
    }

    .product-info .description {
      font-size: 14px;
      color: #777;
      margin-bottom: 10px;
    }

    .product-info .price {
      font-size: 16px;
      font-weight: bold;
      color: #28a745;
    }

    .empty-msg {
      text-align: center;
      font-size: 18px;
      color: #666;
    }
  </style>
</head>
<body>

<h2 class="page-title">My Wishlist ❤️</h2>

<div class="product-container">
  <?php if ($result->num_rows > 0): ?>
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="product-card">

        <div class="image-container">
          <img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">


          <button class="quick-view" onclick="window.location.href='product_detail.php?id=<?php echo $row['product_id']; ?>'">
            <i class="fa fa-eye"></i>
          </button>
        </div>

        <button class="wishlist-btn" data-id="<?php echo $row['product_id']; ?>">
          <i class="fa-solid fa-heart"></i>
        </button>

        <div class="product-info">
          <h3><?php echo $row['name']; ?></h3>
          <p class="description"><?php echo $row['description']; ?></p>
          <p class="price">₹<?php echo $row['price']; ?></p>
        </div>

      </div>
    <?php endwhile; ?>
  <?php else: ?>
    <p class="empty-msg">Your wishlist is empty 💔</p>
  <?php endif; ?>
</div>

<script>
$(document).on("click", ".wishlist-btn", function() {
    var button = $(this);
    var productId = button.data("id");

    $.ajax({
        url: "wishlist_ajax.php",
        type: "POST",
        data: { product_id: productId },
        success: function(response) {
            try {
                var res = JSON.parse(response);
                if (res.status === "removed") {
                    button.closest(".product-card").fadeOut();
                }
            } catch (e) {
                console.error("Invalid JSON:", response);
            }
        }
    });
});
</script>

</body>
</html>
