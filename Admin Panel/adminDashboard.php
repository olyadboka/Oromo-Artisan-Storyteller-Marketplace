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
}
@media (min-width: 901px) {
  .sidebar-overlay, .admin-sidebar-mobile, .admin-sidebar-backdrop { display: none !important; }
  .admin-sidebar-desktop { display: block !important; }
}
.admin-dashboard-card {
  background: #f5f5f5;
  padding: 18px 16px;
  border-radius: 10px;
  min-width: 180px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  margin-bottom: 0;
  display: flex;
  align-items: center;
  gap: 16px;
}
.admin-dashboard-card .icon {
  font-size: 2.2em;
  color: #1a4a7a;
  flex-shrink: 0;
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
    <h2 class="mb-4">Admin Analytics Dashboard</h2>
    <div class="admin-dashboard-cards d-flex flex-wrap" style="gap:30px; margin-bottom:30px;">
        <div class="admin-dashboard-card col-12 col-sm-6 col-md-3">
            <span class="icon"><i class="fa fa-users"></i></span>
            <div>
              <h6 class="mb-1">Total Users</h6>
              <div style="font-size:1.7em; color:#2d7a2d; font-weight:bold;"> <?= $userCount ?> </div>
            </div>
        </div>
        <div class="admin-dashboard-card col-12 col-sm-6 col-md-3">
            <span class="icon"><i class="fa fa-shopping-cart"></i></span>
            <div>
              <h6 class="mb-1">Total Orders</h6>
              <div style="font-size:1.7em; color:#2d7a2d; font-weight:bold;"> <?= $orderCount ?> </div>
            </div>
        </div>
        <div class="admin-dashboard-card col-12 col-sm-6 col-md-3">
            <span class="icon"><i class="fa fa-cube"></i></span>
            <div>
              <h6 class="mb-1">Best-Selling Product</h6>
              <div style="font-size:1.1em; color:#1a4a7a;"> <?= htmlspecialchars($bestProduct) ?> </div>
            </div>
        </div>
        <div class="admin-dashboard-card col-12 col-sm-6 col-md-3">
            <span class="icon"><i class="fa fa-book-open"></i></span>
            <div>
              <h6 class="mb-1">Top Story</h6>
              <div style="font-size:1.1em; color:#1a4a7a;"> <?= htmlspecialchars($topStory) ?> </div>
            </div>
        </div>
    </div>
    <div style="background:#fff; padding:20px; border-radius:8px; max-width:500px; overflow-x:auto; box-shadow:0 2px 8px rgba(0,0,0,0.04);">
        <h5 class="mb-3">User Growth (Last 6 Months)</h5>
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
