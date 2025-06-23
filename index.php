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
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@700;800&display=swap" rel="stylesheet">
  <style>
  body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 
               Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
    background: linear-gradient(120deg, #f8f8f8 60%, #e0c3a3 100%);
    line-height: 1.6;
  color: #333;
  }

  .landing-hero {
    margin: 0;
    padding-top: 0;
    /* Only gradient here, no background image */
    background: linear-gradient(rgba(255, 244, 230, 0), rgba(255, 244, 230, 0));
    background-blend-mode: lighten;
    border-radius: 1.5em;
    box-shadow: 0 8px 40px #7c4f1d22;
    padding: 3.5em 1.5em 2.7em 1.5em;
    max-width: 100vw;
    height: calc(80vh - 7.3rem);
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
    margin-top: 0 !important;
    font-size: 3.5rem;
    font-weight: 800;
  line-height: 1.2;
    font-family: 'Poppins', sans-serif;
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
    font-size: 1.25rem; /* 20px */
    line-height: 1.6;
    color: #f8f8ff;
    margin-bottom: 1.7em;
    z-index: 1;
    position: relative;
    text-shadow: 0 1px 8px #fff8;
    padding: 1rem 8rem 0;
    max-width: 800px;
    font-weight: 400;
    text-align:center;
    align-self:center;
     margin: 0 auto 1.7em;
  }

  .showcase-animated-tagline {
    text-align: center;
    font-size: 1.5em;
    color: #a06c2b;
    font-weight: bold;
    margin-bottom: 1.5em;
    min-height: 2.2em;
    letter-spacing: 1px;
    transition: color 0.5s;
    font-weight: 600;
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
    font-size: 1.375em;
    color: #7c4f1d;
    font-weight: 700;
    margin-bottom: 0.3em;
  }

  .showcase-desc {
    color: #4d2e00;
    font-size: 1.0625em;
    margin-bottom: 1.1em;
     line-height: 1.5;
  }

  .showcase-link {
    background: linear-gradient(90deg, #a06c2b 60%, #7c4f1d 100%);
    color: #fff;
    padding: 0.7em 1.7em;
    border-radius: 2em;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.0625em;
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
    font-size: 1.25em;
    font-weight: 600;
  }

  .step-content p {
    color: #4d2e00;
    margin: 0;
    font-size: 1.0625em;
  }

  .landing-cta {
    font-size: 1.25rem;
    font-weight: 700;
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
    font-size: 1.75em;
    margin-bottom: 1.2em;
    font-weight: 700;
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
    font-size: 1.125rem; /* 18px */
    line-height: 1.6;
  }

  .guide-icon {
    font-size: 1.3em;
    color: #a06c2b;
    flex-shrink: 0;
    margin-top: 0.1em;
  }

  .lang-select-bar {
    position: absolute;
    top: 1.2em;
    right: 1.2em;
    z-index: 2;
    max-width: 320px;
    width: auto;
    margin: 0 !important;
    padding: 0 !important;
    text-align: right;
    display: block;
    background: rgba(249, 171, 171, 0.85);
    border-radius: 2em;
    box-shadow: 0 2px 12px rgba(224, 163, 163, 0.33);
    border: 1.5px solidrgb(255, 0, 0);
    min-width: 120px;
  }
  .lang-select-bar select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    background: transparent;
    border: none;
    outline: none;
    font-size: 1em;
    font-weight: bold;
    color:rgb(255, 188, 110);
    padding: 0.5em 2.2em 0.5em 1.2em;
    border-radius: 2em;
    background-image: url('data:image/svg+xml;utf8,<svg fill="%237c4f1d" height="18" viewBox="0 0 20 20" width="18" xmlns="http://www.w3.org/2000/svg"><path d="M7.293 8.293a1 1 0 011.414 0L10 9.586l1.293-1.293a1 1 0 111.414 1.414l-2 2a1 1 0 01-1.414 0l-2-2a1 1 0 010-1.414z"/></svg>');
    background-repeat: no-repeat;
    background-position: right 1em center;
    background-size: 1.1em;
    box-shadow: none;
    transition: background 0.2s, color 0.2s;
    cursor: pointer;
    min-width: 100px;
  }
  .lang-select-bar select:focus {
    background-color: #fffbe6;
    color: #a06c2b;
    outline: 2px solidrgb(255, 0, 0);
  }
  @media (max-width: 900px) {
    .lang-select-bar {
      top: 0.7em;
      right: 0.7em;
      max-width: 60vw;
      min-width: 90px;
    }
  }
  @media (max-width: 600px) {
    .landing-hero {
      padding: 2.7em 0.5em 1.2em 0.5em;
      margin: 0 !important;
      height: auto;
      min-height: calc(55vh - 6.1rem);
    }
    .lang-select-bar {
      top: 0.5em;
      right: 0.5em;
      max-width: 90vw;
      min-width: 80px;
      box-shadow: 0 1px 6px #e0c3a355;
    }
    .lang-select-bar select {
      font-size: 0.98em;
      padding: 0.45em 1.7em 0.45em 0.8em;
      min-width: 70px;
    }
    .landing-hero h1 {
      font-size: 2em;
      padding: 1.5em 0.2em 0 0.2em;
      line-height: 1.15;
      word-break: break-word;
    }
    .landing-hero p {
      font-size: 1em;
      padding: 0.5em 0.2em 0 0.2em;
      line-height: 1.4;
      word-break: break-word;
    }
  }
  .landing-hero {
    margin-top: 1rem;
    position: relative;
    /* Remove background image here, keep only gradient if desired */
    background: linear-gradient(rgba(255, 244, 230, 0), rgba(255, 244, 230, 0));
    background-blend-mode: lighten;
    border-radius: 1.5em;
    box-shadow: 0 8px 40px #7c4f1d22;
    padding: 3.5em 1.5em 2.7em 1.5em;
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

  
</head>

<body>
 <?php include 'common/headerIndex.php'; ?>
  <!-- Main Content Start -->
  <main style="padding-top:7.3rem;">
    <div class="landing-hero">
      <div class="lang-select-bar">
        <select id="langSelect"
          style="padding:0.2em 2em;border-radius:1.2em;border:1px solid #e0c3a3;font-size:1em;background:#fffbe6;color:#7c4f1d;font-weight:bold;">
          <option value="en">English</option>
          <option value="om">Afaan Oromo</option>
          <option value="am">Amharic</option>
        </select>
      </div>
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
      <a id="ctaBtn" href="Customer dashboard/products.php">Enter Marketplace</a>
    </div>
  </main>
  <!-- Main Content End -->
  <style>
  body {
    background: linear-gradient(120deg, #f8f8f8 60%, #e0c3a3 100%);
  }

  /* Chatbot Styles */
#chatbotContainer {
  border: 1px solid #e0c3a3;
  z-index: 1000;
}

#chatMessages {
  scrollbar-width: thin;
  scrollbar-color: #e0c3a3 #f8f8f8;
}

