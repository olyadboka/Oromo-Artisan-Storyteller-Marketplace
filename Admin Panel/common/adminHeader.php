<!-- Admin Dashboard Custom Header (Modular, Responsive, Dropdown Menu) -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
  .admin-header {
    background: linear-gradient(90deg, #1a4a7a 0%, #2d7a2d 100%);
    color: #fff;
    font-family: 'Montserrat', Arial, sans-serif;
    box-shadow: 0 4px 24px rgba(26,74,122,0.10);
    position: sticky;
    top: 0;
    z-index: 100;
  }
  .admin-header-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 1400px;
    margin: 0 auto;
    padding: 0 32px;
    height: 74px;
    position: relative;
  }
  .admin-header-logo {
    display: flex;
    align-items: center;
    gap: 14px;
  }
  .admin-header-logo-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    background: #1cb933;
    border-radius: 50%;
    padding: 10px 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  }
  .admin-header-logo-icon img {
    width: 32px; height: 32px;
  }
  .admin-header-logo-text {
    font-size: 1.5em;
    font-weight: 700;
    letter-spacing: 0.02em;
    font-family: 'Montserrat', Arial, sans-serif;
  }
  .admin-header-logo-text .oromo-red {
    color: #e53935;
    font-weight: 800;
    letter-spacing: 0.01em;
  }
  .admin-header-nav {
    display: flex;
    gap: 24px;
    transition: all 0.2s;
    align-items: center;
  }
  .admin-header-nav a {
    color: #fff;
    font-weight: 600;
    text-decoration: none;
    font-size: 1.08em;
    padding: 6px 12px;
    border-radius: 6px;
    transition: background 0.18s, color 0.18s;
  }
  .admin-header-nav a.active, .admin-header-nav a:hover {
    background: rgba(255,255,255,0.13);
    color: #f7b731;
  }
  .admin-header-user {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-left: 18px;
  }
  .admin-header-user span {
    font-size: 1em;
    font-weight: 500;
    opacity: 0.85;
  }
  .admin-header-avatar {
    background: #fff;
    color: #1a4a7a;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.1em;
    box-shadow: 0 2px 8px rgba(26,74,122,0.10);
  }
  .admin-header-menu-btn {
    display: none;
    background: none;
    border: none;
    color: #fff;
    font-size: 2em;
    margin-left: 18px;
    cursor: pointer;
    z-index: 102;
    position: absolute;
    top: 18px;
    right: 32px;
  }
  @media (max-width: 900px) {
    /* Fix for admin header alignment on dashboard */
    .admin-header-inner {
      flex-direction: row !important;
      align-items: center !important;
      height: 56px !important;
      padding: 0 8px !important;
      gap: 0 !important;
      position: relative !important;
      justify-content: flex-start !important;
    }
    .admin-header-logo {
      display: flex !important;
      flex-direction: row !important;
      align-items: center !important;
      min-width: 0 !important;
      max-width: 60vw !important;
      gap: 6px !important;
      height: 40px !important;
      z-index: 105 !important;
      justify-content: flex-start !important;
    }
    .admin-header-logo-icon {
      width: 28px !important;
      height: 28px !important;
      padding: 4px 6px !important;
      min-width: 28px !important;
      min-height: 28px !important;
      box-shadow: none !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
    }
    .admin-header-logo-icon img {
      width: 20px !important;
      height: 20px !important;
      display: block !important;
    }
    .admin-header-logo-text {
      font-size: 0.98em !important;
      max-width: 100% !important;
      overflow: hidden !important;
      text-overflow: ellipsis !important;
      white-space: nowrap !important;
      display: block !important;
      font-weight: 700 !important;
      text-align: left !important;
    }
    .admin-header-menu-btn {
      display: block;
      position: absolute;
      top: 10px;
      right: 10px;
      margin-left: 0;
      z-index: 110;
      font-size: 1.7em;
    }
    .admin-header-nav {
      flex-direction: column;
      gap: 0;
      width: 100vw;
      display: none !important;
      background: linear-gradient(90deg, #1a4a7a 0%, #2d7a2d 100%);
      position: fixed;
      left: 0;
      top: 56px;
      box-shadow: 0 4px 24px rgba(26,74,122,0.10);
      border-radius: 0 0 12px 12px;
      z-index: 120;
      align-items: flex-start;
      justify-content: flex-start;
    }
    .admin-header-nav.show {
      display: flex !important;
    }
    .admin-header-nav a {
      padding: 14px 24px;
      border-radius: 0;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      width: 100%;
      text-align: left;
    }
    .admin-header-user {
      position: absolute;
      top: 10px;
      right: 48px;
      margin-top: 0;
      z-index: 115;
      background: none;
    }
  }
</style>
<header class="admin-header">
  <div class="admin-header-inner">
    <div class="admin-header-logo">
      <span class="admin-header-logo-icon">
        <img src="https://www.svgrepo.com/show/475458/tree.svg" alt="Logo" />
      </span>
      <span class="admin-header-logo-text"><span class="oromo-red">Oromo</span> Artisan & Storyteller Admin</span>
    </div>
    <button class="admin-header-menu-btn" id="adminMenuBtn" aria-label="Open menu"><i class="fa fa-bars"></i></button>
    <nav class="admin-header-nav" id="adminHeaderNav">
      <a href="/Admin Panel/adminDashboard.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='adminDashboard.php'?'active':''; ?>">Dashboard</a>
      <a href="/Admin Panel/adminUsers.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='adminUsers.php'?'active':''; ?>">Users</a>
      <a href="/Admin Panel/adminContent.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='adminContent.php'?'active':''; ?>">Content</a>
      <a href="/Admin Panel/adminCommission.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='adminCommission.php'?'active':''; ?>">Commission</a>
      <a href="/Admin Panel/adminCuration.php" class="<?php echo basename($_SERVER['PHP_SELF'])=='adminCuration.php'?'active':''; ?>">Curation</a>
    </nav>
    <div class="admin-header-user">
      <span>Admin</span>
      <span class="admin-header-avatar">A</span>
    </div>
  </div>
</header>
<script>
// Mobile menu toggle
(function() {
  const menuBtn = document.getElementById('adminMenuBtn');
  const nav = document.getElementById('adminHeaderNav');
  let menuOpen = false;
  function closeMenu() {
    nav.classList.remove('show');
    menuOpen = false;
    document.body.style.overflow = '';
  }
  function openMenu() {
    nav.classList.add('show');
    menuOpen = true;
    document.body.style.overflow = 'hidden';
  }
  if (menuBtn && nav) {
    menuBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      if(menuOpen) closeMenu(); else openMenu();
    });
    // Close menu on nav link click (mobile)
    Array.from(nav.getElementsByTagName('a')).forEach(function(link) {
      link.addEventListener('click', function() {
        if(window.innerWidth <= 900) closeMenu();
      });
    });
    // Optional: close menu on outside click
    document.addEventListener('click', function(e) {
      if(window.innerWidth > 900) return;
      if(!nav.contains(e.target) && !menuBtn.contains(e.target)) closeMenu();
    });
    // Prevent menu/user overlap on resize
    window.addEventListener('resize', function() {
      if(window.innerWidth > 900) closeMenu();
    });
  }
})();
</script>
<!-- End Admin Dashboard Custom Header -->
