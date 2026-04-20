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

$category = $_GET['category'] ?? 'fruits';
if (!isset($allProducts[$category])) $category = 'fruits';

$totalQuantity = 0; $totalPrice = 0;
if (isset($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $d) {
    $totalQuantity += $d['quantity'];
    $totalPrice    += $d['price'] * $d['quantity'];
  }
}

$categoryLabels = [
  'vegetables' => 'Farm Fresh', 'fruits' => 'Seasonal',
  'dairy' => 'Farm Direct',     'snacks' => 'Indulge',
];

// Flatten ALL products for the grid (JS will filter by category)
$allFlat = [];
foreach ($allProducts as $cat => $items) {
  foreach ($items as $item) {
    $allFlat[] = array_merge($item, ['category' => $cat]);
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?php echo ucfirst($category); ?> — Grocery Store</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <link rel="stylesheet" href="styles.css"/>
  <style>
    .filter-tabs { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:32px; }
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
        <span class="cart-count"><?php echo $totalQuantity; ?></span>
      <?php endif; ?>
    </a>
    <button class="nav-toggle" aria-label="Menu"><span></span><span></span><span></span></button>
  </div>
</nav>

<div class="page-header">
  <div class="container">
    <div class="t-label" style="color:rgba(255,255,255,.4); margin-bottom:10px" id="catLabel">
      <?php echo $categoryLabels[$category]; ?>
    </div>
    <h1 id="catTitle"><?php echo ucfirst($category); ?></h1>
    <div class="sub" id="catSub"><?php echo count($allProducts[$category]); ?> products</div>
  </div>
</div>

<section class="section">
  <div class="container">

    <!-- Category tabs — data-filter drives JS -->
    <div class="filter-tabs">
      <?php foreach (array_keys($allProducts) as $cat): ?>
        <a href="category.php?category=<?php echo $cat; ?>"
           class="filter-pill <?php echo $cat === $category ? 'active' : ''; ?>"
           data-filter="<?php echo $cat; ?>">
          <?php echo ucfirst($cat); ?>
        </a>
      <?php endforeach; ?>
    </div>

    <div class="results-meta" id="resultsMeta"></div>

    <!-- ALL products rendered; JS hides/shows per active category -->
    <div class="products-grid" id="productGrid">
      <?php foreach ($allFlat as $product): ?>
        <div class="product-card"
             data-category="<?php echo htmlspecialchars($product['category']); ?>"
             <?php if ($product['category'] !== $category): ?>style="display:none"<?php endif; ?>>
          <div class="product-card-img">
            <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>"/>
          </div>
          <div class="product-card-body">
            <div class="product-card-cat" data-label><?php echo ucfirst($product['category']); ?></div>
            <div class="product-card-name"><?php echo htmlspecialchars($product['name']); ?></div>
            <div class="product-card-footer">
              <span class="product-card-price">$<?php echo number_format($product['price'], 2); ?></span>
              <a href="product.php?name=<?php echo urlencode($product['name']); ?>" class="btn btn-primary btn-sm">
                View <i class="fa fa-arrow-right" style="font-size:9px"></i>
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

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