#chatMessages::-webkit-scrollbar {
  width: 6px;
}

#chatMessages::-webkit-scrollbar-track {
  background: #f8f8f8;
}

#chatMessages::-webkit-scrollbar-thumb {
  background-color: #e0c3a3;
  border-radius: 3px;
}

#userInput:focus {
  outline: 2px solid #e0c3a3;
  border-color: transparent;
}

  .landing-hero {
    margin: 0;
    padding-top: 0;
    /* Only gradient here, no background image */
    background: linear-gradient(rgba(255, 244, 230, 0), rgba(255, 244, 230, 0));
    background-blend-mode: lighten;
    border-radius: 1.5em;
    box-shadow: 0 8px 40px #7c4f1d22;
    padding: 3.5em 1.5em 2.7em 1.5em;
    max-width: 100vw;
    height: calc(80vh - 7.3rem);
    text-align: center;
    position: relative;
    overflow: hidden;
    animation: heroFadeIn 1.2s ease;
  
  }
  @media (max-width: 600px) {
    .landing-hero {
      text-align: center;
      justify-content: center;
      padding: 1.2em 0.5em 1.2em 0.5em;
      margin: 0 !important;
      height: auto;
      min-height: calc(55vh - 6.1rem);
    }
  }
  .landing-hero {
    position: relative;
    /* Remove background image here, keep only gradient if desired */
    background: linear-gradient(rgba(255, 244, 230, 0), rgba(255, 244, 230, 0));
    background-blend-mode: lighten;
    border-radius: 1.5em;
    box-shadow: 0 8px 40px #7c4f1d22;
    padding: 3.5em 1.5em 2.7em 1.5em;
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
 <!-- Chatbot Button -->
<button id="chatbotToggle" class="fixed bottom-6 right-6 bg-yellow-400 text-gray-900 p-4 rounded-full shadow-lg hover:bg-yellow-500 transition z-50">
  <i class="fas fa-robot text-xl"></i>
</button>

<!-- Chatbot Container -->
<div id="chatbotContainer" class="fixed bottom-20 right-6 w-80 bg-white rounded-lg shadow-xl hidden flex flex-col z-50" style="height: 60vh;">
  <div class="bg-yellow-400 p-3 rounded-t-lg flex justify-between items-center">
    <h3 class="font-bold">Oromo Guide Assistant</h3>
    <button id="closeChatbot" class="text-gray-700 hover:text-gray-900">✕</button>
  </div>
  
  <div id="chatMessages" class="flex-1 p-4 overflow-y-auto">
    <!-- Messages will appear here -->
  </div>
  
  <div class="p-3 border-t">
    <input type="text" id="userInput" placeholder="Ask about marketplace..." class="w-full p-2 border rounded focus:outline-none focus:ring-2 focus:ring-yellow-400">
    <button id="sendMessage" class="mt-2 bg-yellow-400 text-gray-900 py-2 px-4 rounded hover:bg-yellow-500 w-full font-semibold transition">
      Send
    </button>
  </div>
</div>

<script>
// Configuration - Exactly matches quickstart format
const GEMINI_API_KEY = "AIzaSyC746ZGht2D9eR3pnlTot0OYcW4aMt6HFg";
const MODEL_NAME = "gemini-2.0-flash"; // Using exact model from quickstart

// DOM Elements
const elements = {
  toggle: document.getElementById('chatbotToggle'),
  container: document.getElementById('chatbotContainer'),
  close: document.getElementById('closeChatbot'),
  messages: document.getElementById('chatMessages'),
  input: document.getElementById('userInput'),
  send: document.getElementById('sendMessage')
};

// Toggle Chatbot
elements.toggle.addEventListener('click', () => {
  elements.container.classList.toggle('hidden');
});

elements.close.addEventListener('click', () => {
  elements.container.classList.add('hidden');
});

// API Call - Matches quickstart curl example exactly
async function generateContent(userMessage) {
  const apiUrl = `https://generativelanguage.googleapis.com/v1beta/models/${MODEL_NAME}:generateContent?key=${GEMINI_API_KEY}`;
  
  const response = await fetch(apiUrl, {
    method: 'POST',
    headers: { 
      'Content-Type': 'application/json' // Exactly as in quickstart
    },
    body: JSON.stringify({
      contents: [{
        parts: [{ 
          text: userMessage // Simple text input like quickstart example
        }]
      }]
    }) // No extra parameters (matches quickstart minimalism)
  });

  if (!response.ok) {
    const error = await response.json();
    throw new Error(error.error?.message || "API request failed");
  }

  return await response.json();
}

// System instructions are now prepended to user messages
function formatMessage(userMessage) {
  return `You are an Oromo Marketplace assistant. Respond concisely.\n\nUser: ${userMessage}`;
}

// Enhanced Send Function
async function sendMessageToGemini(userMessage) {
  addMessage(userMessage, 'user');
  showTypingIndicator();

  try {
    const result = await generateContent(formatMessage(userMessage));
    const botResponse = result.candidates[0].content.parts[0].text;
    addMessage(botResponse, 'bot');
  } catch (error) {
    console.error("API Error:", error);
    addMessage("Sorry, I can't respond right now. Please try again later.", 'bot');
  } finally {
    hideTypingIndicator();
  }
}

// Helper Functions (unchanged)
function addMessage(text, sender) {
  const messageDiv = document.createElement('div');
  messageDiv.className = `mb-3 p-3 rounded-lg max-w-[90%] ${sender === 'user' ? 'bg-gray-100 ml-auto' : 'bg-yellow-50 mr-auto'}`;
  messageDiv.textContent = text;
  elements.messages.appendChild(messageDiv);
  elements.messages.scrollTop = elements.messages.scrollHeight;
}

let typingIndicator = null;
function showTypingIndicator() {
  typingIndicator = document.createElement('div');
  typingIndicator.className = 'mb-3 p-3 rounded-lg bg-yellow-50 mr-auto max-w-[90%] typing-indicator';
  typingIndicator.innerHTML = '<span class="dot"></span><span class="dot"></span><span class="dot"></span>';
  elements.messages.appendChild(typingIndicator);
  elements.messages.scrollTop = elements.messages.scrollHeight;
}

function hideTypingIndicator() {
  if (typingIndicator) {
    typingIndicator.remove();
    typingIndicator = null;
  }
}

// Event Listeners
elements.send.addEventListener('click', () => {
  const message = elements.input.value.trim();
  if (message) {
    sendMessageToGemini(message);
    elements.input.value = '';
  }
});

elements.input.addEventListener('keypress', (e) => {
  if (e.key === 'Enter') elements.send.click();
});

// Initial greeting
window.addEventListener('load', () => {
  addMessage("Hello! I'm your Oromo Marketplace guide. How can I help?", 'bot');
});
</script>

<style>
  /* Chatbot specific styles */
  .typing-indicator {
    display: inline-block;
  }
  
  .typing-indicator .dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background-color: #a06c2b;
    margin: 0 2px;
    animation: bounce 1.4s infinite ease-in-out;
  }
  
  .typing-indicator .dot:nth-child(2) {
    animation-delay: 0.2s;
  }
  
  .typing-indicator .dot:nth-child(3) {
    animation-delay: 0.4s;
  }
  
  @keyframes bounce {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-5px); }
  }
  
  #chatMessages::-webkit-scrollbar {
    width: 6px;
  }
  
  #chatMessages::-webkit-scrollbar-track {
    background: #f1f1f1;
  }
  
  #chatMessages::-webkit-scrollbar-thumb {
    background-color: #e0c3a3;
    border-radius: 3px;
  }
</style>
</body>