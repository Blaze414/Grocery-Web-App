<?php
session_start();

$allProducts = [
  "vegetables" => [
    ["name" => "Carrot",   "price" => 1.20, "image" => "Images/carrot.jpg"],
    ["name" => "Broccoli", "price" => 1.80, "image" => "Images/broccoli.jpg"],
  ],
  "fruits" => [
    ["name" => "Apple",  "price" => 1.00, "image" => "Images/apple.jpg"],
    ["name" => "Banana", "price" => 0.50, "image" => "Images/banana.jpg"],
    ["name" => "Orange", "price" => 0.75, "image" => "Images/orange.jpg"],
  ],
  "dairy" => [
    ["name" => "Milk",   "price" => 1.50, "image" => "Images/milk.jpg"],
    ["name" => "Cheese", "price" => 2.50, "image" => "Images/cheese.jpg"],
  ],
  "snacks" => [
    ["name" => "Chips",   "price" => 2.00, "image" => "Images/chips.jpg"],
    ["name" => "Cookies", "price" => 2.50, "image" => "Images/cookies.jpg"],
  ],
];

$category = $_GET['category'] ?? null;
$products  = $allProducts[$category] ?? [];

$totalQuantity = 0; $totalPrice = 0;
if (isset($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $d) {
    $totalQuantity += $d['quantity'];
    $totalPrice    += $d['price'] * $d['quantity'];
  }
}

$categoryLabels = [
  'vegetables' => 'Farm Fresh', 'fruits' => 'Seasonal',
  'dairy' => 'Farm Direct', 'snacks' => 'Indulge',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= $category ? ucfirst($category) : 'Category' ?> — Grocery Store</title>
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
      <li><a href="shop.php">Shop</a></li>
      <li><a href="category.php?category=vegetables" class="active">Categories</a></li>
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

<!-- ── PAGE HEADER ── -->
<div class="page-header">
  <div class="container">
    <?php if ($category): ?>
      <div class="t-label" style="color:rgba(255,255,255,.4); margin-bottom:10px">
        <?= $categoryLabels[$category] ?? 'Category' ?>
      </div>
      <h1><?= ucfirst($category) ?></h1>
      <div class="sub"><?= count($products) ?> products</div>
    <?php else: ?>
      <h1>Categories</h1>
    <?php endif; ?>
  </div>
</div>

<!-- ── PRODUCTS ── -->
<section class="section">
  <div class="container">

    <!-- Category nav tabs -->
    <div style="display:flex; gap:8px; flex-wrap:wrap; margin-bottom:32px">
      <?php foreach (array_keys($allProducts) as $cat): ?>
        <a href="category.php?category=<?= $cat ?>"
           style="font-family:var(--font-display); font-weight:600; font-size:12px;
                  letter-spacing:.1em; text-transform:uppercase; padding:8px 18px;
                  border:1.5px solid <?= $cat === $category ? 'var(--black)' : 'var(--border)' ?>;
                  border-radius:100px;
                  background:<?= $cat === $category ? 'var(--black)' : 'transparent' ?>;
                  color:<?= $cat === $category ? 'var(--white)' : 'var(--text-2)' ?>;">
          <?= ucfirst($cat) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <?php if (empty($products)): ?>
      <div class="empty-state">
        <div class="empty-state-icon"><i class="fa fa-triangle-exclamation"></i></div>
        <h2>No products found</h2>
        <p>This category doesn't have any products yet.</p>
        <a href="shop.php" class="btn btn-primary">Browse All</a>
      </div>
    <?php else: ?>
      <div class="products-grid">
        <?php foreach ($products as $product): ?>
          <div class="product-card">
            <div class="product-card-img">
              <img src="<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>"/>
            </div>
            <div class="product-card-body">
              <div class="product-card-cat"><?= ucfirst($category) ?></div>
              <div class="product-card-name"><?= htmlspecialchars($product['name']) ?></div>
              <div class="product-card-footer">
                <span class="product-card-price">$<?= number_format($product['price'], 2) ?></span>
                <a href="product.php?name=<?= urlencode($product['name']) ?>" class="btn btn-primary btn-sm">
                  View <i class="fa fa-arrow-right" style="font-size:9px"></i>
                </a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</section>

<!-- ── FOOTER ── -->
<footer class="site-footer">
  <div class="inner">
    <span class="footer-brand">Grocery Store</span>
    <span class="footer-copy">&copy; 2024 Al Zadid Yusuf. All rights reserved.</span>
  </div>
</footer>
</body>
</html>