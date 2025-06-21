<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Your Cart & Checkout | Oromo Artisan Marketplace</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body { background: linear-gradient(120deg, #f8f8f8 60%, #e0c3a3 100%); }
    .cart-container {
      max-width: 600px; margin: 3em auto; background: #fff; border-radius: 1.5em; box-shadow: 0 4px 24px #0002; padding: 2.5em 2em 2em 2em;
    }
    .cart-header { color: #7c4f1d; font-size: 2em; margin-bottom: 0.5em; text-align: center; }
    .cart-list { margin: 1.5em 0 1em 0; padding: 0; list-style: none; }
    .cart-list li { display: flex; align-items: center; justify-content: space-between; padding: 0.7em 0; border-bottom: 1px solid #eee; }
    .cart-list .cart-item-name { font-weight: bold; color: #7c4f1d; }
    .cart-list .cart-item-price { color: #2e7d32; font-weight: bold; }
    .cart-total { text-align: right; font-size: 1.2em; color: #2e7d32; font-weight: bold; margin-bottom: 1.5em; }
    .checkout-form label { font-weight: bold; color: #7c4f1d; }
    .checkout-form select, .checkout-form button {
      padding: 0.7em; border-radius: 1em; border: 1px solid #d1bfa3; font-size: 1em; margin-top: 0.5em;
    }
    .checkout-form button {
      background: #7c4f1d; color: #fff; border: none; font-weight: bold; margin-top: 1.2em; box-shadow: 0 2px 8px #0002; transition: background 0.2s; cursor: pointer;
    }
    .checkout-form button:hover { background: #a06c2b; }
    .cart-impact {
      background: #f7e7d3; border-radius: 1em; padding: 1em; margin: 2em auto 0 auto; color: #4d2e00; font-size: 1.1em; text-align: center;
    }
    #orderStatus { text-align: center; margin-top: 1em; color: #7c4f1d; font-size: 1.1em; }
    footer {
      margin-top: 3em;
      background: #7c4f1d;
      color: #fff;
      text-align: center;
      padding: 1.5em 1em;
      border-radius: 1em 1em 0 0;
    }
    footer div {
      margin: 0.5em 0;
    }
    footer .footer-title {
      font-size: 1.1em;
      font-weight: bold;
    }
    footer .footer-subtitle {
      margin: 0.5em 0;
    }
    footer .footer-copy {
      font-size: 0.95em;
    }
  </style>
  <script defer src="assets/cart.js"></script>
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
    <div class="cart-container">
      <div class="cart-header">Your Cart & Checkout</div>
      <ul id="cartItems" class="cart-list"></ul>
      <div id="cartTotal" class="cart-total"></div>
      <form id="checkoutForm" class="checkout-form" action="payment.php" method="POST">
        <label for="gateway">Payment Method:</label><br>
        <select name="gateway" id="gateway" required>
          <option value="Chapa">Chapa (Visa/MasterCard/ETB)</option>
          <option value="Telebirr">Telebirr (ETB)</option>
          <option value="HelloCash">HelloCash (ETB)</option>
          <option value="Amole">Amole (ETB)</option>
        </select><br>
        <button type="submit">Checkout</button>
      </form>
      <div id="orderStatus"></div>
      <div class="cart-impact">
        <strong>Business Impact:</strong> Your purchase supports Oromo artisans, strengthens local businesses, and helps preserve our cultural legacy. Thank you for making a difference!
      </div>
    </div>
  </main>
  <footer>
    <div class="footer-title">Oromo Artisan & Storyteller Marketplace</div>
    <div class="footer-subtitle">Empowering Oromo artisans, storytellers, and communities</div>
    <div class="footer-copy">&copy; 2025 Oromo Marketplace. All rights reserved.</div>
  </footer>
</body>
</html>
