<?php
session_start();

if (isset($_POST['remove_item'])) {
  unset($_SESSION['cart'][$_POST['remove_item']]);
  header("Location: cart.php"); exit();
}
if (isset($_POST['update_quantity'])) {
  $item = $_POST['item_name'];
  $qty  = intval($_POST['quantity']);
  if ($qty > 0) $_SESSION['cart'][$item]['quantity'] = $qty;
  else          unset($_SESSION['cart'][$item]);
  header("Location: cart.php"); exit();
}

$totalQuantity = 0; $totalPrice = 0;
if (isset($_SESSION['cart'])) {
  foreach ($_SESSION['cart'] as $d) {
    $totalQuantity += $d['quantity'];
    $totalPrice    += $d['price'] * $d['quantity'];
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Cart — Grocery Store</title>
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

<!-- ── PAGE HEADER ── -->
<div class="page-header">
  <div class="container">
    <h1>Your Cart</h1>
    <?php if ($totalQuantity > 0): ?>
      <div class="sub"><?= $totalQuantity ?> item<?= $totalQuantity != 1 ? 's' : '' ?></div>
    <?php endif; ?>
  </div>
</div>

<!-- ── CART BODY ── -->
<section class="section">
  <div class="container">

    <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>

      <div class="cart-layout">

        <!-- Items -->
        <div>
          <table class="cart-table">
            <thead>
              <tr>
                <th>Product</th>
                <th style="text-align:center">Quantity</th>
                <th>Total</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($_SESSION['cart'] as $item => $details):
                $itemTotal = $details['price'] * $details['quantity'];
              ?>
              <tr>
                <td>
                  <div class="cart-item-name"><?= htmlspecialchars($item) ?></div>
                  <div style="font-size:13px; color:var(--text-3); margin-top:3px">
                    $<?= number_format($details['price'], 2) ?> each
                  </div>
                </td>
                <td style="text-align:center">
                  <form method="post" class="cart-qty-wrap" style="justify-content:center">
                    <input type="hidden" name="item_name" value="<?= htmlspecialchars($item) ?>"/>
                    <input type="number" name="quantity" value="<?= $details['quantity'] ?>" min="0"/>
                    <button type="submit" name="update_quantity" class="btn btn-secondary btn-sm">Update</button>
                  </form>
                </td>
                <td>
                  <span class="t-price" style="font-size:18px">$<?= number_format($itemTotal, 2) ?></span>
                </td>
                <td>
                  <form method="post">
                    <input type="hidden" name="remove_item" value="<?= htmlspecialchars($item) ?>"/>
                    <button type="submit" class="btn btn-danger">
                      <i class="fa fa-trash" style="font-size:10px"></i> Remove
                    </button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <div style="margin-top:24px">
            <a href="shop.php" class="btn btn-secondary">
              <i class="fa fa-arrow-left" style="font-size:10px"></i>&nbsp; Continue Shopping
            </a>
          </div>
        </div>

        <!-- Summary -->
        <div class="cart-summary">
          <div class="cart-summary-title">Order Summary</div>

          <?php
            $subtotal = $totalPrice;
            $delivery = $subtotal >= 30 ? 0 : 4.99;
            $grandTotal = $subtotal + $delivery;
          ?>

          <div class="summary-row">
            <span>Subtotal</span>
            <span>$<?= number_format($subtotal, 2) ?></span>
          </div>
          <div class="summary-row">
            <span>Delivery</span>
            <span><?= $delivery == 0 ? '<span style="color:var(--success);font-weight:600">Free</span>' : '$'.number_format($delivery,2) ?></span>
          </div>
          <?php if ($delivery > 0): ?>
          <div class="summary-row" style="font-size:12px; color:var(--text-3)">
            <span>Add $<?= number_format(30 - $subtotal, 2) ?> for free delivery</span>
          </div>
          <?php endif; ?>

          <div class="summary-total">
            <span class="summary-total-label">Total</span>
            <span class="summary-total-price">$<?= number_format($grandTotal, 2) ?></span>
          </div>

          <a href="checkout.php" class="btn btn-primary btn-block">
            Proceed to Checkout &nbsp;<i class="fa fa-arrow-right" style="font-size:11px"></i>
          </a>
        </div>

      </div>

    <?php else: ?>

      <div class="empty-state">
        <div class="empty-state-icon"><i class="fa fa-bag-shopping"></i></div>
        <h2>Your cart is empty</h2>
        <p>Looks like you haven't added anything yet.</p>
        <a href="shop.php" class="btn btn-primary">Start Shopping</a>
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