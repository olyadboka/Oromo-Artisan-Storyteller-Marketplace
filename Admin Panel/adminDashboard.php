<?php
// --- Admin session and role check ---
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php?error=not_logged_in');
    exit();
}

// Ensure database connection is available as $conn
if (!isset($conn)) {
    if (file_exists('../common/dbConnection.php')) {
        include_once '../common/dbConnection.php';
    } elseif (file_exists(__DIR__ . '/../common/dbConnection.php')) {
        include_once __DIR__ . '/../common/dbConnection.php';
    }
    if (!isset($conn)) {
        die('Database connection not established.');
    }
}

// Check user role (must be admin)
$userId = intval($_SESSION['user_id']);
$userResult = $conn->query("SELECT role FROM users WHERE id = $userId LIMIT 1");
if (!$userResult || $userResult->num_rows === 0) {
    session_destroy();
    header('Location: ../index.php?error=invalid_user');
    exit();
}
$userRow = $userResult->fetch_assoc();
if (strtolower($userRow['role']) !== 'admin') {
    header('Location: ../index.php?error=not_authorized');
    exit();
}

// --- Analytics Queries ---
// Get total users
$userCountResult = $conn->query("SELECT COUNT(*) as cnt FROM users");
if ($userCountResult) {
    $userCountRow = $userCountResult->fetch_assoc();
    $userCount = $userCountRow['cnt'];
} else {
    $userCount = 0;
}
// Get total orders
$orderCountResult = $conn->query("SELECT COUNT(*) as cnt FROM orders");
if ($orderCountResult) {
    $orderCountRow = $orderCountResult->fetch_assoc();
    $orderCount = $orderCountRow['cnt'];
} else {
    $orderCount = 0;
}
// Get best selling product
$bestProductResult = $conn->query("SELECT p.name, SUM(oi.quantity) as sold FROM order_items oi JOIN products p ON oi.item_id=p.id WHERE oi.type='product' GROUP BY oi.item_id ORDER BY sold DESC LIMIT 1");
if ($bestProductResult && $bestProductResult->num_rows > 0) {
    $bestProductRow = $bestProductResult->fetch_assoc();
    $bestProduct = $bestProductRow['name'];
} else {
    $bestProduct = '-';
}
// Get top story
$topStoryResult = $conn->query("SELECT s.title, SUM(oi.quantity) as sold FROM order_items oi JOIN stories s ON oi.item_id=s.id WHERE oi.type='story' GROUP BY oi.item_id ORDER BY sold DESC LIMIT 1");
if ($topStoryResult && $topStoryResult->num_rows > 0) {
    $topStoryRow = $topStoryResult->fetch_assoc();
    $topStory = $topStoryRow['title'];
} else {
    $topStory = '-';
}
// User growth by month (last 6 months)
$userGrowthResult = $conn->query("SELECT DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count FROM users GROUP BY month ORDER BY month DESC LIMIT 6");
$userGrowthData = array();
if ($userGrowthResult) {
    while ($row = $userGrowthResult->fetch_assoc()) {
        $userGrowthData[] = $row;
    }
}
$userGrowthData = array_reverse($userGrowthData);
?>
<?php include 'common/adminHeader.php'; ?>
<style>
  .dashboard-section {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 2px 12px rgba(26,74,122,0.07);
    padding: 32px 24px 24px 24px;
    margin-bottom: 32px;
  }
  .dashboard-cards {
    display: flex;
    flex-wrap: wrap;
    gap: 24px;
    margin-bottom: 32px;
  }
  .dashboard-card {
    flex: 1 1 220px;
    min-width: 220px;
    background: linear-gradient(120deg, #f5f5f5 60%, #eaf6f0 100%);
    border-radius: 14px;
    box-shadow: 0 2px 12px rgba(26,74,122,0.07);
    padding: 24px 18px;
    display: flex;
    flex-direction: column;
    align-items: center;
    transition: box-shadow 0.2s, transform 0.2s;
    position: relative;
    overflow: hidden;
  }
  .dashboard-card:hover {
    box-shadow: 0 6px 24px rgba(26,74,122,0.13);
    transform: translateY(-2px) scale(1.03);
    z-index: 2;
  }
  .dashboard-card .icon {
    font-size: 2.2em;
    color: #fff;
    background: linear-gradient(135deg, #1a4a7a 60%, #2d7a2d 100%);
    border-radius: 50%;
    padding: 14px 16px;
    margin-bottom: 10px;
    box-shadow: 0 2px 8px rgba(26,74,122,0.10);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .dashboard-card h6 {
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
    margin-bottom: 2px;
    color: #1a4a7a;
    letter-spacing: 0.01em;
  }
  .dashboard-card .stat {
    font-size: 1.7em;
    color: #2d7a2d;
    font-weight: bold;
  }
  .dashboard-card .stat-secondary {
    font-size: 1.1em;
    color: #1a4a7a;
    font-weight: 600;
  }
  .dashboard-growth {
    background: #fff;
    padding: 24px 20px 18px 20px;
    border-radius: 12px;
    max-width: 600px;
    overflow-x: auto;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    margin-top: 18px;
  }
  .dashboard-growth h5 {
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    margin-bottom: 18px;
    color: #1a4a7a;
  }
  .dashboard-growth .growth-chart {
    width: 100%;
    min-width: 320px;
    height: 120px;
    margin-bottom: 10px;
    display: block;
  }
  .growth-chart-bar {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    height: 140px;
    width: 100%;
    gap: 18px;
    margin-bottom: 8px;
    padding: 0 8px;
    background: repeating-linear-gradient(
      to top,
      #f3f6fa 0px,
      #f3f6fa 1px,
      transparent 1px,
      transparent 28px
    );
    border-radius: 10px;
    border: 1px solid #e0e6ed;
    position: relative;
  }
  .growth-bar {
    flex: 1 1 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-end;
    min-width: 32px;
    position: relative;
  }
  .growth-bar-rect {
    width: 100%;
    min-width: 22px;
    border-radius: 8px 8px 0 0;
    box-shadow: 0 2px 8px rgba(26,74,122,0.13);
    border: 2.5px solid #fff;
    transition: height 0.3s, background 0.2s;
    display: flex;
    align-items: flex-end;
    justify-content: center;
    position: relative;
    cursor: pointer;
  }
  .growth-bar-rect[data-color="1"] { background: #1cb933; }
  .growth-bar-rect[data-color="2"] { background: #f7b731; }
  .growth-bar-rect[data-color="3"] { background: #e17055; }
  .growth-bar-rect[data-color="4"] { background: #1a4a7a; }
  .growth-bar-rect[data-color="5"] { background: #2d7a2d; }
  .growth-bar-rect[data-color="6"] { background: #6c47a3; }
  .growth-bar-label {
    font-size: 1em;
    color: #1a4a7a;
    margin-top: 8px;
    font-family: 'Montserrat', sans-serif;
    font-weight: 700;
    text-align: center;
    word-break: break-all;
    letter-spacing: 0.01em;
  }
  .growth-bar-value {
    position: absolute;
    top: -30px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 1.08em;
    color: #fff;
    font-weight: bold;
    background: #1a4a7a;
    border-radius: 8px;
    padding: 3px 10px 2px 10px;
    box-shadow: 0 2px 8px rgba(26,74,122,0.13);
    opacity: 0.97;
    pointer-events: none;
    border: 2px solid #fff;
    z-index: 2;
    letter-spacing: 0.01em;
  }
  .growth-bar-rect[data-color="2"] .growth-bar-value { background: #f7b731; color: #1a4a7a; }
  .growth-bar-rect[data-color="3"] .growth-bar-value { background: #e17055; color: #fff; }
  .growth-bar-rect[data-color="4"] .growth-bar-value { background: #1a4a7a; color: #fff; }
  .growth-bar-rect[data-color="5"] .growth-bar-value { background: #2d7a2d; color: #fff; }
  .growth-bar-rect[data-color="6"] .growth-bar-value { background: #6c47a3; color: #fff; }
  .growth-bar-rect[data-color="1"] .growth-bar-value { background: #1cb933; color: #fff; }
  .growth-bar-rect:hover, .growth-bar-rect:focus {
    filter: brightness(1.08) drop-shadow(0 2px 8px #1a4a7a22);
    outline: 2px solid #f7b731;
    z-index: 3;
  }
  .growth-bar-label {
    margin-top: 10px;
    font-size: 1em;
    font-weight: 700;
    color: #1a4a7a;
    background: #f3f6fa;
    border-radius: 6px;
    padding: 2px 7px;
    box-shadow: 0 1px 4px rgba(26,74,122,0.07);
    display: inline-block;
    min-width: 32px;
  }
  .dashboard-growth .growth-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 8px;
    margin-top: 10px;
  }
  .dashboard-growth .growth-table th, .dashboard-growth .growth-table td {
    padding: 10px 16px;
    font-size: 1.08em;
    font-family: 'Montserrat', sans-serif;
    text-align: center;
    background: #f3f6fa;
    border: none;
    border-radius: 8px;
  }
  .dashboard-growth .growth-table th {
    background: #1a4a7a;
    color: #fff;
    font-weight: 700;
    letter-spacing: 0.01em;
  }
  .dashboard-growth .growth-table td.month-badge {
    background: #f7b731;
    color: #1a4a7a;
    font-weight: 700;
    border-radius: 8px 0 0 8px;
    letter-spacing: 0.01em;
  }
  .dashboard-growth .growth-table td.user-badge {
    background: #1cb933;
    color: #fff;
    font-weight: 700;
    border-radius: 0 8px 8px 0;
    letter-spacing: 0.01em;
  }
  @media (max-width: 900px) {
    .admin-header-inner { flex-direction: column; height: auto; padding: 12px 8px; gap: 8px; }
    .admin-header-nav { flex-wrap: wrap; gap: 10px; justify-content: center; }
    .admin-header-logo-text { font-size: 1.1em; }
    .dashboard-cards { flex-direction: column; gap: 15px; }
    .dashboard-card { min-width: 0 !important; width: 100% !important; margin-bottom: 12px !important; }
    .dashboard-growth { max-width: 100vw; }
  }
  @media (max-width: 600px) {
    .growth-chart-bar { gap: 6px; padding: 0 2px; }
    .growth-bar { min-width: 18px; }
    .growth-bar-label { font-size: 0.82em; }
    .growth-bar-value { font-size: 0.85em; }
  }
</style>

<div class="dashboard-section">
  <div class="dashboard-cards">
    <div class="dashboard-card">
      <span class="icon"><i class="fa fa-users"></i></span>
      <h6>Total Users</h6>
      <div class="stat"> <?php echo $userCount; ?> </div>
    </div>
    <div class="dashboard-card">
      <span class="icon" style="background:linear-gradient(135deg,#2d7a2d 60%,#1a4a7a 100%);"><i class="fa fa-shopping-cart"></i></span>
      <h6>Total Orders</h6>
      <div class="stat"> <?php echo $orderCount; ?> </div>
    </div>
    <div class="dashboard-card">
      <span class="icon" style="background:linear-gradient(135deg,#f7b731 60%,#1a4a7a 100%);"><i class="fa fa-cube"></i></span>
      <h6>Best-Selling Product</h6>
      <div class="stat-secondary"> <?php echo htmlspecialchars($bestProduct); ?> </div>
    </div>
    <div class="dashboard-card">
      <span class="icon" style="background:linear-gradient(135deg,#e17055 60%,#1a4a7a 100%);"><i class="fa fa-book-open"></i></span>
      <h6>Top Story</h6>
      <div class="stat-secondary"> <?php echo htmlspecialchars($topStory); ?> </div>
    </div>
  </div>
  <div class="dashboard-growth mt-4">
    <h5>User Growth (Last 6 Months)</h5>
    <div class="growth-chart-bar">
      <?php
        // Find max value for scaling
        $maxGrowth = 0;
        foreach($userGrowthData as $row) {
          if ($row['count'] > $maxGrowth) $maxGrowth = $row['count'];
        }
        $barColors = [1,2,3,4,5,6];
        $i = 0;
        foreach($userGrowthData as $row) {
          $height = $maxGrowth > 0 ? round(($row['count']/$maxGrowth)*110) : 0;
          $colorIdx = $barColors[$i % count($barColors)];
          echo '<div class="growth-bar">';
          echo '<div class="growth-bar-rect" tabindex="0" data-color="'.$colorIdx.'" style="height:'.$height.'px;">';
          echo '<span class="growth-bar-value">'.intval($row['count']).'</span>';
          echo '</div>';
          $monthLabel = date('M', strtotime($row['month'].'-01'));
          echo '<div class="growth-bar-label">'.$monthLabel.'</div>';
          echo '</div>';
          $i++;
        }
      ?>
    </div>
    <div class="table-responsive">
      <table class="growth-table">
        <thead>
          <tr>
            <th>Month</th>
            <th>New Users</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $i = 0;
          foreach($userGrowthData as $row) {
            $monthLabel = date('M Y', strtotime($row['month'].'-01'));
            echo '<tr>';
            echo '<td class="month-badge">'.$monthLabel.'</td>';
            echo '<td class="user-badge">'.intval($row['count']).'</td>';
            echo '</tr>';
            $i++;
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
