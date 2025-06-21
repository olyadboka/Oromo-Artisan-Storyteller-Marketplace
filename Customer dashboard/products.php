<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Browse Products | Oromo Artisan Marketplace</title>
  <link rel="stylesheet" href="assets/style.css">
  <style>
    body { background: linear-gradient(120deg, #f8f8f8 60%, #e0c3a3 100%); }
    .products-hero {
      background: #fff4e6; border-radius: 1em; box-shadow: 0 4px 24px #0001; padding: 2em 1em 1em 1em; margin-bottom: 2em; text-align: center;
    }
    .products-hero h2 { color: #7c4f1d; font-size: 2em; margin-bottom: 0.2em; }
    .products-hero p { color: #4d2e00; font-size: 1.1em; }
    .filters {
      display: flex; flex-wrap: wrap; gap: 1em; justify-content: center; margin-bottom: 2em;
    }
    .filters input, .filters select {
      padding: 0.7em; border-radius: 1em; border: 1px solid #d1bfa3; font-size: 1em;
    }
    #productList {
      display: flex; flex-wrap: wrap; gap: 2em; justify-content: center;
    }
    .product-card {
      background: #fff; border-radius: 1em; box-shadow: 0 2px 12px #0001; width: 320px; padding: 1.5em 1em 1em 1em; display: flex; flex-direction: column; align-items: center; transition: box-shadow 0.2s;
    }
    .product-card:hover { box-shadow: 0 6px 24px #7c4f1d22; }
    .product-img {
      width: 220px; height: 160px; object-fit: cover; border-radius: 0.7em; margin-bottom: 1em; box-shadow: 0 2px 8px #0002;
    }
    .product-title { font-size: 1.2em; color: #7c4f1d; font-weight: bold; margin-bottom: 0.2em; }
    .product-meta { color: #4d2e00; font-size: 0.98em; margin-bottom: 0.5em; }
    .product-price { color: #2e7d32; font-size: 1.1em; font-weight: bold; margin-bottom: 0.7em; }
    .product-actions button {
      background: #7c4f1d; color: #fff; border: none; border-radius: 2em; padding: 0.6em 1.5em; margin: 0.2em 0.3em; font-size: 1em; font-weight: bold; cursor: pointer; box-shadow: 0 2px 8px #0002; transition: background 0.2s;
    }
    .product-actions button:hover { background: #a06c2b; }
    .impact-biz {
      background: #f7e7d3; border-radius: 1em; padding: 1em; margin: 2em auto 0 auto; color: #4d2e00; font-size: 1.1em; max-width: 700px; text-align: center;
    }
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
      font-size: 0.95em;
    }
  </style>
  <script defer src="assets/products.js"></script>
  <script defer src="assets/3dviewer.js"></script>
  <script defer src="assets/gltfloader.js"></script>
  <script defer src="assets/orbitcontrols.js"></script>
  <script defer src="assets/viewer360.js"></script>
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
    <section class="products-hero">
      <h2>Browse Unique Oromo Artisan Products</h2>
      <p>Support local artisans, discover authentic crafts, and make a real impact with every purchase.</p>
    </section>
    <div class="filters">
      <input type="text" id="search" placeholder="Search products...">
      <select id="categoryFilter"><option value="">All Categories</option></select>
      <select id="locationFilter"><option value="">All Locations</option></select>
    </div>
    <div id="productList"></div>
    <div class="impact-biz">
      <strong>Business Impact:</strong> Every purchase empowers Oromo artisans, supports local economies, and helps preserve cultural heritage. Choose with purpose!
    </div>
  </main>
  <footer>
    <div class="footer-title">Oromo Artisan & Storyteller Marketplace</div>
    <div class="footer-subtitle">Empowering Oromo artisans, storytellers, and communities</div>
    <div style="font-size:0.95em;">&copy; 2025 Oromo Marketplace. All rights reserved.</div>
  </footer>
</body>
</html>
