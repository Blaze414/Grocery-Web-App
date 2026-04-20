<?php
session_start();

$products = [
  "Apple"    => ["price" => 1.00, "image" => "Images/apple.jpg",    "category" => "fruits"],
  "Banana"   => ["price" => 0.50, "image" => "Images/banana.jpg",   "category" => "fruits"],
  "Orange"   => ["price" => 0.75, "image" => "Images/orange.jpg",   "category" => "fruits"],
  "Carrot"   => ["price" => 1.20, "image" => "Images/carrot.jpg",   "category" => "vegetables"],
  "Broccoli" => ["price" => 1.80, "image" => "Images/broccoli.jpg", "category" => "vegetables"],
  "Milk"     => ["price" => 1.50, "image" => "Images/milk.jpg",     "category" => "dairy"],
  "Cheese"   => ["price" => 2.50, "image" => "Images/cheese.jpg",   "category" => "dairy"],
  "Chips"    => ["price" => 2.00, "image" => "Images/chips.jpg",    "category" => "snacks"],
  "Cookies"  => ["price" => 2.50, "image" => "Images/cookies.jpg",  "category" => "snacks"],
];

$productName = $_GET['name'] ?? null;
if (!$productName || !isset($products[$productName])) {
  header("Location: shop.php"); exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $quantity = max(1, intval($_POST['quantity']));
  if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
  if (isset($_SESSION['cart'][$productName])) {
    $_SESSION['cart'][$productName]['quantity'] += $quantity;
  } else {
    $_SESSION['cart'][$productName] = ['price' => $products[$productName]['price'], 'quantity' => $quantity];
  }
  header("Location: cart.php"); exit();
}

$totalQuantity = 0; $totalPrice = 0;
if (isset($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $d) {
    $totalQuantity += $d['quantity'];
    $totalPrice    += $d['price'] * $d['quantity'];
  }
}

$prod = $products[$productName];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($productName) ?> — Grocery Store</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="stylesheet" href="styles.css"/>
</head>
<body>

<!-- ── NAV ── -->
<nav class="site-nav">
  <div class="inner">
    <a href="index.php" class="nav-brand">Grocery Store</a>
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="shop.php" class="active">Shop</a></li>
      <li><a href="category.php?category=vegetables">Categories</a></li>
    </ul>
    <div class="nav-spacer"></div>
    <form class="nav-search" method="get" action="shop.php">
      <input type="search" name="search" placeholder="Search products…"/>
      <button type="submit"><i class="fa fa-search"></i></button>
    </form>
    <a href="cart.php" class="nav-cart" style="margin-left:16px">
      <i class="fa fa-bag-shopping"></i> Cart
      <?php if ($totalQuantity > 0): ?>
        <span class="cart-count"><?= $totalQuantity ?></span>
      <?php endif; ?>
    </a>
    <button class="nav-toggle" aria-label="Menu"><span></span><span></span><span></span></button>
  </div>
</nav>

<!-- ── PRODUCT ── -->
<div class="product-detail">
  <div class="product-detail-img">
    <img src="<?= $prod['image'] ?>" alt="<?= htmlspecialchars($productName) ?>"/>
  </div>
  <div class="product-detail-content">
    <div class="product-breadcrumb">
      <a href="shop.php">Shop</a> &nbsp;/&nbsp;
      <a href="category.php?category=<?= $prod['category'] ?>"><?= ucfirst($prod['category']) ?></a> &nbsp;/&nbsp;
      <?= htmlspecialchars($productName) ?>
    </div>

    <div class="t-label" style="margin-bottom:12px"><?= ucfirst($prod['category']) ?></div>

    <h1 class="product-title"><?= htmlspecialchars($productName) ?></h1>
    <div class="product-price-tag">$<?= number_format($prod['price'], 2) ?></div>

    <div class="product-divider"></div>

    <form method="post">
      <div style="margin-bottom:16px">
        <div class="t-label" style="margin-bottom:10px">Quantity</div>
        <div class="qty-control">
          <button type="button" class="qty-btn" onclick="adjustQty(-1)">−</button>
          <input type="number" name="quantity" id="qty" class="qty-input" value="1" min="1"/>
          <button type="button" class="qty-btn" onclick="adjustQty(1)">+</button>
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-block" style="margin-bottom:12px">
        <i class="fa fa-bag-shopping"></i>&nbsp; Add to Cart
      </button>
    </form>
    <a href="shop.php" class="btn btn-secondary btn-block">
      <i class="fa fa-arrow-left" style="font-size:11px"></i>&nbsp; Back to Shop
    </a>
  </div>
</div>

<!-- ── FOOTER ── -->
<footer class="site-footer">
  <div class="inner">
    <span class="footer-brand">Grocery Store</span>
    <span class="footer-copy">&copy; 2024 Al Zadid Yusuf. All rights reserved.</span>
  </div>
</footer>

<script>
function adjustQty(delta) {
  var el = document.getElementById('qty');
  var v = parseInt(el.value) + delta;
  el.value = Math.max(1, v);
}
</script>
</body>
</html>