<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Grocery Store</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="stylesheet" href="styles.css"/>
</head>
<body>

<!-- ── NAV ── -->
<?php
$totalQuantity = 0; $totalPrice = 0;
if (isset($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $d) {
    $totalQuantity += $d['quantity'];
    $totalPrice    += $d['price'] * $d['quantity'];
  }
}
?>
<nav class="site-nav">
  <div class="inner">
    <a href="index.php" class="nav-brand">Grocery Store</a>
    <ul class="nav-links">
      <li><a href="index.php" class="active">Home</a></li>
      <li><a href="shop.php">Shop</a></li>
      <li><a href="category.php?category=vegetables">Categories</a></li>
    </ul>
    <div class="nav-spacer"></div>
    <form class="nav-search" method="get" action="shop.php">
      <input type="search" name="search" placeholder="Search products…"
             value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"/>
      <button type="submit"><i class="fa fa-search"></i></button>
    </form>
    <a href="cart.php" class="nav-cart" style="margin-left:16px">
      <i class="fa fa-bag-shopping"></i>
      Cart
      <?php if ($totalQuantity > 0): ?>
        <span class="cart-count"><?= $totalQuantity ?></span>
      <?php endif; ?>
    </a>
    <button class="nav-toggle" aria-label="Menu">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>

<!-- ── HERO ── -->
<section class="hero">
  <div class="hero-bg"></div>
  <div class="hero-grid"></div>
  <div class="hero-inner">
    <div class="hero-tag">Free delivery on orders over $30</div>
    <h1>Fresh.<br><em>Every</em><br>Day.</h1>
    <p>Premium groceries delivered straight to your door. Quality produce, unbeatable prices, city-fast.</p>
    <a href="shop.php" class="btn btn-accent">Shop Now &nbsp;<i class="fa fa-arrow-right"></i></a>
    <div class="hero-stats">
      <div>
        <div class="hero-stat-num">500+</div>
        <div class="hero-stat-label">Products</div>
      </div>
      <div>
        <div class="hero-stat-num">2hr</div>
        <div class="hero-stat-label">Delivery</div>
      </div>
      <div>
        <div class="hero-stat-num">4.9★</div>
        <div class="hero-stat-label">Rating</div>
      </div>
    </div>
  </div>
</section>

<!-- ── CATEGORIES ── -->
<section class="section">
  <div class="container">
    <div class="section-header">
      <h2 class="section-title">Popular <span>Categories</span></h2>
      <a href="shop.php" class="btn btn-secondary btn-sm">View All</a>
    </div>
    <div class="categories-grid">
      <a href="category.php?category=vegetables">
        <div class="cat-card">
          <img src="Images/vegetables.jpg" alt="Vegetables"/>
          <div class="cat-card-body">
            <div class="cat-card-label">Fresh Pick</div>
            <div class="cat-card-name">Vegetables</div>
            <div class="cat-card-arrow"><i class="fa fa-arrow-up-right"></i></div>
          </div>
        </div>
      </a>
      <a href="category.php?category=fruits">
        <div class="cat-card">
          <img src="Images/fruits.jpg" alt="Fruits"/>
          <div class="cat-card-body">
            <div class="cat-card-label">Seasonal</div>
            <div class="cat-card-name">Fruits</div>
            <div class="cat-card-arrow"><i class="fa fa-arrow-up-right"></i></div>
          </div>
        </div>
      </a>
      <a href="category.php?category=dairy">
        <div class="cat-card">
          <img src="Images/dairy.jpg" alt="Dairy"/>
          <div class="cat-card-body">
            <div class="cat-card-label">Farm Fresh</div>
            <div class="cat-card-name">Dairy</div>
            <div class="cat-card-arrow"><i class="fa fa-arrow-up-right"></i></div>
          </div>
        </div>
      </a>
      <a href="category.php?category=snacks">
        <div class="cat-card">
          <img src="Images/snacks.jpg" alt="Snacks"/>
          <div class="cat-card-body">
            <div class="cat-card-label">Indulge</div>
            <div class="cat-card-name">Snacks</div>
            <div class="cat-card-arrow"><i class="fa fa-arrow-up-right"></i></div>
          </div>
        </div>
      </a>
    </div>
  </div>
</section>

<!-- ── FOOTER ── -->
<footer class="site-footer">
  <div class="inner">
    <span class="footer-brand">Grocery Store</span>
    <span class="footer-copy">&copy; 2024 Al Zadid Yusuf. All rights reserved.</span>
  </div>
</footer>

<script src="transitions.js"></script>
</body>
</html>