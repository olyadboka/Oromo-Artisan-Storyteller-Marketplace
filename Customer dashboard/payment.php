<?php
// Simulate payment processing for demo
$gateways = ['Chapa', 'Telebirr', 'HelloCash', 'Amole'];
$gateway = $_POST['gateway'] ?? 'Chapa';
$orderId = rand(1000,9999);
$success = true;
$amount = 0;
$cart = [];
if (isset($_POST['cart'])) {
  $cart = json_decode($_POST['cart'], true);
  foreach ($cart as $item) {
    $amount += $item['price'];
  }
} else {
  $amount = 500; // fallback demo value
}
// Simulate processing delay
sleep(2);
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Payment Confirmation | Oromo Artisan Marketplace</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body { background: linear-gradient(120deg, #f8f8f8 60%, #e0c3a3 100%); }
    .pay-container {
      max-width: 500px; margin: 4em auto; background: #fff; border-radius: 1.5em; box-shadow: 0 4px 24px #0002; padding: 2.5em 2em 2em 2em; text-align: center;
    }
    .pay-header { color: #7c4f1d; font-size: 2em; margin-bottom: 0.5em; }
    .pay-success { color: #2e7d32; font-size: 1.2em; margin-bottom: 1em; }
    .pay-fail { color: #b71c1c; font-size: 1.2em; margin-bottom: 1em; }
    .pay-details { margin: 1.5em 0; text-align: left; }
    .pay-details strong { color: #7c4f1d; }
    .pay-order-list { margin: 1em 0 2em 0; padding: 0; list-style: none; }
    .pay-order-list li { padding: 0.3em 0; border-bottom: 1px solid #eee; }
    .pay-biz { background: #f7e7d3; border-radius: 1em; padding: 1em; margin-top: 2em; color: #4d2e00; font-size: 1.1em; }
    .pay-btn { background: #7c4f1d; color: #fff; padding: 0.8em 2em; border-radius: 2em; text-decoration: none; font-weight: bold; font-size: 1em; box-shadow: 0 2px 8px #0002; transition: background 0.2s; display: inline-block; margin-top: 1.5em; }
    .pay-btn:hover { background: #a06c2b; }
    .pay-gateway { font-weight: bold; color: #3b2c1a; }
    .pay-id { font-family: monospace; color: #a06c2b; }
  </style>
</head>
<body>
  <header style="background: linear-gradient(90deg,#7c4f1d 60%,#e0c3a3 100%);padding:2em 1em 1.5em 1em;border-radius:0 0 2em 2em;box-shadow:0 4px 24px #0001;margin-bottom:2em;position:relative;">
    <div style="display:flex;align-items:center;justify-content:center;gap:1em;flex-wrap:wrap;text-align:center;width:100%;">
      <div style="width:100%;">
        <div style="font-size:2.2em;font-weight:bold;color:#fff;letter-spacing:1px;">Oromo Artisan & Storyteller</div>
        <div style="font-size:1.1em;color:#ffe7c2;">Marketplace</div>
      </div>
    </div>
    <nav style="margin-top:1.5em;text-align:center;">
      <a href="index.html" style="color:#fff;font-weight:bold;margin:0 1.2em;text-decoration:none;font-size:1.1em;">Dashboard</a>
      <a href="products.html" style="color:#fff;font-weight:bold;margin:0 1.2em;text-decoration:none;font-size:1.1em;">Products</a>
      <a href="stories.html" style="color:#fff;font-weight:bold;margin:0 1.2em;text-decoration:none;font-size:1.1em;">Stories</a>
      <a href="cart.html" style="color:#fff;font-weight:bold;margin:0 1.2em;text-decoration:none;font-size:1.1em;">Cart</a>
    </nav>
  </header>
  <main>
    <div class="pay-container">
      <div class="pay-header">Payment <?php echo $success ? 'Successful' : 'Failed'; ?></div>
      <div class="<?php echo $success ? 'pay-success' : 'pay-fail'; ?>">
        <?php if ($success): ?>
          Thank you for your purchase! Your order is confirmed.<br>
          <span style="font-size:0.95em;">(Order ID: <span class="pay-id"><?= $orderId ?></span>)</span>
        <?php else: ?>
          Sorry, your payment could not be processed.
        <?php endif; ?>
      </div>
      <div class="pay-details">
        <div>Payment Method: <span class="pay-gateway"><?= htmlspecialchars($gateway) ?></span></div>
        <div>Total Amount: <strong><?= $amount ?> ETB</strong></div>
      </div>
      <?php if ($cart): ?>
        <div><strong>Order Details:</strong></div>
        <ul class="pay-order-list">
          <?php foreach ($cart as $item): ?>
            <li><?= htmlspecialchars($item['name']) ?> <span style="float:right;"><?= $item['price'] ?> ETB</span></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <div class="pay-biz">
        <strong>Business Impact:</strong><br>
        Your purchase directly supports Oromo artisans and storytellers, empowering local communities and preserving cultural heritage. Thank you for being part of this impact-driven marketplace!
      </div>
      <a class="pay-btn" href="products.html">Continue Shopping</a>
    </div>
  </main>
  <footer style="margin-top:3em;background:#7c4f1d;color:#fff;text-align:center;padding:1.5em 1em 1em 1em;border-radius:1em 1em 0 0;">
    <div style="font-size:1.1em;font-weight:bold;">Oromo Artisan & Storyteller Marketplace</div>
    <div style="margin:0.5em 0;">Empowering Oromo artisans, storytellers, and communities</div>
    <div style="font-size:0.95em;">&copy; 2025 Oromo Marketplace. All rights reserved.</div>
  </footer>
</body>
</html>
