<?php include 'common/header.php'; ?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome | Oromo Artisan & Storyteller Marketplace</title>
  <link rel="stylesheet" href="Customer dashboard/assets/style.css">
  <link href="CSS/index.css">

  </link>
</head>

<body>
  <div style="text-align:right;max-width:900px;margin:0 auto 0.5em auto;">
    <select id="langSelect"
      style="padding:0.4em 1em;border-radius:1.2em;border:1px solid #e0c3a3;font-size:1em;background:#fffbe6;color:#7c4f1d;font-weight:bold;">
      <option value="en">English</option>
      <option value="om">Afaan Oromo</option>
      <option value="am">Amharic</option>
    </select>
  </div>
  <main>
    <div class="landing-hero">
      <h1 id="heroTitle">Welcome to Oromo Artisan & Storyteller Marketplace</h1>
      <p id="heroDesc">Discover, support, and celebrate Oromo culture through artisan crafts and oral storytelling. This
        platform connects artisans, storytellers, and customers in a vibrant, fair-trade community.</p>
    </div>
    <div class="showcase-animated-tagline" id="animatedTagline">Empowering Artisans</div>
    <div class="landing-showcase" id="showcaseCards">
      <!-- Showcase cards will be rendered by JS -->
    </div>
    <div class="landing-guides">
      <h2 id="guidesTitle">How to Get the Most Out of This Marketplace</h2>
      <ul id="guidesList">
        <!-- Guide items will be rendered by JS -->
      </ul>
    </div>
    <div class="landing-steps" id="stepsList">
      <!-- Steps will be rendered by JS -->
    </div>
    <div class="landing-cta">
      <a id="ctaBtn" href="Customer dashboard/index.php">Enter Marketplace</a>
    </div>
  </main>
  <script>
  // Language integration
  let langData = {};
  let currentLang = 'en';
  const showcaseImages = [
    "https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80",
    "https://images.unsplash.com/photo-1464983953574-0892a716854b?auto=format&fit=crop&w=400&q=80",
    "https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80",
    "https://images.unsplash.com/photo-1511367461989-f85a21fda167?auto=format&fit=crop&w=400&q=80",
    "https://images.unsplash.com/photo-1508672019048-805c876b67e2?auto=format&fit=crop&w=400&q=80"
  ];
  const showcaseLinks = [
    "Customer dashboard/products.php",
    "Customer dashboard/stories.php",
    "Customer dashboard/cart.php",
    "Customer dashboard/index.php",
    "Artisan and Story teller/artisan.php"
  ];

  const guideIcons = ["🔑", "🔎", "🎧", "🛒", "👤", "🌱"];
  const langSelect = document.getElementById('langSelect');
  fetch('lang.json')
    .then(res => res.json())
    .then(data => {
      langData = data;

      langSelect.value = currentLang; // Set dropdown to match currentLang
      renderLang(currentLang);
      setTaglines(currentLang);
    });
  langSelect.addEventListener('change', function() {
    currentLang = this.value;
    renderLang(currentLang);
    setTaglines(currentLang);
  });

  function renderLang(lang) {
    if (!langData[lang]) {
      console.error('Language data missing for', lang);
      return;
    }
    const d = langData[lang];
    document.getElementById('heroTitle').textContent = d.heroTitle;
    document.getElementById('heroDesc').textContent = d.heroDesc;
    // Showcase
    const showcase = d.showcase;
    const showcaseDiv = document.getElementById('showcaseCards');
    showcaseDiv.innerHTML = '';
    showcase.forEach((item, i) => {
      showcaseDiv.innerHTML +=
        `<div class="showcase-card"><img src="${showcaseImages[i]}" alt="${item.title}" class="showcase-img"><div class="showcase-title">${item.title}</div><div class="showcase-desc">${item.desc}</div><a href="${showcaseLinks[i]}" class="showcase-link">${item.link}</a></div>`;
    });
    // Guides
    document.getElementById('guidesTitle').textContent = d.guidesTitle;
    const guidesList = document.getElementById('guidesList');
    guidesList.innerHTML = '';
    d.guides.forEach((g, i) => {
      let li = `<li><span class=\"guide-icon\">${g.icon || guideIcons[i] || ''}</span>`;
      li += g.text;
      if (g.link && g.linkText) {
        li +=
          `<a href=\"${g.link}\" style=\"color:#7c4f1d;font-weight:bold;text-decoration:underline;\">${g.linkText}</a>`;
      }
      if (g.textAfter) li += g.textAfter;
      li += '</li>';
      guidesList.innerHTML += li;
    });
    // Steps
    const stepsDiv = document.getElementById('stepsList');
    stepsDiv.innerHTML = '';
    d.steps.forEach((step, i) => {
      stepsDiv.innerHTML +=
        `<div class="step"><span class="step-number">${i+1}</span><span class="step-icon">${guideIcons[i]}</span><div class="step-content"><h3>${step.title}</h3><p>${step.desc}</p></div></div>`;
    });
    // CTA
    document.getElementById('ctaBtn').textContent = d.cta;
  }
  // Animated tagline cycling (now language-aware)
  let taglines = [];
  let taglineIdx = 0;
  const taglineEl = document.getElementById('animatedTagline');
  let taglineInterval;

  function setTaglines(lang) {
    if (!langData[lang]) {
      console.error('Taglines: Language data missing for', lang);
      return;
    }
    taglines = langData[lang].taglines || [
      "Empowering Artisans",
      "Preserving Stories",
      "Connecting Communities",
      "Celebrating Oromo Heritage",
      "Fair-Trade, Real Impact"
    ];
    taglineIdx = 0;
    taglineEl.textContent = taglines[0];
    if (taglineInterval) clearInterval(taglineInterval);
    taglineInterval = setInterval(() => {
      taglineIdx = (taglineIdx + 1) % taglines.length;
      taglineEl.textContent = taglines[taglineIdx];
      taglineEl.style.color = taglineIdx % 2 === 0 ? '#a06c2b' : '#7c4f1d';
    }, 2200);
  }
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