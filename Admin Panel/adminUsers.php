<?php
include '../common/header.php';
?>
<style>
@media (max-width: 900px) {
  .admin-users-flex { flex-direction: column !important; }
  .admin-users-content { padding: 15px !important; }
}
</style>
<div class="admin-users-flex d-flex" style="min-height:100vh;">
  <?php include 'adminSidebar.php'; ?>
  <div class="admin-users-content" style="flex:1; padding:30px; min-width:0;">
    <?php
    include '../common/dbConnection.php';
    // Approve/suspend logic
    if (isset($_GET['verify_artisan'])) {
      $id = intval($_GET['verify_artisan']);
      $conn->query("UPDATE artisans SET verification_status='verified' WHERE id=$id");
      header('Location: adminUsers.php'); exit;
    }
    if (isset($_GET['reject_artisan'])) {
      $id = intval($_GET['reject_artisan']);
      $conn->query("UPDATE artisans SET verification_status='rejected' WHERE id=$id");
      header('Location: adminUsers.php'); exit;
    }
    if (isset($_GET['verify_storyteller'])) {
      $id = intval($_GET['verify_storyteller']);
      $conn->query("UPDATE storytellers SET verification_status='verified' WHERE id=$id");
      header('Location: adminUsers.php'); exit;
    }
    if (isset($_GET['reject_storyteller'])) {
      $id = intval($_GET['reject_storyteller']);
      $conn->query("UPDATE storytellers SET verification_status='rejected' WHERE id=$id");
      header('Location: adminUsers.php'); exit;
    }
    ?>
    <div class="container my-5">
      <h1 class="mb-4 text-center">User Verification & Moderation</h1>
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
              $res = $conn->query("SELECT a.*, u.username FROM artisans a JOIN users u ON a.user_id = u.id");
              echo '<table class="table table-bordered table-sm"><thead><tr><th>Name</th><th>Business</th><th>Status</th><th>Action</th></tr></thead><tbody>';
              while ($row = $res->fetch_assoc()) {
                echo '<tr><td>' . htmlspecialchars($row['username']) . '</td><td>' . htmlspecialchars($row['business_name']) . '</td><td>' . $row['verification_status'] . '</td>';
                echo '<td>';
                if ($row['verification_status'] !== 'verified') {
                  echo '<a href="?verify_artisan=' . $row['id'] . '" class="btn btn-success btn-sm">Approve</a> ';
                }
                if ($row['verification_status'] !== 'rejected') {
                  echo '<a href="?reject_artisan=' . $row['id'] . '" class="btn btn-danger btn-sm">Suspend</a>';
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
              $res = $conn->query("SELECT s.*, u.username FROM storytellers s JOIN users u ON s.user_id = u.id");
              echo '<table class="table table-bordered table-sm"><thead><tr><th>Name</th><th>Specialization</th><th>Status</th><th>Action</th></tr></thead><tbody>';
              while ($row = $res->fetch_assoc()) {
                echo '<tr><td>' . htmlspecialchars($row['username']) . '</td><td>' . htmlspecialchars($row['specialization']) . '</td><td>' . $row['verification_status'] . '</td>';
                echo '<td>';
                if ($row['verification_status'] !== 'verified') {
                  echo '<a href="?verify_storyteller=' . $row['id'] . '" class="btn btn-success btn-sm">Approve</a> ';
                }
                if ($row['verification_status'] !== 'rejected') {
                  echo '<a href="?reject_storyteller=' . $row['id'] . '" class="btn btn-danger btn-sm">Suspend</a>';
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
