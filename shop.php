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

$searchQuery     = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$categoryFilter  = isset($_GET['cat'])    ? $_GET['cat'] : '';

$filteredProducts = [];
foreach ($products as $name => $d) {
  $matchSearch = !$searchQuery || strpos(strtolower($name), $searchQuery) !== false;
  $matchCat    = !$categoryFilter || $d['category'] === $categoryFilter;
  if ($matchSearch && $matchCat) $filteredProducts[$name] = $d;
}

$totalQuantity = 0; $totalPrice = 0;
if (isset($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $d) {
    $totalQuantity += $d['quantity'];
    $totalPrice    += $d['price'] * $d['quantity'];
  }
}

$categories = ['fruits','vegetables','dairy','snacks'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Shop — Grocery Store</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="stylesheet" href="styles.css"/>
  <style>
    .filter-bar {
      display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 32px;
    }
    .filter-pill {
      font-family: var(--font-display);
      font-weight: 600; font-size: 12px; letter-spacing: .1em;
      text-transform: uppercase; padding: 8px 18px;
      border: 1.5px solid var(--border); border-radius: 100px;
      background: transparent; color: var(--text-2);
      cursor: pointer; transition: background .15s, color .15s, border-color .15s;
      text-decoration: none;
    }
    .filter-pill:hover,
    .filter-pill.active {
      background: var(--black); color: var(--white);
      border-color: var(--black);
    }
    .results-meta {
      font-family: var(--font-display);
      font-size: 12px; letter-spacing: .1em; text-transform: uppercase;
      color: var(--text-3); margin-bottom: 16px;
    }
  </style>
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
      <input type="search" name="search" placeholder="Search products…"
             value="<?= htmlspecialchars($searchQuery) ?>"/>
      <?php if ($categoryFilter): ?>
        <input type="hidden" name="cat" value="<?= htmlspecialchars($categoryFilter) ?>"/>
      <?php endif; ?>
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
    <h1>All Products</h1>
    <div class="sub"><?= count($filteredProducts) ?> items available</div>
  </div>
</div>

<!-- ── SHOP ── -->
<section class="section">
  <div class="container">

    <!-- Filters -->
    <div class="filter-bar">
      <a href="shop.php<?= $searchQuery ? '?search='.urlencode($searchQuery) : '' ?>"
         class="filter-pill <?= !$categoryFilter ? 'active' : '' ?>">All</a>
      <?php foreach ($categories as $cat): ?>
        <a href="shop.php?<?= $searchQuery ? 'search='.urlencode($searchQuery).'&' : '' ?>cat=<?= $cat ?>"
           class="filter-pill <?= $categoryFilter === $cat ? 'active' : '' ?>">
          <?= ucfirst($cat) ?>
        </a>
      <?php endforeach; ?>
    </div>

    <?php if ($searchQuery): ?>
      <div class="results-meta">Results for "<?= htmlspecialchars($searchQuery) ?>"</div>
    <?php endif; ?>

    <?php if (empty($filteredProducts)): ?>
      <div class="empty-state">
        <div class="empty-state-icon"><i class="fa fa-magnifying-glass"></i></div>
        <h2>No products found</h2>
        <p>Try a different search or browse all categories.</p>
        <a href="shop.php" class="btn btn-primary">Clear Filters</a>
      </div>
    <?php else: ?>
      <div class="products-grid">
        <?php foreach ($filteredProducts as $name => $d): ?>
          <div class="product-card">
            <div class="product-card-img">
              <img src="<?= $d['image'] ?>" alt="<?= htmlspecialchars($name) ?>"/>
            </div>
            <div class="product-card-body">
              <div class="product-card-cat"><?= ucfirst($d['category']) ?></div>
              <div class="product-card-name"><?= htmlspecialchars($name) ?></div>
              <div class="product-card-footer">
                <span class="product-card-price">$<?= number_format($d['price'], 2) ?></span>
                <a href="product.php?name=<?= urlencode($name) ?>" class="btn btn-primary btn-sm">
                  Add <i class="fa fa-plus" style="font-size:10px"></i>
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