<?php 
session_start();

// Show logout success message once
if (isset($_SESSION['logout_msg'])) {
    echo '<div style="background:#d4edda; color:#155724; padding:10px; margin:15px; border:1px solid #c3e6cb; border-radius:5px;">';
    echo $_SESSION['logout_msg'];
    echo '</div>';
    unset($_SESSION['logout_msg']);
}

// Check if user logged in
$isLoggedIn = isset($_SESSION['username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bamboo Haven | Sustainable Bamboo Products</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
  <style>
    :root {
      --primary-color: #4b5320;
      --text-color: #333;
      --gray-light: #f8f8f8;
      --shadow: 0 2px 5px rgba(0,0,0,0.1);
      --transition: all 0.3s ease;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
    body { background-color: var(--gray-light); color: var(--text-color); line-height: 1.6; }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 20px; }

    header { background-color: rgb(179, 179, 107); box-shadow: var(--shadow); position: sticky; top: 0; z-index: 1000; }
    .header-container { display: flex; justify-content: space-between; align-items: center; padding: 15px 0; }
    .logo { font-size: 24px; font-weight: 700; color: var(--primary-color); text-decoration: none; display: flex; align-items: center; }
    .logo i { margin-right: 10px; }
    .nav-links { display: flex; list-style: none; }
    .nav-links li { margin-left: 30px; position: relative; }
    .nav-links a { text-decoration: none; color: var(--text-color); font-weight: 500; transition: var(--transition); }
    .nav-links a:hover { color: var(--primary-color); }

.dropdown {
  position: relative;
  display: inline-block;
}


.dropdown-btn {
  background: none;
  border: none;
  color: inherit;
  font: inherit;
  cursor: pointer;
  padding: 0;
  margin: 0;
  display: inline-block;
  line-height: 1.5; 
}

.nav-links li a,
.dropdown-btn {
  text-decoration: none;
  color: #222;
  font-weight: 500;
  padding: 8px 12px;
  display: inline-block;
  vertical-align: middle;
}

.dropdown-btn:hover {
  color: darkgreen;
}



.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f9f9f9;
  min-width: 160px;
  box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
  z-index: 1;
  border-radius: 6px;
}


.dropdown-content a {
  color: black;
  padding: 10px 14px;
  text-decoration: none;
  display: block;
}


.dropdown-content a:hover {
  background-color: #ddd;
}


.dropdown:hover .dropdown-content {
  display: block;
}

    
    .cart-icon, .wishlist-icon { position: relative; }
    .cart-count, .wishlist-count {
      position: absolute; top: -8px; right: -8px;
      background-color: var(--primary-color); color: white;
      border-radius: 50%; width: 18px; height: 18px;
      font-size: 12px; display: flex; justify-content: center; align-items: center;
    }
    .mobile-menu-btn { display: none; background: none; border: none; font-size: 24px; cursor: pointer; color: var(--primary-color); }

    
    .hero {
      height: 650px;
      background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://rare-gallery.com/uploads/posts/516143-bamboo.jpg');
      background-size: cover; background-position: center;
      display: flex; align-items: center;
      color: rgb(180, 203, 118); text-align: center;
      margin-bottom: 40px;
    }
    .hero-content { max-width: 900px; margin: 0 auto; padding: 0 20px; }
    .hero h1 { font-size: 48px; margin-bottom: 20px; color: white; }
    .hero p { font-size: 20px; margin-bottom: 30px; color: #fff7f0; }
    .btn { display: inline-block; padding: 12px 30px; background-color: rgb(9, 100, 64); color: white; text-decoration: none; border-radius: 5px; font-weight: 500; transition: var(--transition); }
    .btn:hover { background-color: rgb(4, 103, 4); transform: translateY(-3px); }

    
    .why-bamboo { padding: 60px 20px; text-align: center;  background-color: #b7c57f;}
    .why-bamboo h2 { font-size: 36px; margin-bottom: 20px; color: var(--primary-color); }
    .why-bamboo p { font-size: 18px; color: #555; max-width: 700px; margin: 0 auto 40px auto; }

    
    .slider-container { position: relative; width: 100%; max-width: 1200px; margin: 0 auto; overflow: hidden; }
    .slide { position: relative; width: 100%; height: 600px; }
    .item {
      width: 300px; height: 500px;
      position: absolute; top: 50%;
      transform: translate(0, -50%);
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      background-position: center; background-size: cover;
      transition: all 0.5s ease-in-out;
    }
    
.slide .item:nth-child(2) {
  left: 50%;
  transform: translate(-50%, -50%);
  width: 70vw;
  height: 80vh;
  border-radius: 15px;
}


.slide .item:nth-child(1) {
  left: calc(50% - 350px);
  width: 300px;
  height: 500px;
  opacity: 0.8;
}

    .slide .item:nth-child(3) { left: calc(50% + 350px); width: 300px; height: 500px; }
    .slide .item:nth-child(4) { left: calc(50% + 650px); opacity: 0.6; width: 300px; height: 500px; }
    .slide .item:nth-child(n + 5) { left: calc(50% + 1000px); opacity: 0; }
      .content {
  position: absolute;
  bottom: 20%;         
  left: 5%;            
  transform: none;     
  text-align: left;    
  color: white;
}
    .slide .item:nth-child(2) .content { display: block; }
     .content .name {
  font-size: 68px;        
  font-weight: bold;
  color: white;           
  margin-bottom: 10px;    
  line-height: 1.5;       
  max-width: 80%;       
  word-wrap: break-word;  
  white-space: normal;    
  text-align: left;       
}


.content button {
  margin-top: 10px;
}

    .content .des { margin-top: 10px; font-size: 20px; font-weight: bold; width: 50%; }
    .content button { padding: 10px 20px; border: none; cursor: pointer; background: #ff6600; color: #fff; margin-top: 15px; border-radius: 5px; font-size: 16px; }

    .button { position: absolute; bottom: 20px; width: 100%; text-align: center; }
    .button button {
      width: 50px; height: 50px; border-radius: 50%; border: none;
      cursor: pointer; font-size: 24px; background: #636843; color: white;
      margin: 10px; transition: 0.3s;
    }
    .button button:hover { background: #2e3b0f; }

    
    .footer-bottom { text-align: center; padding: 10px 0; border-top: 1px solid rgba(248, 242, 242, 0.1); font-size: 20px; background-color: rgb(59, 84, 10); color: white; }

    @media (max-width: 768px) {
      .header-container { flex-wrap: wrap; }
      .mobile-menu-btn { display: block; order: 1; }
      .logo { order: 2; flex-grow: 1; justify-content: center; }
      .nav-links { display: none; order: 3; width: 100%; margin-top: 20px; flex-direction: column; }
      .nav-links.active { display: flex; }
      .nav-links li { margin: 10px 0; margin-left: 0; }
      .hero h1 { font-size: 36px; }
      .hero p { font-size: 18px; }
    }
.slide .item {
  z-index: 1;
}

.slide .item:not(.active) .content {
  pointer-events: none;
}

.slide .item.active .content {
  pointer-events: auto;
  z-index: 10;
}
.button button {
  position: relative;
  z-index: 1000;
}



  </style>
</head>
<body>
  <!-- Header -->
  <header>
    <div class="container header-container">
      <button class="mobile-menu-btn"><i class="fas fa-bars"></i></button>
      <a href="#" class="logo"><i class="fas fa-leaf"></i> Bamboo Haven</a>
      <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="product.html">Shop</a></li>
       

        <li class="dropdown">
          <button class="dropdown-btn"><i class="fas fa-user"></i> Account</button>
          <div class="dropdown-content">
            <?php if ($isLoggedIn): ?>
              <a href="my_account.php">My Account</a>
              <a href="my_order.php">My Orders</a>
              <a href="wishlist.php">Wishlist</a>
              <a href="cart.php">Cart</a>
              <a href="logout.php">Logout</a>
            <?php else: ?>
              <a href="login.php">Login</a>
              <a href="register.php">Register</a>
            <?php endif; ?>
          </div>
           <li><a href="about.html">About</a></li>
        </li>
      </ul>
    </div>
  </header>
<section class="hero"> 
  <div class="hero-content"> 
  <h1>Welcome to Bamboo Haven</h1> 
  <p>Sustainable bamboo products for a greener tomorrow</p> 
  <a href="gallery.html" class="btn">Explore gallery</a> </div> 
</section> 
 <section class="why-bamboo"> 
  <h2>Why Choose Bamboo?</h2> 
  <p>"Discover the benefits, uses, and sustainability of bamboo products”</p>
   <div class="slider-container"> 
    <div class="slide">
       <div class="item" style="background-image: url('https://i.pinimg.com/1200x/01/c6/8b/01c68b4ce895d1b9abc408ea27a6351a.jpg');"> 
        <div class="content"> 
        <div class="name">Manufacturing of Bamboo</div>
        <a href="manufacturing.html"><button>See More</button></a> </div>
       </div>
        <div class="item" style="background-image: url('https://i.pinimg.com/736x/39/40/1c/39401cf95b180f6b91513b3f916142d9.jpg');"> 
        <div class="content"> 
          <div class="name">What is Bamboo?</div>
          <a href="whatisbamboo.html"><button>See More</button></a> 
        </div> 
      </div> 
      <div class="item" style="background-image: url('https://www.youchengbamboo.com/uploads/202412/youcheng_1735192384_WNo_1600d900.jpg');">
         <div class="content"> 
          <div class="name">Benefits of Bamboo</div>
          <a href="benefits.html"><button>See More</button></a> 
        </div> 
      </div>
       <div class="item" style="background-image: url('https://i.pinimg.com/1200x/eb/70/29/eb7029812034e20136606c9e519c4ffb.jpg');"> 
        <div class="content">
          <div class="name">Uses of Bamboo</div>
           <a href="uses.html"><button>See More</button></a>
           </div>
           </div> 
          </div>
           <div class="button"> <button class="prev">←</button> 
           <button class="next">→</button> 
          </div> 
        </div> 
      </section> 
       <footer>
         <div class="footer-bottom"> <p>&copy; 2023 Bamboo Haven. All Rights Reserved.</p> </div>
       </footer> 
       <script>
document.addEventListener("DOMContentLoaded", function () {
  const menuBtn = document.querySelector('.mobile-menu-btn');
  const navLinks = document.querySelector('.nav-links');

  menuBtn.addEventListener('click', () => {
    navLinks.classList.toggle('active');
  });

  let next = document.querySelector('.next');
  let prev = document.querySelector('.prev');
  let slide = document.querySelector('.slide');

  function setActiveSlide() {
    let items = document.querySelectorAll('.item');
    items.forEach(item => item.classList.remove('active'));
    if (items[1]) items[1].classList.add('active'); 
  }

  setActiveSlide();

  next.addEventListener('click', function(){
    let items = document.querySelectorAll('.item');
    slide.appendChild(items[0]);
    setActiveSlide();
  });

  prev.addEventListener('click', function(){
    let items = document.querySelectorAll('.item');
    slide.prepend(items[items.length - 1]);
    setActiveSlide();
  });
     });
</script>
  </body>
          </html> 