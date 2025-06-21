<?php include 'common/header.php'; ?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome | Oromo Artisan & Storyteller Marketplace</title>
  <link rel="stylesheet" href="Customer dashboard/assets/style.css">
  <style>
    body { background: linear-gradient(120deg, #f8f8f8 60%, #e0c3a3 100%); }
    .landing-hero {
      background: linear-gradient(rgba(255,244,230,0.96), rgba(255,244,230,0.96)), url('https://images.unsplash.com/photo-1502086223501-7ea6ecd79368?auto=format&fit=crop&w=1200&q=80') center/cover no-repeat;
      border-radius: 1.5em;
      box-shadow: 0 8px 40px #7c4f1d22;
      padding: 3.5em 1.5em 2.7em 1.5em;
      margin: 2.5em auto 2em auto;
      max-width: 900px;
      text-align: center;
      position: relative;
      overflow: hidden;
      animation: heroFadeIn 1.2s ease;
    }
    @keyframes heroFadeIn {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .landing-hero::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: linear-gradient(120deg, rgba(224,195,163,0.18) 60%, rgba(124,79,29,0.10) 100%);
      z-index: 0;
      border-radius: 1.5em;
    }
    .landing-hero h1 {
      font-size: 2.9em;
      color: #7c4f1d;
      margin-bottom: 0.35em;
      letter-spacing: 1.5px;
      z-index: 1;
      position: relative;
      text-shadow: 0 2px 12px #fff8, 0 1px 0 #e0c3a3;
    }
    .landing-hero p {
      font-size: 1.3em;
      color: #4d2e00;
      margin-bottom: 1.7em;
      z-index: 1;
      position: relative;
      text-shadow: 0 1px 8px #fff8;
    }
    .showcase-animated-tagline {
      text-align: center;
      font-size: 1.3em;
      color: #a06c2b;
      font-weight: bold;
      margin-bottom: 1.5em;
      min-height: 2.2em;
      letter-spacing: 1px;
      transition: color 0.5s;
    }
    .landing-showcase {
      display: flex;
      flex-wrap: wrap;
      gap: 2em;
      justify-content: center;
      margin: 2.5em auto;
      max-width: 1100px;
    }
    .showcase-card {
      background: #fff;
      border-radius: 1.2em;
      box-shadow: 0 4px 16px #0001;
      width: 320px;
      padding: 2em 1.2em 1.5em 1.2em;
      text-align: center;
      display: flex;
      flex-direction: column;
      align-items: center;
      transition: box-shadow 0.2s, transform 0.2s;
      position: relative;
      opacity: 0;
      transform: translateY(40px) scale(0.98);
      transition: box-shadow 0.2s, transform 0.5s, opacity 0.5s;
    }
    .showcase-card:hover {
      box-shadow: 0 10px 32px #7c4f1d33;
      transform: translateY(-6px) scale(1.03);
    }
    .showcase-card.visible {
      opacity: 1;
      transform: translateY(0) scale(1);
    }
    .showcase-img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 1em;
      margin-bottom: 1.2em;
      box-shadow: 0 4px 16px #0002;
      border: 3px solid #e0c3a3;
      background: #f8f8f8;
    }
    .showcase-title {
      font-size: 1.25em;
      color: #7c4f1d;
      font-weight: bold;
      margin-bottom: 0.3em;
    }
    .showcase-desc {
      color: #4d2e00;
      font-size: 1.05em;
      margin-bottom: 1.1em;
    }
    .showcase-link {
      background: linear-gradient(90deg, #a06c2b 60%, #7c4f1d 100%);
      color: #fff;
      padding: 0.7em 1.7em;
      border-radius: 2em;
      text-decoration: none;
      font-weight: bold;
      font-size: 1.05em;
      box-shadow: 0 2px 8px #0002;
      transition: background 0.2s, box-shadow 0.2s;
      margin-top: auto;
      letter-spacing: 0.5px;
    }
    .showcase-link:hover { background: #a06c2b; box-shadow: 0 4px 16px #7c4f1d33; }
    .landing-steps {
      display: flex;
      flex-direction: column;
      gap: 1.7em;
      max-width: 850px;
      margin: 0 auto 2.5em auto;
    }
    .step {
      background: #fff;
      border-radius: 1.2em;
      box-shadow: 0 2px 12px #0001;
      padding: 1.7em 1.2em 1.7em 1.2em;
      text-align: left;
      display: flex;
      align-items: flex-start;
      gap: 1.2em;
      position: relative;
      transition: box-shadow 0.2s;
    }
    .step:hover {
      box-shadow: 0 6px 24px #a06c2b22;
    }
    .step-number {
      font-size: 1.5em;
      font-weight: bold;
      color: #fff;
      background: linear-gradient(90deg, #a06c2b 60%, #7c4f1d 100%);
      border-radius: 50%;
      width: 2.2em;
      height: 2.2em;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 2px 8px #7c4f1d22;
      flex-shrink: 0;
      margin-right: 0.5em;
    }
    .step-icon {
      font-size: 2em;
      color: #e0c3a3;
      flex-shrink: 0;
      margin-right: 0.5em;
    }
    .step-content h3 {
      color: #7c4f1d;
      margin: 0 0 0.3em 0;
      font-size: 1.18em;
      font-weight: bold;
    }
    .step-content p {
      color: #4d2e00;
      margin: 0;
      font-size: 1.03em;
    }
    .landing-cta {
      text-align: center;
      margin: 3em 0 0 0;
    }
    .landing-cta a {
      background: linear-gradient(90deg, #a06c2b 60%, #7c4f1d 100%);
      color: #fff;
      padding: 1.3em 3em;
      border-radius: 2.5em;
      text-decoration: none;
      font-weight: bold;
      font-size: 1.3em;
      box-shadow: 0 6px 32px #7c4f1d33;
      transition: background 0.2s, box-shadow 0.2s, transform 0.2s;
      letter-spacing: 0.7px;
      animation: ctaPulse 1.8s infinite alternate;
      display: inline-block;
    }
    .landing-cta a:hover {
      background: #a06c2b;
      box-shadow: 0 12px 40px #7c4f1d44;
      transform: scale(1.04);
    }
    @keyframes ctaPulse {
      from { box-shadow: 0 6px 32px #7c4f1d33; }
      to { box-shadow: 0 12px 48px #a06c2b55; }
    }
    .landing-guides {
      background: #fffbe6;
      border-radius: 1.2em;
      box-shadow: 0 2px 12px #e0c3a355;
      max-width: 850px;
      margin: 2.5em auto;
      padding: 2.5em 1.5em 2em 1.5em;
      text-align: left;
      border-left: 8px solid #e0c3a3;
      position: relative;
    }
    .landing-guides h2 {
      color: #7c4f1d;
      font-size: 1.6em;
      margin-bottom: 1.2em;
      font-weight: bold;
      letter-spacing: 0.5px;
      text-align: center;
    }
    .landing-guides ul {
      list-style: none;
      color: #4d2e00;
      font-size: 1.08em;
      margin: 0;
      padding: 0;
    }
    .landing-guides li {
      margin-bottom: 1.1em;
      display: flex;
      align-items: flex-start;
      gap: 0.7em;
      line-height: 1.6;
      background: #fff8e1;
      border-radius: 0.7em;
      padding: 0.7em 1em;
      box-shadow: 0 1px 4px #e0c3a322;
    }
    .guide-icon {
      font-size: 1.3em;
      color: #a06c2b;
      flex-shrink: 0;
      margin-top: 0.1em;
    }
    @media (max-width: 900px) {
      .landing-showcase { flex-direction: column; align-items: center; }
      .showcase-card { width: 95%; max-width: 350px; }
    }
    @media (max-width: 600px) {
      .landing-hero { padding: 1.5em 0.5em; }
      .landing-hero h1 { font-size: 2em; }
      .landing-hero p { font-size: 1em; }
      .landing-showcase { gap: 1.2em; }
      .showcase-card { padding: 1.2em 0.5em; }
      .landing-guides, .landing-steps { padding: 1em 0.5em; }
      .landing-guides {
        padding: 1.2em 0.5em 1em 0.5em;
        border-left: 4px solid #e0c3a3;
        box-shadow: 0 1px 6px #e0c3a322;
      }
      .landing-guides h2 {
        font-size: 1.1em;
        margin-bottom: 0.7em;
      }
      .landing-guides ul {
        font-size: 1em;
      }
      .landing-guides li {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.3em;
        padding: 0.6em 0.7em;
        font-size: 1em;
      }
      .guide-icon {
        font-size: 1.5em;
        margin-bottom: 0.2em;
      }
    }
  </style>
</head>
<body>
  <main>
    <div class="landing-hero">
      <h1>Welcome to Oromo Artisan & Storyteller Marketplace</h1>
      <p>Discover, support, and celebrate Oromo culture through artisan crafts and oral storytelling. This platform connects artisans, storytellers, and customers in a vibrant, fair-trade community.</p>
    </div>
    <div class="showcase-animated-tagline" id="animatedTagline">Empowering Artisans</div>
    <div class="landing-showcase">
      <div class="showcase-card">
        <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80" alt="Products" class="showcase-img">
        <div class="showcase-title">Featured Products</div>
        <div class="showcase-desc">See the most popular and unique Oromo artisan crafts, handpicked for you.</div>
        <a href="Customer dashboard/products.php" class="showcase-link">Browse Products</a>
      </div>
      <div class="showcase-card">
        <img src="https://images.unsplash.com/photo-1464983953574-0892a716854b?auto=format&fit=crop&w=400&q=80" alt="Stories" class="showcase-img">
        <div class="showcase-title">Oral Story Library</div>
        <div class="showcase-desc">Listen to captivating Oromo stories, legends, and folktales in audio and video.</div>
        <a href="Customer dashboard/stories.php" class="showcase-link">Explore Stories</a>
      </div>
      <div class="showcase-card">
        <img src="https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80" alt="Cart" class="showcase-img">
        <div class="showcase-title">Your Cart</div>
        <div class="showcase-desc">Review your selected items and proceed to secure checkout with local payment options.</div>
        <a href="Customer dashboard/cart.php" class="showcase-link">View Cart</a>
      </div>
      <div class="showcase-card">
        <img src="https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=crop&w=400&q=80" alt="Profile" class="showcase-img">
        <div class="showcase-title">Customer Dashboard</div>
        <div class="showcase-desc">Manage your profile, see your orders, and track your impact on the community.</div>
        <a href="Customer dashboard/index.php" class="showcase-link">Go to Dashboard</a>
      </div>
      <div class="showcase-card">
        <img src="https://images.unsplash.com/photo-1508672019048-805c876b67e2?auto=format&fit=crop&w=400&q=80" alt="Artisan & Storyteller" class="showcase-img">
        <div class="showcase-title">Artisan & Storyteller Hub</div>
        <div class="showcase-desc">Meet the talented Oromo artisans and storytellers, explore their profiles, and discover their crafts and stories. Artisans can add products, track earnings, and share their culture with the world.</div>
        <a href="Artisan and Story teller/artisan.php" class="showcase-link">Visit Artisan & Storyteller</a>
      </div>
    </div>
    <div class="landing-guides">
      <h2>How to Get the Most Out of This Marketplace</h2>
      <ul>
        <li><span class="guide-icon">🔑</span>Sign up or log in to personalize your experience and support your favorite artisans.</li>
        <li><span class="guide-icon">🔎</span>Use filters on the <a href="Customer dashboard/products.php">Products</a> page to find crafts by category, location, or artisan.</li>
        <li><span class="guide-icon">🎧</span>Listen to stories on the <a href="Customer dashboard/stories.php">Stories</a> page and share your favorites with friends.</li>
        <li><span class="guide-icon">🛒</span>Add products to your <a href="Customer dashboard/cart.php">cart</a> and check out securely with local payment options.</li>
        <li><span class="guide-icon">👤</span>Visit your <a href="Customer dashboard/index.php">dashboard</a> to manage your profile and see your order history.</li>
        <li><span class="guide-icon">🌱</span>Every purchase and story you enjoy helps preserve Oromo culture and supports local families.</li>
      </ul>
    </div>
    <div class="landing-steps">
      <div class="step">
        <span class="step-number">1</span>
        <span class="step-icon">🛍️</span>
        <div class="step-content">
          <h3>Browse Unique Products</h3>
          <p>Start by exploring beautiful, handcrafted goods made by Oromo artisans. Use filters to find crafts by category, location, or artisan—there’s something for everyone!</p>
        </div>
      </div>
      <div class="step">
        <span class="step-number">2</span>
        <span class="step-icon">📖</span>
        <div class="step-content">
          <h3>Enjoy Oral Stories</h3>
          <p>Listen to and watch traditional Oromo stories, legends, and folktales in audio and video formats. Share your favorites with friends and family.</p>
        </div>
      </div>
      <div class="step">
        <span class="step-number">3</span>
        <span class="step-icon">🛒</span>
        <div class="step-content">
          <h3>Easy Shopping & Secure Checkout</h3>
          <p>Add items to your cart and pay securely with Chapa, Telebirr, HelloCash, or Amole. Shopping is safe and simple for everyone!</p>
        </div>
      </div>
      <div class="step">
        <span class="step-number">4</span>
        <span class="step-icon">🤝</span>
        <div class="step-content">
          <h3>Support Local Communities</h3>
          <p>Your purchases empower Oromo artisans and storytellers, helping preserve culture and support families. Every action makes a difference!</p>
        </div>
      </div>
    </div>
    <div class="landing-cta">
      <a href="Customer dashboard/index.php">Enter Marketplace</a>
    </div>
  </main>
  <script>
    // Animated tagline cycling
    const taglines = [
      "Empowering Artisans",
      "Preserving Stories",
      "Connecting Communities",
      "Celebrating Oromo Heritage",
      "Fair-Trade, Real Impact"
    ];
    let taglineIdx = 0;
    const taglineEl = document.getElementById('animatedTagline');
    setInterval(() => {
      taglineIdx = (taglineIdx + 1) % taglines.length;
      taglineEl.textContent = taglines[taglineIdx];
      taglineEl.style.color = taglineIdx % 2 === 0 ? '#a06c2b' : '#7c4f1d';
    }, 2200);

    // Showcase card scroll-in animation
    function revealShowcaseCards() {
      const cards = document.querySelectorAll('.showcase-card');
      const trigger = window.innerHeight * 0.92;
      cards.forEach(card => {
        const rect = card.getBoundingClientRect();
        if (rect.top < trigger) {
          card.classList.add('visible');
        }
      });
    }
    window.addEventListener('scroll', revealShowcaseCards);
    window.addEventListener('load', revealShowcaseCards);
  </script>
<?php include './common/footer.php'; ?>
</body>