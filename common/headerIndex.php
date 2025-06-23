<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Always start session and include dbConnection
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/dbConnection.php';

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
<!-- Announcement Bar (fixed above header) -->
<style>
  .oas-announcement-bar {
    background: rgb(28, 185, 51);
    color: #fff;
    font-size: 0.95rem;
    padding: 0.4rem 0;
    text-align: center;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    z-index: 1100;
    letter-spacing: 0.01em;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
  }
  .oas-announcement-icon {
    margin-right: 0.5em;
    font-size: 1.2em;
    vertical-align: middle;
  }
  /* Ensure body/main content is not hidden under fixed bars */
  body, main {
    padding-top: 2.5rem !important; /* 2.1rem announcement + 4.4rem header */
  }
</style>
<div class="oas-announcement-bar">
  <span class="oas-announcement-icon">&#129504;</span>
  Discover Oromia: Experience Culture, Nature & Hospitality
</div>

<!-- Main Site Header (Admin-inspired Design, Responsive, SVG icons) -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
<style>
  .oas-header {
    background: linear-gradient(90deg, #1a4a7a 0%, #2d7a2d 100%);
    color: #fff;
    font-family: 'Montserrat', Arial, sans-serif;
    box-shadow: 0 4px 24px rgba(26,74,122,0.10);
    position: sticky;
    top: 2.1rem; /* below announcement bar */
    z-index: 1053;
    width: 100vw;
  }
  .oas-header-inner {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.3rem 1.2rem;
    height: 74px;
    position: relative;
  }
  .oas-brand {
    display: flex;
    align-items: center;
    text-decoration: none;
  }
  .oas-brand-logo {
    width: 44px;
    height: 44px;
    background: #1cb933;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.07);
  }
  .oas-brand-logo img {
    width: 32px;
    height: 32px;
    display: block;
  }
  .oas-brand-title {
    font-weight: 700;
    font-size: 1.5rem;
    color: #fff;
    letter-spacing: 0.01em;
  }
  .oas-brand-title-main {
    color:rgb(255, 0, 0);
    margin-right: 0.2em;
  }
  .oas-nav {
    margin-left: auto;
  }
  .oas-nav-list {
    display: flex;
    align-items: center;
    gap: 0.7em;
    list-style: none;
    margin: 0;
    padding: 0;
  }
  .oas-nav-list li {
    position: relative;
  }
  .oas-nav-list a {
    text-decoration: none;
    color: #fff;
    font-weight: 600;
    font-size: 1.08em;
    padding: 0.5em 0.9em;
    border-radius: 6px;
    transition: background 0.15s, color 0.15s;
    display: flex;
    align-items: center;
    gap: 0.5em;
  }
  .oas-nav-list a:hover, .oas-nav-list a:focus {
    background: rgba(255,255,255,0.13);
    color: #f7b731;
  }
  .oas-nav-svg {
    margin-right: 0.4em;
    vertical-align: middle;
    display: inline-block;
    width: 24px !important;
    height: 24px !important;
    min-width: 24px;
    min-height: 24px;
  }
  .oas-profile-img {
    width: 33px;
    height: 33px;
    border-radius: 50%;
    object-fit: cover;
    border: 2.5px solid #1cb933;
    background: #eee;
  }
  .oas-cart-badge {
    position: absolute;
    top: -7px;
    right: -7px;
    background: #f7b731;
    color: #1a4a7a;
    font-size: 0.75rem;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
  }
  .oas-login-btn {
    background: #e53935;
    color: #fff !important;
    padding: 0.45em 1.1em;
    border-radius: 6px;
    font-weight: 600;
    font-size: 1.08em;
    border: none;
    outline: none;
    transition: background 0.15s;
    margin-left: 0.5em;
    display: inline-block;
  }
  .oas-login-btn:hover, .oas-login-btn:focus {
    background: #b71c1c;
    color: #fff;
  }
  .oas-nav-search {
    margin-top:5px;
    display: flex;
    align-items: center;
    height: 80%;
    justify-content: center;
  }
  .oas-search-form {
     margin-top:5px;
    display: flex;
    align-items: center;
    gap: 0.2em;
    height: 100%;
    justify-content: center;
  }
  .oas-search-input {
    padding: 0.35em 0.7em;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 1em;
    outline: none;
    width: 110px;
    transition: border 0.15s;
    justify-content: center;
  }
  .oas-search-input:focus {
    border: 1.5px solid #1cb933;
  }
  .oas-search-btn {
    background: #1cb933;
    color: #fff;
    border: none;
    border-radius: 5px;
    padding: 0.35em 0.7em;
    font-size: 1em;
    cursor: pointer;
    transition: background 0.15s;
  }
  .oas-search-btn:hover, .oas-search-btn:focus {
    background: #158a2a;
  }
  .oas-hamburger {
    display: none;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 38px;
    height: 38px;
    background: none;
    border: none;
    cursor: pointer;
    margin-left: 1em;
    z-index: 1060;
  }
  .oas-hamburger svg {
    width: 28px;
    height: 28px;
  }
  /* Responsive Styles */
  @media (max-width: 991.98px) {
    .oas-header-inner {
      flex-wrap: wrap;
      padding: 0.3rem 0.7rem;
    }
    .oas-nav {
      position: absolute;
      top: 100%;
      left: 0;
      width: 100vw;
      background: linear-gradient(90deg, #1a4a7a 0%, #2d7a2d 100%);
      box-shadow: 0 2px 8px rgba(0,0,0,0.04);
      display: none;
      transition: max-height 0.3s;
    }
    .oas-nav.open {
      display: block;
    }
    .oas-nav-list {
      flex-direction: column;
      align-items: flex-start;
      gap: 0;
      padding: 0.7em 0.5em;
    }
    .oas-nav-list a {
      width: 100%;
      padding: 0.9em 1.2em;
      font-size: 1.13em;
    }
    .oas-hamburger {
      display: flex;
    }
  }
  @media (max-width: 600px) {
    .oas-brand-title {
      font-size: 1.1rem;
    }
    .oas-header-inner {
      padding: 0.2rem 0.3rem;
    }
    .oas-brand-logo {
      width: 36px;
      height: 36px;
      margin-right: 7px;
    }
    .oas-brand-logo img {
      width: 24px;
      height: 24px;
    }
  }
</style>
<header class="oas-header">
  <div class="oas-header-inner">
    <a class="oas-brand" href="/index.php">
      <span class="oas-brand-logo">
        <img src="https://www.svgrepo.com/show/475458/tree.svg" alt="Oromo Marketplace Logo" width="32" height="32" />
      </span>
      <span class="oas-brand-title">
        <span class="oas-brand-title-main">Oromo</span> Artisan and Stories
      </span>
    </a>
    <button class="oas-hamburger" id="oasHamburger" aria-label="Toggle navigation" aria-expanded="false" aria-controls="oasNav">
      <svg viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg"><rect y="6" width="28" height="3.5" rx="1.75" fill="white"/><rect y="12.25" width="28" height="3.5" rx="1.75" fill="white"/><rect y="18.5" width="28" height="3.5" rx="1.75" fill="white"/></svg>
    </button>
    <nav class="oas-nav" id="oasNav">
      <ul class="oas-nav-list">
        <li><a href="/Artisan and Story teller/artisan.php">Artisans' Product</a></li>
        <li><a href="/Artisan and Story teller/storytellers.php">Oromo Stories</a></li>
        <li class="oas-nav-search">
          <form class="oas-search-form" role="search">
            <input class="oas-search-input" type="search" placeholder="Search" aria-label="Search">
            <button class="oas-search-btn" type="submit"><svg class="oas-nav-svg" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="9" cy="9" r="7" stroke="#fff" stroke-width="2"/><line x1="14.5" y1="14.5" x2="18" y2="18" stroke="#fff" stroke-width="2" stroke-linecap="round"/></svg></button>
          </form>
        </li>
        <li class="oas-nav-cart">
          <a href="/Customer dashboard/cart.php" aria-label="Cart">
            <svg class="oas-nav-svg" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="8" cy="17" r="1.5" fill="#f7b731"/><circle cx="15" cy="17" r="1.5" fill="#f7b731"/><path d="M2 2h2l2.6 11.59A2 2 0 0 0 8.5 16h6.76a2 2 0 0 0 1.97-1.68l1.3-7.32A1 1 0 0 0 17.54 6H5.21" stroke="#fff" stroke-width="2" fill="none"/></svg>
            <span class="oas-cart-badge">3</span>
          </a>
        </li>
        <li>
          <a href="/Customer dashboard/account.php" aria-label="Account">
            <?php if (!empty($profileData)): ?>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($profileData); ?>" alt="Profile" class="oas-profile-img">
            <?php else: ?>
            <svg class="oas-nav-svg" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><circle cx="10" cy="7" r="4" fill="#f7b731"/><path d="M2 18c0-3.31 3.13-6 7-6s7 2.69 7 6" stroke="#fff" stroke-width="2" fill="none"/></svg>
            <?php endif; ?>
          </a>
        </li>
        <li class="oas-nav-login">
          <a href="/login.php" class="oas-login-btn">Login</a>
        </li>
      </ul>
    </nav>
  </div>
</header>
<script>
(function() {
  var hamburger = document.getElementById('oasHamburger');
  var nav = document.getElementById('oasNav');
  if (hamburger && nav) {
    hamburger.addEventListener('click', function() {
      var expanded = hamburger.getAttribute('aria-expanded') === 'true';
      hamburger.setAttribute('aria-expanded', !expanded);
      nav.classList.toggle('open');
    });
    // Optional: close nav when clicking outside on mobile
    document.addEventListener('click', function(e) {
      if (!nav.contains(e.target) && !hamburger.contains(e.target)) {
        nav.classList.remove('open');
        hamburger.setAttribute('aria-expanded', 'false');
      }
    });
  }
})();
</script>
<!-- End Main Site Header -->
