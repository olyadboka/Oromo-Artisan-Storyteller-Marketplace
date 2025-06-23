<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Always start session and include dbConnection

include_once __DIR__ . '/common/dbConnection.php';

if (!isset($_SESSION['user_id'])) {
  // Set a default user id for testing if not set
  $_SESSION['user_id'] = 8;
}
$user_id = $_SESSION['user_id'];
$profileData = null;
if (isset($con) && $con && $con instanceof mysqli && $con->connect_errno === 0) {
  $sql = "SELECT profileImage from users where id = $user_id";
  $result = mysqli_query($con, $sql);
  if ($result) {
    while($row = mysqli_fetch_assoc($result)){
      $profileData = $row['profileImage'];
    }
  }
}
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome | Oromo Artisan & Storyteller Marketplace</title>
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

  <style>
  body {
    background: linear-gradient(120deg, #f8f8f8 60%, #e0c3a3 100%);
  }

  .landing-hero {
    margin-top: 0.7rem !important;
    /* Remove background image here, keep only gradient if desired */
    background: linear-gradient(rgba(255, 244, 230, 0), rgba(255, 244, 230, 0));
    background-blend-mode: lighten;
    border-radius: 1.5em;
    box-shadow: 0 8px 40px #7c4f1d22;
    padding: 3.5em 1.5em 2.7em 1.5em;
    margin: 2.5em auto 2em auto;
    max-width: 100vw;
    height: 80vh;
    text-align: center;
    position: relative;
    overflow: hidden;
    animation: heroFadeIn 1.2s ease;

    /* Blur background using pseudo-element */
  }

  .landing-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
      url("https://media.licdn.com/dms/image/v2/D4E12AQHQteOqo9FyOQ/article-cover_image-shrink_720_1280/article-cover_image-shrink_720_1280/0/1695207472629?e=2147483647&v=beta&t=rE19TLABHKh6-VcwvjTRJqln9mQgTUTAM0orhTBgEyg") center/cover no-repeat,
      linear-gradient(rgba(255, 244, 230, 0.7), rgba(255, 244, 230, 0.7));
    filter: blur(12px);
    z-index: -1;
    border-radius: 1.5em;
    opacity: 0.85;
  }

  .landing-hero>* {
    position: relative;
    z-index: 1;
  }

  @keyframes heroFadeIn {
    from {
      opacity: 0;
      transform: translateY(-30px);
    }

    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  .landing-hero h1 {
    margin-top: 1rem !important;
    font-size: 4rem;
    font-family: 'Poppins', 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
    color: rgb(28, 185, 51);
    margin-bottom: 0.35em;
    letter-spacing: 1.5px;
    z-index: 1;
    position: relative;
    text-shadow: 0 2px 12px #fff8, 0 1px 0 #e0c3a3;
    padding: 1rem 8rem 0;
    filter: blur(0);
  }

  .landing-hero p {
    margin-top: 0.7rem !important;
    font-size: 1.3em;
    color: #f8f8ff;
    margin-bottom: 1.7em;
    z-index: 1;
    position: relative;
    text-shadow: 0 1px 8px #fff8;
    padding: 1rem 8rem 0;
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

  .showcase-link:hover {
    background: #a06c2b;
    box-shadow: 0 4px 16px #7c4f1d33;
  }

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
    from {
      box-shadow: 0 6px 32px #7c4f1d33;
    }

    to {
      box-shadow: 0 12px 48px #a06c2b55;
    }
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

  .lang-select-bar {
    display: block;
    text-align: right;
    max-width: 420px;
    margin: -1.2em 0 0 auto !important; /* pull up closer to header */
    padding: 0 !important;
    position: relative;
    top: 0;
  }

  @media (max-width: 900px) {
    .landing-showcase {
      flex-direction: column;
      align-items: center;
    }

    .showcase-card {
      width: 95%;
      max-width: 350px;
    }
  }

  @media (max-width: 600px) {
    .landing-hero {
      padding: 1.5em 0.5em;
    }

    .landing-hero h1 {
      font-size: 2em;
    }

    .landing-hero p {
      font-size: 1em;
    }

    .landing-showcase {
      gap: 1.2em;
    }

    .showcase-card {
      padding: 1.2em 0.5em;
    }

    .landing-guides,
    .landing-steps {
      padding: 1em 0.5em;
    }

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

    .lang-select-bar {
      max-width: 100vw;
      margin: 0 0 0 auto !important;
      padding: 0 !important;
      text-align: right;
      display: block;
    }
    .landing-hero {
      margin-top: 0.3 !important; /* Remove all top margin */
      padding-top: 0 !important; /* Remove all top padding */
      max-width: 540px !important;
      margin-left: auto !important;
      margin-right: auto !important;
    }
  }

  
  </style>

  
</head>

<body>
 <?php include 'common/headerIndex.php'; ?>
  <!-- Main Content Start -->
  <main>
    <div class="lang-select-bar">
      <select id="langSelect"
        style="padding:0.4em 1em;border-radius:1.2em;border:1px solid #e0c3a3;font-size:1em;background:#fffbe6;color:#7c4f1d;font-weight:bold;">
        <option value="en">English</option>
        <option value="om">Afaan Oromo</option>
        <option value="am">Amharic</option>
      </select>
    </div>
    <div class="landing-hero">
      <h1 id="heroTitle">Oromo Artisan & Storyteller Anaadhufuu!!</h1>
      <p id="heroDesc">Discover, support, and celebrate Oromo culture through artisan crafts and oral storytelling. This
        platform connects artisans, storytellers, and Tourists in a vibrant, fair-trade community.</p>
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
  <!-- Main Content End -->
  <style>
  body {
    background: linear-gradient(120deg, #f8f8f8 60%, #e0c3a3 100%);
  }

  .landing-hero {
    margin-top: -1rem;
    /* Only gradient here, no background image */
    background: linear-gradient(rgba(255, 244, 230, 0), rgba(255, 244, 230, 0));
    background-blend-mode: lighten;
    border-radius: 1.5em;
    box-shadow: 0 8px 40px #7c4f1d22;
    padding: 3.5em 1.5em 2.7em 1.5em;
    margin: 2.5em auto 2em auto;
    max-width: 100vw;
    height: 80vh;
    text-align: center;
    position: relative;
    overflow: hidden;
    animation: heroFadeIn 1.2s ease;
  }

  .landing-hero::before {
    content: "";
    position: absolute;
    inset: 0;
    background:
      url("https://media.licdn.com/dms/image/v2/D4E12AQHQteOqo9FyOQ/article-cover_image-shrink_720_1280/article-cover_image-shrink_720_1280/0/1695207472629?e=2147483647&v=beta&t=rE19TLABHKh6-VcwvjTRJqln9mQgTUTAM0orhTBgEyg") center/cover no-repeat,
      linear-gradient(rgba(255, 244, 230, 0.7), rgba(255, 244, 230, 0.7));
    filter: blur(12px);
    z-index: -1;
    border-radius: 1.5em;
    opacity: 0.85;
  }

  .showcase-card {
    /* existing styles */
  }

  .guide-icon {
    /* existing styles */
  }

  .step {
    /* existing styles */
  }

   .navbar-brand-logo {
    width: 44px;
    height: 44px;
    background: rgb(28, 185, 51);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
  }
  .cart-badge {
    position: absolute;
    top: 0;
    right: -8px;
    background: rgb(28, 185, 51);
    color: #fff;
    font-size: 0.75rem;
    border-radius: 50%;
    width: 22px;
    height: 22px;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .announcement-bar {
    background: rgb(28, 185, 51);
    color: #fff;
    font-size: 0.95rem;
    padding: 0.4rem 0;
    text-align: center;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    z-index: 1052;
  }
  .navbar {
    position: fixed !important;
    top: 2.1rem; /* height of announcement bar */
    left: 0;
    width: 100vw;
    z-index: 1053 !important;
    border-radius: 0;
  }
  .dropdown-menu-end[aria-labelledby="langDropdown"] {
    min-width: 8rem;
  }
  /* Remove forced debug CSS for production */
  /* .navbar-collapse, .navbar-nav {
    display: flex !important;
    opacity: 1 !important;
    visibility: visible !important;
    height: auto !important;
  } */
  @media (max-width: 991.98px) {
    .navbar-collapse {
      display: none !important;
    }
    .navbar-collapse.show {
      display: block !important;
    }
    .navbar-nav {
      width: 100%;
    }
    .nav-link {
      color: #222 !important;
      font-size: 1.1em;
      padding: 0.8em 1.2em;
      width: 100%;
      text-align: left;
    }
  }
  </style>
  <script>
  // Language integration
  let langData = {};
  let currentLang = 'en';
  const showcaseImages = [
    "images/tools.jpg",
    "images/story.jpg",
    "images/cart.png",
    "images/dashboard.png",
    "images/art.jpg",
    "https://images.unsplash.com/photo-1508672019048-805c876b67e2?auto=format&fit=crop&w=400&q=80"
  ];
  const showcaseLinks = [
    "./Customer dashboard/products.php",
    "./Customer dashboard/storyLibrary.php",
    "Customer dashboard/cart.php",
    "./Customer dashboard/customer.php",
    "./Artisan and Story teller/artisan.php"
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
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>