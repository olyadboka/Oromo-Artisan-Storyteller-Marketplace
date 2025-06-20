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
    background: #b91c1c;
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
    background: #b91c1c;
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
    background: #7f1d1d;
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
    <i class="fa-solid fa-gift me-2"></i>
    Free shipping on orders over 1500 ETB | Support Oromo artisans
  </div>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
        <span class="navbar-brand-logo">
          <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" width="28" height="28">
            <!-- Oak tree SVG path -->
            <path
              d="M12 2C9.243 2 7 4.243 7 7c0 .34.03.674.09 1.001C5.276 8.165 4 9.582 4 11.25c0 1.657 1.343 3 3 3h1v2.5c0 .276.224.5.5.5h1.5V20c0 .552.448 1 1 1s1-.448 1-1v-3.75h1.5c.276 0 .5-.224.5-.5V14.25h1c1.657 0 3-1.343 3-3 0-1.668-1.276-3.085-3.09-3.249A4.98 4.98 0 0 0 17 7c0-2.757-2.243-5-5-5zm-2 7c0-1.654 1.346-3 3-3s3 1.346 3 3c0 .552-.448 1-1 1h-4c-.552 0-1-.448-1-1zm-3 2.25c0-.69.56-1.25 1.25-1.25h11.5c.69 0 1.25.56 1.25 1.25s-.56 1.25-1.25 1.25h-11.5c-.69 0-1.25-.56-1.25-1.25z" />
          </svg>
        </span>
        <span class="fw-bold fs-4 text-dark">
          <span class="text-danger">Oromo</span> Marketplace
        </span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
        aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-lg-center">
          <li class="nav-item">
            <a class="nav-link fw-medium" href="artisans.php"><i class="fa-solid fa-hands-helping me-1"></i>Artisans</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-medium" href="stories.php"><i class="fa-solid fa-book-open me-1"></i>Stories</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-medium" href="categories.php"><i class="fa-solid fa-th-large me-1"></i>Categories</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-medium" href="about.php"><i class="fa-solid fa-info-circle me-1"></i>About</a>
          </li>
          <li class="nav-item dropdown ms-lg-3">
            <a class="nav-link dropdown-toggle" href="#" id="langDropdown" role="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              <i class="fa-solid fa-language me-1"></i>EN
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
              <li><a class="dropdown-item" href="#">Afaan Oromo</a></li>
              <li><a class="dropdown-item" href="#">English</a></li>
              <li><a class="dropdown-item" href="#">Amharic</a></li>
            </ul>
          </li>
          <li class="nav-item ms-lg-3">
            <form class="d-flex" role="search">
              <input class="form-control form-control-sm me-2" type="search" placeholder="Search" aria-label="Search">
              <button class="btn btn-outline-danger btn-sm" type="submit"><i class="fa-solid fa-search"></i></button>
            </form>
          </li>
          <li class="nav-item ms-lg-3 position-relative">
            <a class="nav-link" href="account.php" aria-label="Account">
              <i class="fa-solid fa-user-circle fs-5"></i>
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