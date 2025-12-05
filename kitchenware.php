<?php
session_start();
include("db_connect.php");


$isLoggedIn = isset($_SESSION['user_id']);
$user_id = $isLoggedIn ? $_SESSION['user_id'] : 0;


$query = "SELECT * FROM products WHERE category='Kitchenware'";
$result = mysqli_query($conn, $query);


$wishlistProducts = [];
if ($isLoggedIn) {
    $res = mysqli_query($conn, "SELECT product_id FROM wishlist WHERE user_id=$user_id");
    while($w = mysqli_fetch_assoc($res)) {
        $wishlistProducts[] = $w['product_id'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Kitchenware Products</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<style>
:root{ --brand:#3e5216; --page-bg:rgb(229,229,142); --card-radius:14px; --gap:22px; }
body{ margin:0; font-family: Arial, sans-serif; background:var(--page-bg); }
header{ position:fixed; top:0; left:0; right:0; z-index:20; display:flex; align-items:center; justify-content:space-between; background:var(--brand); color:#fff; padding:14px 18px; }
header h1{ margin:0; font-size:20px; font-weight:700; }
header input{ width:280px; max-width:45vw; padding:8px 10px; border-radius:8px; border:none; }
.hamburger{ display:none; font-size:24px; background:none; border:0; color:#fff; }
.nav-links{ list-style:none; display:flex; gap:22px; margin:0; padding:0; }
.nav-links a{ color:#fff; text-decoration:none; padding:6px 10px; border-radius:4px; transition:background .2s,color .2s; }
.nav-links a:hover{ background:rgba(255,255,255,.2); color:#ffd700; }
.filters{ position:fixed; top:64px; left:-210px; z-index:10; width:200px; height:100vh; padding:18px; background:#d0cfaf; transition:left .35s ease-in-out; box-shadow: 2px 0 8px rgba(0,0,0,.08); }
.filters:hover{ left:0; } 
.filters h3{ margin:0 0 12px; font-size:18px; color:#333; }
.filter-list{ list-style:none; margin:0; padding:0; }
.filter-list li{ display:flex; align-items:center; gap:10px; margin:50px 0; font-size:16px; }
.filter-list input[type="radio"]{ accent-color:var(--brand); }
.main-content{ padding:96px 24px 28px; }
.main-content h2{ text-align:center; font-size:36px; font-weight:700; margin:0 0 8px; }
#related-container{ display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:8px; padding:20px; justify-content:center; align-items:start; }
.product-card{ width:340px; background:#fff; border-radius:var(--card-radius); box-shadow:0 4px 10px rgba(0,0,0,.18); overflow:hidden; position:relative; }
.image-container{ position:relative; height:400px; overflow:hidden; }
.image-container img{ width:100%; height:100%; object-fit:cover; border-radius:6px; transition: transform .4s ease; }
.image-container::after{ content:""; position:absolute; inset:0; background:rgba(0,0,0,.5); opacity:0; transition:opacity .3s; }
.image-container:hover::after{ opacity:1; } 
.image-container:hover img{ transform:scale(1.05); }
.quickview-btn{ position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); font-size:26px; color:white; background:none; border:none; padding:0; opacity:0; transition:opacity .3s, transform .3s; cursor:pointer; z-index:2; }
.image-container:hover .quickview-btn{ opacity:1; transform:translate(-50%, -50%) scale(1.1); }
.wishlist-btn{ position:absolute; top:12px; right:12px; z-index:3; background:white; border:none; border-radius:6px; padding:4px 6px; box-shadow:0 2px 6px rgba(0,0,0,0.15); cursor:pointer; transition:transform .2s; }
.wishlist-btn i{ font-size:18px; color:gray; transition: color .2s; }
.wishlist-btn.active i{ color:red; }
.wishlist-btn:hover{ transform:scale(1.1); }
.card-body{ padding:14px 16px 16px; }
.card-body h3{ font-size:22px; margin:6px 0 6px; }
.card-body p{ margin:0; color:#333; font-size:16px; line-height:1.35; }
.price{ margin-top:14px; font-size:18px; font-weight:700; color:#2f6d2f; }
.buy-btn{ display:inline-block; margin-top:10px; padding:10px 16px; background:var(--brand); color:#fff; text-decoration:none; border-radius:8px; font-size:16px; transition:background .2s; }
.buy-btn:hover{ background:#2e3d10; }
@media(max-width:768px){ 
    .nav-links{ display:none; position:absolute; top:56px; left:0; right:0; background:var(--brand); padding:10px 16px; flex-direction:column; gap:10px; } 
    .nav-links.active{ display:flex; } 
    .hamburger{ display:block; }
    header input{ display:none; } 
}
</style>
</head>
<body>
<header>
    <h1>Bamboo Heaven</h1>
    <input type="text" id="searchInput" placeholder="Search bamboo products...">
    <button class="hamburger" onclick="toggleMenu()">&#9776;</button>
    <ul class="nav-links">
      <li><a href="furniture.php">Furniture</a></li>
      <li><a href="kidsproduct.php">Kids Material</a></li>
      <li><a href="accessories.php">Accessories</a></li>
      <li><a href="wishlist.php"><i class="fa-solid fa-heart"></i> Wishlist</a></li>
      <li><a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Cart</a></li>
    </ul>
      

</header>

<aside class="filters">
    <h3>Filter by Price</h3>
    <ul class="filter-list">
      <li><input type="radio" name="price" value="all" checked> <label>All</label></li>
      <li><input type="radio" name="price" value="under300"> <label>Under ₹300</label></li>
      <li><input type="radio" name="price" value="300to1000"> <label>₹300–₹1000</label></li>
      <li><input type="radio" name="price" value="above1000"> <label>Above ₹1000</label></li>
    </ul>
</aside>

<div class="main-content">
    <h2>Kitchenware Products</h2>
    <div id="related-container">
      <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="product-card" data-price="<?= $row['price'] ?>" data-title="<?= strtolower($row['name']) ?>" data-desc="<?= strtolower($row['description']) ?>">
          <div class="image-container">
            <img src="<?= $row['image'] ?>" alt="<?= $row['name'] ?>">
            <a href="product_detail.php?id=<?= $row['product_id'] ?>" class="quickview-btn"><i class="fas fa-eye"></i></a>
            <button type="button" class="wishlist-btn <?= in_array($row['product_id'], $wishlistProducts) ? 'active' : '' ?>" data-product-id="<?= $row['product_id'] ?>">
              <i class="fa fa-heart"></i>
            </button>
          </div>
          <div class="card-body">
            <h3><?= $row['name'] ?></h3>
            <p><?= $row['description'] ?></p>
            <div class="price">₹<?= $row['price'] ?></div>
            <form method="POST" action="cart.php">
              <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
              <input type="hidden" name="quantity" value="1">
              <button type="submit" class="buy-btn">Add to Cart</button>
            </form>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
    <p id="no-results" style="display:none; text-align:center; font-size:18px; color:#555; margin-top:20px;">
      ❌ No results found
    </p>
</div>

<script>
function toggleMenu(){ document.querySelector(".nav-links").classList.toggle("active"); }

function filterProducts(){
  const filter = document.querySelector('input[name="price"]:checked').value;
  const keyword = document.getElementById("searchInput").value.toLowerCase();
  const cards = document.querySelectorAll(".product-card");
  let anyVisible = false;

  cards.forEach(card => {
    const price = parseFloat(card.dataset.price);
    const title = card.dataset.title;
    const desc = card.dataset.desc;

    let matchPrice =
      filter === "all" ||
      (filter === "under300" && price < 300) ||
      (filter === "300to1000" && price >= 300 && price <= 1000) ||
      (filter === "above1000" && price > 1000);

    let matchSearch = title.includes(keyword) || desc.includes(keyword);

    if(matchPrice && matchSearch){
      card.style.display = "block";
      anyVisible = true;
    } else {
      card.style.display = "none";
    }
  });

  document.getElementById("no-results").style.display = anyVisible ? "none" : "block";
}

document.querySelectorAll('input[name="price"]').forEach(radio => radio.addEventListener("change", filterProducts));
document.getElementById("searchInput").addEventListener("input", filterProducts);

// Wishlist functionality
document.querySelectorAll(".wishlist-btn").forEach(btn => {
    btn.addEventListener("click", () => {
        <?php if(!$isLoggedIn): ?>
        alert("Please create your account to add products in wishlist!");
         window.location.href = "register.php"; 
        return;
        <?php else: ?>
        const productId = btn.dataset.productId;

        fetch("wishlist_ajax.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "product_id=" + productId
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === "added") {
                btn.classList.add("active");
            } else if(data.status === "removed") {
                btn.classList.remove("active");
            }
        })
        .catch(err => console.log(err));
        <?php endif; ?>
    });
});
</script>
</body>
</html>
