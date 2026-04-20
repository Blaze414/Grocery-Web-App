<?php
session_start();
require 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
  $name  = $_POST['name'];
  $email = $_POST['email'];
  $cart  = $_SESSION['cart'];

  try {
    $pdo->beginTransaction();
    $totalAmount = 0;
    foreach ($cart as $details) $totalAmount += $details['price'] * $details['quantity'];

    $stmt = $pdo->prepare("INSERT INTO orders (user_name, user_email, total_amount, order_date) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$name, $email, $totalAmount]);
    $orderId = $pdo->lastInsertId();

    foreach ($cart as $item => $details) {
      $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_name, quantity, price) VALUES (?, ?, ?, ?)");
      $stmt->execute([$orderId, $item, $details['quantity'], $details['price']]);
    }

    $pdo->commit();
    $_SESSION['cart'] = [];
    $orderConfirmed = true;

  } catch (PDOException $e) {
    $pdo->rollBack();
    $orderError = "Error processing your order: " . $e->getMessage();
  }
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
  <title>Checkout — Grocery Store</title>
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
    </ul>
    <div class="nav-spacer"></div>
    <a href="cart.php" class="nav-cart">
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
    <h1>Checkout</h1>
    <div class="sub">Secure order</div>
  </div>
</div>

<section class="section">
  <div class="container">

    <?php if (isset($orderConfirmed) && $orderConfirmed): ?>

      <!-- Success State -->
      <div style="max-width:520px; margin:0 auto; text-align:center; padding:60px 0">
        <div style="width:72px; height:72px; background:var(--success); border-radius:50%;
                    display:flex; align-items:center; justify-content:center; margin:0 auto 24px;
                    font-size:28px; color:white;">
          <i class="fa fa-check"></i>
        </div>
        <h2 style="font-family:var(--font-display); font-weight:800; font-size:36px;
                   text-transform:uppercase; letter-spacing:-.02em; margin-bottom:12px">
          Order Confirmed!
        </h2>
        <p style="color:var(--text-2); margin-bottom:32px; font-size:15px">
          Thank you for your order. You'll receive a confirmation email shortly.
        </p>
        <a href="index.php" class="btn btn-primary">
          <i class="fa fa-arrow-left" style="font-size:10px"></i>&nbsp; Continue Shopping
        </a>
      </div>

    <?php elseif (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>

      <?php if (isset($orderError)): ?>
        <div class="alert alert-warning" style="margin-bottom:24px">
          <i class="fa fa-triangle-exclamation"></i> <?= htmlspecialchars($orderError) ?>
        </div>
      <?php endif; ?>

      <div class="checkout-layout">

        <!-- Form -->
        <div>
          <div class="checkout-box">
            <div class="checkout-box-title">
              <i class="fa fa-user" style="font-size:14px; margin-right:8px"></i> Contact Details
            </div>
            <form method="post" id="checkoutForm">
              <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control"
                       placeholder="Your full name" required/>
              </div>
              <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control"
                       placeholder="you@example.com" required/>
              </div>
              <button type="submit" name="confirm_order" class="btn btn-primary btn-block"
                      style="margin-top:8px">
                <i class="fa fa-lock" style="font-size:11px"></i>&nbsp; Confirm Order &nbsp;·&nbsp;
                $<?= number_format($totalPrice + ($totalPrice >= 30 ? 0 : 4.99), 2) ?>
              </button>
            </form>
          </div>
        </div>

        <!-- Order summary -->
        <div>
          <div class="checkout-box">
            <div class="checkout-box-title">
              <i class="fa fa-bag-shopping" style="font-size:13px; margin-right:8px"></i> Order Summary
            </div>
            <?php
              $subtotal = $totalPrice;
              $delivery = $subtotal >= 30 ? 0 : 4.99;
              $grand    = $subtotal + $delivery;
            ?>
            <?php foreach ($_SESSION['cart'] as $item => $d):
              $itemTotal = $d['price'] * $d['quantity'];
            ?>
              <div class="order-line">
                <div>
                  <div class="order-line-name"><?= htmlspecialchars($item) ?></div>
                  <div class="order-line-qty">× <?= $d['quantity'] ?> &nbsp;·&nbsp; $<?= number_format($d['price'],2) ?> each</div>
                </div>
                <div class="order-line-price">$<?= number_format($itemTotal, 2) ?></div>
              </div>
            <?php endforeach; ?>

            <div style="margin-top:16px; padding-top:16px; border-top:1px solid var(--border)">
              <div style="display:flex; justify-content:space-between; margin-bottom:8px;
                          font-size:13px; color:var(--text-2)">
                <span>Subtotal</span><span>$<?= number_format($subtotal,2) ?></span>
              </div>
              <div style="display:flex; justify-content:space-between; margin-bottom:16px;
                          font-size:13px; color:var(--text-2)">
                <span>Delivery</span>
                <span><?= $delivery == 0
                  ? '<span style="color:var(--success);font-weight:600">Free</span>'
                  : '$'.number_format($delivery,2) ?></span>
              </div>
              <div style="display:flex; justify-content:space-between;
                          border-top:2px solid var(--black); padding-top:16px">
                <span style="font-family:var(--font-display); font-weight:700; font-size:13px;
                             letter-spacing:.08em; text-transform:uppercase">Total</span>
                <span style="font-family:var(--font-display); font-weight:800; font-size:24px;
                             letter-spacing:-.02em">$<?= number_format($grand,2) ?></span>
              </div>
            </div>
          </div>

          <a href="cart.php" style="display:block; margin-top:12px; text-align:center;
             font-family:var(--font-display); font-size:12px; letter-spacing:.08em;
             text-transform:uppercase; color:var(--text-3)">
            <i class="fa fa-arrow-left" style="font-size:9px"></i>&nbsp; Edit Cart
          </a>
        </div>

      </div>

    <?php else: ?>

      <div class="empty-state">
        <div class="empty-state-icon"><i class="fa fa-bag-shopping"></i></div>
        <h2>Nothing to checkout</h2>
        <p>Your cart is empty. Add some products first.</p>
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
<script src="transitions.js"></script>
</body>
</html>