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

$searchQuery    = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
$categoryFilter = isset($_GET['cat'])    ? $_GET['cat']                       : '';

$filteredProducts = [];
foreach ($products as $name => $d) {
  $matchSearch = !$searchQuery || strpos(strtolower($name), $searchQuery) !== false;
  if ($matchSearch) $filteredProducts[$name] = $d;
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
    .filter-bar { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:32px; }
    .filter-pill {
      font-family:var(--font-display); font-weight:600; font-size:12px;
      letter-spacing:.1em; text-transform:uppercase; padding:8px 18px;
      border:1.5px solid var(--border); border-radius:100px;
      background:transparent; color:var(--text-2); cursor:pointer;
      transition:background .18s,color .18s,border-color .18s;
      text-decoration:none; user-select:none;
    }
    .filter-pill:hover { background:var(--surface-2); color:var(--text-1); }
    .filter-pill.active { background:var(--black); color:var(--white); border-color:var(--black); }
    .results-meta {
      font-family:var(--font-display); font-size:12px; letter-spacing:.1em;
      text-transform:uppercase; color:var(--text-3); margin-bottom:16px; min-height:1.4em;
    }
  </style>
</head>
<body>

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
             value="<?php echo htmlspecialchars($searchQuery); ?>"/>
      <button type="submit"><i class="fa fa-search"></i></button>
    </form>
    <a href="cart.php" class="nav-cart" style="margin-left:16px">
      <i class="fa fa-bag-shopping"></i> Cart
      <?php if ($totalQuantity > 0): ?>
        <span class="cart-count"><?php echo $totalQuantity; ?></span>
      <?php endif; ?>
    </a>
    <button class="nav-toggle" aria-label="Menu"><span></span><span></span><span></span></button>
  </div>
</nav>

<div class="page-header">
  <div class="container">
    <h1>All Products</h1>
    <div class="sub"><?php echo count($filteredProducts); ?> items available</div>
  </div>
</div>

<section class="section">
  <div class="container">

    <div class="filter-bar">
      <a href="shop.php<?php echo $searchQuery ? '?search='.urlencode($searchQuery) : ''; ?>"
         class="filter-pill <?php echo !$categoryFilter ? 'active' : ''; ?>"
         data-filter="all">All</a>
      <?php foreach ($categories as $cat): ?>
        <a href="shop.php?<?php echo $searchQuery ? 'search='.urlencode($searchQuery).'&' : ''; ?>cat=<?php echo $cat; ?>"
           class="filter-pill <?php echo $categoryFilter === $cat ? 'active' : ''; ?>"
           data-filter="<?php echo $cat; ?>">
          <?php echo ucfirst($cat); ?>
        </a>
      <?php endforeach; ?>
    </div>

    <?php if ($searchQuery): ?>
      <div class="results-meta">Results for "<?php echo htmlspecialchars($searchQuery); ?>"</div>
    <?php else: ?>
      <div class="results-meta"></div>
    <?php endif; ?>

    <?php if (empty($filteredProducts)): ?>
      <div class="empty-state">
        <div class="empty-state-icon"><i class="fa fa-magnifying-glass"></i></div>
        <h2>No products found</h2>
        <p>Try a different search or browse all categories.</p>
        <a href="shop.php" class="btn btn-primary">Clear Filters</a>
      </div>
    <?php else: ?>
      <div class="products-grid" id="productGrid">
        <?php foreach ($filteredProducts as $name => $d): ?>
          <div class="product-card"
               data-category="<?php echo htmlspecialchars($d['category']); ?>"
               <?php if ($categoryFilter && $d['category'] !== $categoryFilter): ?>style="display:none"<?php endif; ?>>
            <div class="product-card-img">
              <img src="<?php echo $d['image']; ?>" alt="<?php echo htmlspecialchars($name); ?>"/>
            </div>
            <div class="product-card-body">
              <div class="product-card-cat"><?php echo ucfirst($d['category']); ?></div>
              <div class="product-card-name"><?php echo htmlspecialchars($name); ?></div>
              <div class="product-card-footer">
                <span class="product-card-price">$<?php echo number_format($d['price'], 2); ?></span>
                <a href="product.php?name=<?php echo urlencode($name); ?>" class="btn btn-primary btn-sm">
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

<footer class="site-footer">
  <div class="inner">
    <span class="footer-brand">Grocery Store</span>
    <span class="footer-copy">&copy; 2024 Al Zadid Yusuf. All rights reserved.</span>
  </div>
</footer>
<script src="filter-anim.js"></script>
<script src="transitions.js"></script>
</body>
</html>