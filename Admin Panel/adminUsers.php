<?php
// This page is for user verification and moderation in the admin panel
include '../common/header.php';
?>
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
  <?php include 'adminSidebar.php'; ?>
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
          <button class="nav-link active" id="artisans-tab" data-bs-toggle="tab" data-bs-target="#artisans" type="button" role="tab">Artisans</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="storytellers-tab" data-bs-toggle="tab" data-bs-target="#storytellers" type="button" role="tab">Storytellers</button>
        </li>
      </ul>
      <div class="tab-content mt-3" id="adminTabContent">
        <div class="tab-pane fade show active" id="artisans" role="tabpanel">
          <div class="card">
            <div class="card-body table-responsive">
              <?php
              // Get all artisans and their usernames
              $getArtisans = $conn->query("SELECT a.*, u.username FROM artisans a JOIN users u ON a.user_id = u.id");
              echo '<table class="table table-bordered table-sm"><thead><tr><th>Name</th><th>Business</th><th>Status</th><th>Action</th></tr></thead><tbody>';
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
        <div class="tab-pane fade" id="storytellers" role="tabpanel">
          <div class="card">
            <div class="card-body table-responsive">
              <?php
              // Get all storytellers and their usernames
              $getStorytellers = $conn->query("SELECT s.*, u.username FROM storytellers s JOIN users u ON s.user_id = u.id");
              echo '<table class="table table-bordered table-sm"><thead><tr><th>Name</th><th>Specialization</th><th>Status</th><th>Action</th></tr></thead><tbody>';
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
<?php include '../common/footer.php'; ?>
