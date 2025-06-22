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
<!-- Announcement Bar -->
<div class="announcement-bar">
  <i class="fa-solid fa-compass me-2"></i>
  Discover Oromia: Experience Culture, Nature & Hospitality
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="/index.php">
      <span class="navbar-brand-logo">
        <img src="https://www.svgrepo.com/show/475458/tree.svg" alt="Oromo Marketplace Logo" width="32" height="32" />
      </span>
      <span class="fw-bold fs-4 text-dark">
        <span class="text-danger">Oromo</span> Atisan and stories
      </span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
      aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="mainNavbar">
      <!-- DEBUG: NAVBAR UL START -->
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
        <li class="nav-item">
          <a class="nav-link fw-medium" href="/Artisan and Story teller/artisan.php"><i class="fa-solid fa-hands-helping me-1"></i>Artisans' Product</a>
        </li>
        <li class="nav-item">
          <a class="nav-link fw-medium" href="/Artisan and Story teller/stories.php"><i class="fa-solid fa-book-open me-1"></i>Oromo Stories</a>
        </li>
        <li class="nav-item ms-lg-3">
          <form class="d-flex" role="search">
            <input class="form-control form-control-sm me-2" type="search" placeholder="Search" aria-label="Search">
            <button class="btn btn-outline-danger btn-sm" type="submit"><i class="fa-solid fa-search"></i></button>
          </form>
        </li>
        <li class="nav-item ms-lg-3 position-relative">
          <a class="nav-link" href="/Customer dashboard/account.php" aria-label="Account">
            <?php if (!empty($profileData)): ?>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($profileData); ?>" alt="Profile"
              style="width:32px; height:32px; border-radius:50%;">
            <?php else: ?>
            <i class="fa-solid fa-user-circle fa-2x text-secondary"></i>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item ms-lg-2 position-relative">
          <a class="nav-link position-relative" href="/Customer dashboard/cart.php" aria-label="Cart">
            <i class="fa-solid fa-shopping-cart fs-5"></i>
            <span class="cart-badge">3</span>
          </a>
        </li>
      </ul>
      <!-- DEBUG: NAVBAR UL END -->
    </div>
  </div>
</nav>

<!-- Header Styles and Scripts -->
<style>
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
  /* Force navbar and nav links to be visible for debugging */
  .navbar-collapse, .navbar-nav {
    display: flex !important;
    opacity: 1 !important;
    visibility: visible !important;
    height: auto !important;
  }
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