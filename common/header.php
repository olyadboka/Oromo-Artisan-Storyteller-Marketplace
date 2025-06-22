<?php
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
$sql = "SELECT profileImage from users where id = $user_id";
$result = mysqli_query($con, $sql);
$profileData = null;
if ($result) {
  while($row = mysqli_fetch_assoc($result)){
    $profileData = $row['profileImage'];
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Oromo Marketplace</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
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
  }

  .dropdown-menu-end[aria-labelledby="langDropdown"] {
    min-width: 8rem;
  }
  </style>
</head>

<body>
  <!-- Announcement Bar -->
  <div class="announcement-bar">
    <i class="fa-solid fa-compass me-2"></i>
    Discover Oromia: Experience Culture, Nature & Hospitality
  </div>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <span class="navbar-brand-logo">
          <img src="https://www.svgrepo.com/show/475458/tree.svg" alt="Oromo Marketplace Logo" width="32" height="32" />
          </svg>
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
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
          <li class="nav-item">
            <a class="nav-link fw-medium" href="artisans.php"><i class="fa-solid fa-hands-helping me-1"></i>Artisans'
              Product</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-medium" href="stories.php"><i class="fa-solid fa-book-open me-1"></i>Oromo Stories</a>
          </li>


          <li class="nav-item ms-lg-3">
            <form class="d-flex" role="search">
              <input class="form-control form-control-sm me-2" type="search" placeholder="Search" aria-label="Search">
              <button class="btn btn-outline-danger btn-sm" type="submit"><i class="fa-solid fa-search"></i></button>
            </form>
          </li>
          <li class="nav-item ms-lg-3 position-relative">
            <a class="nav-link" href="account.php" aria-label="Account">
              <?php if (!empty($profileData)): ?>
              <img src="data:image/jpeg;base64,<?php echo base64_encode($profileData); ?>" alt="Profile"
                style="width:32px; height:32px; border-radius:50%;">
              <?php else: ?>
              <i class="fa-solid fa-user-circle fa-2x text-secondary"></i>
              <?php endif; ?>
            </a>
          </li>
          <li class="nav-item ms-lg-2 position-relative">
            <a class="nav-link position-relative" href="cart.php" aria-label="Cart">
              <i class="fa-solid fa-shopping-cart fs-5"></i>
              <span class="cart-badge">3</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>