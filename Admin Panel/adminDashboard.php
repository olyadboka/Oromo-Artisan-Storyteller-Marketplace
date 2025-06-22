<?php
include '../common/header.php';
include '../common/dbConnection.php';

// --- Analytics Queries ---
$userCount = $conn->query("SELECT COUNT(*) as cnt FROM users")->fetch_assoc()['cnt'] ?? 0;
$orderCount = $conn->query("SELECT COUNT(*) as cnt FROM orders")->fetch_assoc()['cnt'] ?? 0;
$bestProduct = $conn->query("SELECT p.name, SUM(oi.quantity) as sold FROM order_items oi JOIN products p ON oi.item_id=p.id WHERE oi.type='product' GROUP BY oi.item_id ORDER BY sold DESC LIMIT 1")->fetch_assoc()['name'] ?? '-';
$topStory = $conn->query("SELECT s.title, SUM(oi.quantity) as sold FROM order_items oi JOIN stories s ON oi.item_id=s.id WHERE oi.type='story' GROUP BY oi.item_id ORDER BY sold DESC LIMIT 1")->fetch_assoc()['title'] ?? '-';
// User growth by month (last 6 months)
$userGrowth = $conn->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count FROM users GROUP BY month ORDER BY month DESC LIMIT 6");
$userGrowthData = [];
while ($row = $userGrowth->fetch_assoc()) {
    $userGrowthData[] = $row;
}
$userGrowthData = array_reverse($userGrowthData);
?>
<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&display=swap');
.admin-dashboard-banner {
  background: linear-gradient(90deg, #1a4a7a 0%, #2d7a2d 100%);
  color: #fff;
  border-radius: 16px;
  padding: 32px 28px 24px 28px;
  margin-bottom: 32px;
  box-shadow: 0 4px 24px rgba(26,74,122,0.10);
  display: flex;
  align-items: center;
  gap: 24px;
  flex-wrap: wrap;
}
.admin-dashboard-banner .banner-icon {
  font-size: 2.8em;
  background: rgba(255,255,255,0.13);
  border-radius: 50%;
  padding: 18px 22px;
  margin-right: 18px;
  color: #fff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.admin-dashboard-banner h1 {
  font-family: 'Montserrat', sans-serif;
  font-size: 2.1em;
  font-weight: 700;
  margin: 0 0 6px 0;
}
.admin-dashboard-banner p {
  font-size: 1.1em;
  margin: 0;
  opacity: 0.93;
}
.admin-dashboard-cards {
  gap: 30px !important;
  margin-bottom: 30px;
}
.admin-dashboard-card {
  background: linear-gradient(120deg, #f5f5f5 60%, #eaf6f0 100%);
  padding: 22px 18px;
  border-radius: 14px;
  min-width: 180px;
  box-shadow: 0 2px 12px rgba(26,74,122,0.07);
  margin-bottom: 0;
  display: flex;
  align-items: center;
  gap: 18px;
  transition: box-shadow 0.2s, transform 0.2s;
  position: relative;
  overflow: hidden;
}
.admin-dashboard-card:hover {
  box-shadow: 0 6px 24px rgba(26,74,122,0.13);
  transform: translateY(-2px) scale(1.03);
  z-index: 2;
}
.admin-dashboard-card .icon {
  font-size: 2.2em;
  color: #fff;
  background: linear-gradient(135deg, #1a4a7a 60%, #2d7a2d 100%);
  border-radius: 50%;
  padding: 14px 16px;
  box-shadow: 0 2px 8px rgba(26,74,122,0.10);
  margin-right: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.admin-dashboard-card h6 {
  font-family: 'Montserrat', sans-serif;
  font-weight: 600;
  margin-bottom: 2px;
  color: #1a4a7a;
  letter-spacing: 0.01em;
}
.admin-dashboard-card .stat {
  font-size: 1.7em;
  color: #2d7a2d;
  font-weight: bold;
}
.admin-dashboard-card .stat-secondary {
  font-size: 1.1em;
  color: #1a4a7a;
  font-weight: 600;
}
.admin-dashboard-growth {
  background: #fff;
  padding: 24px 20px 18px 20px;
  border-radius: 12px;
  max-width: 600px;
  overflow-x: auto;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  margin-top: 18px;
}
.admin-dashboard-growth h5 {
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;
  margin-bottom: 18px;
  color: #1a4a7a;
}
.admin-dashboard-growth .growth-chart {
  width: 100%;
  min-width: 320px;
  height: 120px;
  margin-bottom: 10px;
  display: block;
}
@media (max-width: 900px) {
  .admin-dashboard-flex { flex-direction: column !important; }
  .admin-dashboard { padding: 12px !important; }
  .admin-dashboard-cards { flex-direction: column !important; gap: 15px !important; }
  .admin-dashboard-card { min-width: 0 !important; width: 100% !important; margin-bottom: 12px !important; }
  .sidebar-overlay { display: block !important; }
  .admin-sidebar-mobile { display: block !important; position: fixed; top: 0; left: 0; width: 80vw; max-width: 320px; height: 100vh; background: #fff; z-index: 1050; box-shadow: 2px 0 12px rgba(0,0,0,0.15); transform: translateX(-100%); transition: transform 0.3s; }
  .admin-sidebar-mobile.open { transform: translateX(0); }
  .admin-sidebar-backdrop { display: block !important; position: fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.25); z-index:1049; }
  .admin-sidebar-desktop { display: none !important; }
  .admin-dashboard-growth { max-width: 100vw; }
}
@media (min-width: 901px) {
  .sidebar-overlay, .admin-sidebar-mobile, .admin-sidebar-backdrop { display: none !important; }
  .admin-sidebar-desktop { display: block !important; }
}
</style>
<script>
function toggleSidebar(open) {
  var sidebar = document.getElementById('adminSidebarMobile');
  var backdrop = document.getElementById('adminSidebarBackdrop');
  if (open) {
    sidebar.classList.add('open');
    backdrop.style.display = 'block';
  } else {
    sidebar.classList.remove('open');
    backdrop.style.display = 'none';
  }
}
</script>
<div class="admin-dashboard-flex d-flex" style="min-height:100vh; position:relative;">

  <button class="btn btn-outline-secondary d-md-none mb-3 mt-2" style="position:fixed; top:10px; left:10px; z-index:1100;" onclick="toggleSidebar(true)">
    <i class="fa fa-bars"></i> Menu
  </button>
  
  <div class="admin-sidebar-desktop" style="height:100vh;">
    <?php include 'adminSidebar.php'; ?>
  </div>

  <div id="adminSidebarMobile" class="admin-sidebar-mobile" style="display:none;">
    <div style="padding:10px 10px 0 10px; text-align:right;">
      <button class="btn btn-sm btn-outline-danger" onclick="toggleSidebar(false)"><i class="fa fa-times"></i></button>
    </div>
    <?php include 'adminSidebar.php'; ?>
  </div>
  <div id="adminSidebarBackdrop" class="admin-sidebar-backdrop" style="display:none;" onclick="toggleSidebar(false)"></div>
  <div class="admin-dashboard" style="flex:1; padding:30px; min-width:0;">
    <div class="admin-dashboard-banner mb-4">
      <span class="banner-icon"><i class="fa fa-chart-line"></i></span>
      <div>
        <h1>Welcome, Admin!</h1>
        <p>Monitor marketplace performance, user growth, and content at a glance. All analytics are live and up-to-date.</p>
      </div>
    </div>
    <h2 class="mb-4" style="font-family:'Montserrat',sans-serif; font-weight:700; color:#1a4a7a;">Admin Analytics Dashboard</h2>
    <div class="admin-dashboard-cards d-flex flex-wrap">
        <div class="admin-dashboard-card col-12 col-sm-6 col-md-3">
            <span class="icon" style="background:linear-gradient(135deg,#1a4a7a 60%,#2d7a2d 100%);"><i class="fa fa-users"></i></span>
            <div>
              <h6 class="mb-1">Total Users</h6>
              <div class="stat"> <?= $userCount ?> </div>
            </div>
        </div>
        <div class="admin-dashboard-card col-12 col-sm-6 col-md-3">
            <span class="icon" style="background:linear-gradient(135deg,#2d7a2d 60%,#1a4a7a 100%);"><i class="fa fa-shopping-cart"></i></span>
            <div>
              <h6 class="mb-1">Total Orders</h6>
              <div class="stat"> <?= $orderCount ?> </div>
            </div>
        </div>
        <div class="admin-dashboard-card col-12 col-sm-6 col-md-3">
            <span class="icon" style="background:linear-gradient(135deg,#f7b731 60%,#1a4a7a 100%);"><i class="fa fa-cube"></i></span>
            <div>
              <h6 class="mb-1">Best-Selling Product</h6>
              <div class="stat-secondary"> <?= htmlspecialchars($bestProduct) ?> </div>
            </div>
        </div>
        <div class="admin-dashboard-card col-12 col-sm-6 col-md-3">
            <span class="icon" style="background:linear-gradient(135deg,#e17055 60%,#1a4a7a 100%);"><i class="fa fa-book-open"></i></span>
            <div>
              <h6 class="mb-1">Top Story</h6>
              <div class="stat-secondary"> <?= htmlspecialchars($topStory) ?> </div>
            </div>
        </div>
    </div>
    <div class="admin-dashboard-growth mt-4">
        <h5 class="mb-3">User Growth (Last 6 Months)</h5>
        <svg class="growth-chart" viewBox="0 0 320 120" preserveAspectRatio="none">
        <?php
        $maxGrowth = 1;
        foreach($userGrowthData as $row) { if($row['count'] > $maxGrowth) $maxGrowth = $row['count']; }
        $barW = 36;
        $gap = 16;
        $n = count($userGrowthData);
        $chartW = $n * $barW + ($n-1)*$gap;
        $chartH = 100;
        $x = 0;
        foreach($userGrowthData as $i=>$row):
          $barH = $maxGrowth ? ($row['count']/$maxGrowth)*$chartH : 0;
          $y = $chartH - $barH;
          $color = "#1a4a7a";
        ?>
          <rect x="<?= $x ?>" y="<?= $y ?>" width="<?= $barW ?>" height="<?= $barH ?>" rx="7" fill="url(#barGrad)"/>
          <text x="<?= $x+$barW/2 ?>" y="<?= $chartH+16 ?>" text-anchor="middle" font-size="12" fill="#1a4a7a"><?= htmlspecialchars(substr($row['month'],2)) ?></text>
          <text x="<?= $x+$barW/2 ?>" y="<?= $y-6 ?>" text-anchor="middle" font-size="12" fill="#2d7a2d" font-weight="bold"><?= $row['count'] ?></text>
        <?php $x += $barW + $gap; endforeach; ?>
        <defs>
          <linearGradient id="barGrad" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="#2d7a2d"/>
            <stop offset="100%" stop-color="#1a4a7a"/>
          </linearGradient>
        </defs>
        </svg>
        <div class="table-responsive">
        <table class="table table-bordered table-sm" style="width:100%; border-collapse:collapse;">
            <tr style="background:#f0f0f0;"><th>Month</th><th>New Users</th></tr>
            <?php foreach($userGrowthData as $row): ?>
                <tr><td><?= htmlspecialchars($row['month']) ?></td><td><?= $row['count'] ?></td></tr>
            <?php endforeach; ?>
        </table>
        </div>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
