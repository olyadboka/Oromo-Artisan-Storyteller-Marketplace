<?php
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
?>
<?php include 'common/adminHeader.php'; ?>
<!-- Admin Dashboard Custom Header (Consistent with Main Site) -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
  .admin-header-nav {
    display: flex;
    gap: 24px;
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
  @media (max-width: 900px) {
    .admin-header-inner { flex-direction: column; height: auto; padding: 12px 8px; gap: 8px; }
    .admin-header-nav { flex-wrap: wrap; gap: 10px; justify-content: center; }
    .admin-header-logo-text { font-size: 1.1em; }
  }
</style>

<!-- End Admin Dashboard Custom Header -->

<!-- Add Bootstrap CSS/JS for tabs and responsive tables -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&display=swap');
@media (max-width: 900px) {
  .admin-users-flex { flex-direction: column !important; }
  .admin-users-content { padding: 15px !important; }
  .admin-users-banner { flex-direction: column !important; text-align: center; }
}
.admin-users-banner {
  position: relative;
  background: linear-gradient(90deg, #1a4a7a 0%, #2d7a2d 100%);
  color: #fff;
  border-radius: 16px;
  padding: 28px 24px 18px 24px;
  margin-bottom: 32px;
  box-shadow: 0 4px 24px rgba(26,74,122,0.10);
  display: flex;
  align-items: center;
  gap: 20px;
  flex-wrap: wrap;
}
.admin-users-banner::before {
  content: '';
  position: absolute;
  left: 0; top: 0; right: 0; bottom: 0;
  background: linear-gradient(90deg, rgba(0,0,0,0.38) 0%, rgba(0,0,0,0.22) 100%);
  border-radius: 16px;
  z-index: 1;
}
.admin-users-banner .banner-icon {
  font-size: 2.3em;
  background: rgba(255,255,255,0.13);
  border-radius: 50%;
  padding: 14px 18px;
  margin-right: 12px;
  color: #fff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.admin-users-banner h1 {
  font-family: 'Montserrat', sans-serif;
  font-size: 2.1em;
  font-weight: 800;
  margin: 0 0 4px 0;
  color: #fff !important;
  position: relative;
  z-index: 2;
  padding: 8px 18px;
  border-radius: 10px;
  background: rgba(0,0,0,0.18);
  text-shadow: 0 4px 18px rgba(0,0,0,0.45), 0 1px 0 #fff, 0 0 2px #1a4a7a;
  border: 1.5px solid rgba(255,255,255,0.18);
  letter-spacing: 0.01em;
}
.admin-users-banner p {
  font-size: 1.05em;
  margin: 0;
  opacity: 0.93;
}
.admin-users-content h1 {
  font-family: 'Montserrat', sans-serif;
  font-weight: 700;
  color: #1a4a7a;
  margin-bottom: 1.5rem;
}
.nav-tabs .nav-link {
  font-family: 'Montserrat', sans-serif;
  font-weight: 600;
  color: #1a4a7a;
  border-radius: 8px 8px 0 0;
  margin-right: 4px;
  background: #f5f5f5;
  border: none;
  transition: background 0.2s, color 0.2s;
}
.nav-tabs .nav-link.active {
  background: linear-gradient(90deg, #1a4a7a 0%, #2d7a2d 100%);
  color: #fff;
  box-shadow: 0 2px 8px rgba(26,74,122,0.10);
}
.card {
  border-radius: 14px;
  box-shadow: 0 2px 12px rgba(26,74,122,0.07);
  border: none;
  margin-bottom: 18px;
}
.card-body {
  border-radius: 14px;
}
.table {
  border-radius: 10px;
  overflow: hidden;
  background: #fff;
}
.table thead th {
  background: #eaf6f0;
  color: #1a4a7a;
  font-family: 'Montserrat', sans-serif;
  font-weight: 600;
  border-bottom: 2px solid #d0e2db;
}
.table-bordered > :not(caption) > * > * {
  border-color: #d0e2db;
}
.table tbody tr {
  transition: background 0.15s;
}
.table tbody tr:hover {
  background: #f0f7fa;
}
.status-badge {
  display: inline-block;
  padding: 3px 12px;
  border-radius: 12px;
  font-size: 0.98em;
  font-weight: 600;
  color: #fff;
}
.status-verified { background: #2d7a2d; }
.status-rejected { background: #e17055; }
.status-pending { background: #1a4a7a; }
.btn-action {
  font-family: 'Montserrat', sans-serif;
  font-weight: 600;
  border-radius: 8px;
  margin-right: 4px;
  min-width: 90px;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}
.btn-action-approve {
  background: linear-gradient(90deg, #2d7a2d 60%, #1a4a7a 100%);
  color: #fff;
  border: none;
}
.btn-action-approve:hover { background: #2d7a2d; color: #fff; }
.btn-action-suspend {
  background: linear-gradient(90deg, #e17055 60%, #1a4a7a 100%);
  color: #fff;
  border: none;
}
.btn-action-suspend:hover { background: #e17055; color: #fff; }
</style>
<div class="admin-users-flex d-flex" style="min-height:100vh;">
  <?php if (file_exists('adminSidebar.php')) { include 'adminSidebar.php'; } ?>
  <div class="admin-users-content" style="flex:1; padding:30px; min-width:0;">
    <div class="admin-users-banner mb-4">
      <span class="banner-icon"><i class="fa fa-user-shield"></i></span>
      <div>
        <h1>User Verification & Moderation</h1>
        <p>Approve, suspend, and manage artisans and storytellers. All actions are live and instantly reflected.</p>
      </div>
    </div>
    <?php
    // Connect to the database
    include '../common/dbConnection.php';
    // Approve or suspend logic for artisans and storytellers
    if (isset($_GET['verify_artisan'])) {
      $artisanId = intval($_GET['verify_artisan']);
      $updateArtisan = $conn->query("UPDATE artisans SET verification_status='verified' WHERE id=" . $artisanId);
      header('Location: adminUsers.php');
      exit;
    }
    if (isset($_GET['reject_artisan'])) {
      $artisanId = intval($_GET['reject_artisan']);
      $updateArtisan = $conn->query("UPDATE artisans SET verification_status='rejected' WHERE id=" . $artisanId);
      header('Location: adminUsers.php');
      exit;
    }
    if (isset($_GET['verify_storyteller'])) {
      $storytellerId = intval($_GET['verify_storyteller']);
      $updateStoryteller = $conn->query("UPDATE storytellers SET verification_status='verified' WHERE id=" . $storytellerId);
      header('Location: adminUsers.php');
      exit;
    }
    if (isset($_GET['reject_storyteller'])) {
      $storytellerId = intval($_GET['reject_storyteller']);
      $updateStoryteller = $conn->query("UPDATE storytellers SET verification_status='rejected' WHERE id=" . $storytellerId);
      header('Location: adminUsers.php');
      exit;
    }
    ?>
    <div class="container my-5">
      <ul class="nav nav-tabs" id="adminTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="artisans-tab" data-bs-toggle="tab" data-bs-target="#artisans" type="button" role="tab" aria-controls="artisans" aria-selected="true">Artisans</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="storytellers-tab" data-bs-toggle="tab" data-bs-target="#storytellers" type="button" role="tab" aria-controls="storytellers" aria-selected="false">Storytellers</button>
        </li>
      </ul>
      <div class="tab-content mt-3" id="adminTabContent">
        <div class="tab-pane fade show active" id="artisans" role="tabpanel" aria-labelledby="artisans-tab">
          <div class="card">
            <div class="card-body table-responsive">
              <?php
              // Get all artisans and their usernames
              $getArtisans = $conn->query("SELECT a.*, u.username FROM artisans a JOIN users u ON a.user_id = u.id");
              echo '<table class="table table-bordered table-hover table-sm align-middle mb-0"><thead><tr><th>Name</th><th>Business</th><th>Status</th><th>Action</th></tr></thead><tbody>';
              while ($row = $getArtisans->fetch_assoc()) {
                $status = $row['verification_status'];
                if ($status === 'verified') {
                  $badgeClass = 'status-badge status-verified';
                } else if ($status === 'rejected') {
                  $badgeClass = 'status-badge status-rejected';
                } else {
                  $badgeClass = 'status-badge status-pending';
                }
                echo '<tr><td>' . htmlspecialchars($row['username']) . '</td><td>' . htmlspecialchars($row['business_name']) . '</td><td><span class="' . $badgeClass . '">' . ucfirst($status) . '</span></td>';
                echo '<td>';
                if ($row['verification_status'] !== 'verified') {
                  echo '<a href="?verify_artisan=' . $row['id'] . '" class="btn btn-action btn-action-approve btn-sm"><i class="fa fa-check"></i> Approve</a> ';
                }
                if ($row['verification_status'] !== 'rejected') {
                  echo '<a href="?reject_artisan=' . $row['id'] . '" class="btn btn-action btn-action-suspend btn-sm"><i class="fa fa-ban"></i> Suspend</a>';
                }
                echo '</td></tr>';
              }
              echo '</tbody></table>';
              ?>
            </div>
          </div>
        </div>
        <div class="tab-pane fade" id="storytellers" role="tabpanel" aria-labelledby="storytellers-tab">
          <div class="card">
            <div class="card-body table-responsive">
              <?php
              // Get all storytellers and their usernames
              $getStorytellers = $conn->query("SELECT s.*, u.username FROM storytellers s JOIN users u ON s.user_id = u.id");
              echo '<table class="table table-bordered table-hover table-sm align-middle mb-0"><thead><tr><th>Name</th><th>Specialization</th><th>Status</th><th>Action</th></tr></thead><tbody>';
              while ($row = $getStorytellers->fetch_assoc()) {
                $status = $row['verification_status'];
                if ($status === 'verified') {
                  $badgeClass = 'status-badge status-verified';
                } else if ($status === 'rejected') {
                  $badgeClass = 'status-badge status-rejected';
                } else {
                  $badgeClass = 'status-badge status-pending';
                }
                echo '<tr><td>' . htmlspecialchars($row['username']) . '</td><td>' . htmlspecialchars($row['specialization']) . '</td><td><span class="' . $badgeClass . '">' . ucfirst($status) . '</span></td>';
                echo '<td>';
                if ($row['verification_status'] !== 'verified') {
                  echo '<a href="?verify_storyteller=' . $row['id'] . '" class="btn btn-action btn-action-approve btn-sm"><i class="fa fa-check"></i> Approve</a> ';
                }
                if ($row['verification_status'] !== 'rejected') {
                  echo '<a href="?reject_storyteller=' . $row['id'] . '" class="btn btn-action btn-action-suspend btn-sm"><i class="fa fa-ban"></i> Suspend</a>';
                }
                echo '</td></tr>';
              }
              echo '</tbody></table>';
              ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
// Ensure correct tab is shown on reload (if using hash)
document.addEventListener('DOMContentLoaded', function() {
  var hash = window.location.hash;
  if (hash) {
    var tabTrigger = document.querySelector('button[data-bs-target="' + hash + '"]');
    if (tabTrigger) {
      var tab = new bootstrap.Tab(tabTrigger);
      tab.show();
    }
  }
});
</script>
<?php include '../common/footer.php'; ?>
