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
<!-- End Admin Dashboard Custom Header -->

<style>
@media (max-width: 900px) {
  .admin-commission-flex { flex-direction: column !important; }
  .admin-commission-main { padding: 15px !important; }
}
.admin-card-header {
  background: linear-gradient(120deg, #1a4a7a 60%, #2d7a2d 100%);
  color: #fff;
  font-weight: 700;
  font-size: 1.1em;
  border-radius: 10px 10px 0 0;
  padding: 14px 18px;
  letter-spacing: 0.01em;
}
.commission-rate-box {
  display: flex;
  align-items: center;
  gap: 18px;
  background: linear-gradient(120deg, #f5f5f5 60%, #eaf6f0 100%);
  border-radius: 14px;
  box-shadow: 0 2px 12px rgba(26,74,122,0.07);
  padding: 18px 24px;
  margin-bottom: 18px;
}
.commission-rate-box .icon {
  font-size: 2.2em;
  color: #fff;
  background: linear-gradient(135deg, #e6a100 60%, #1a4a7a 100%);
  border-radius: 50%;
  padding: 14px 16px;
  box-shadow: 0 2px 8px rgba(26,74,122,0.10);
  display: flex;
  align-items: center;
  justify-content: center;
}
.btn-update {
  background: #1a4a7a;
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 8px 22px;
  font-weight: 600;
  font-size: 1.08em;
  transition: background 0.2s;
}
.btn-update:disabled {
  background: #bfc9d1;
  color: #fff;
  opacity: 0.7;
}
.card {
  border-radius: 12px;
  box-shadow: 0 2px 12px rgba(26,74,122,0.07);
  border: none;
}
.card-header {
  border-bottom: 1px solid #e0e6ed;
}
.card-body {
  background: #f3f6fa;
  border-radius: 0 0 12px 12px;
}
.alert-admin-info {
  background: #eaf6f0;
  color: #1a4a7a;
  border-radius: 8px;
  padding: 10px 18px;
  font-size: 1em;
  font-weight: 500;
  border: 1px solid #b2d8c7;
}
</style>
<div class="admin-commission-flex d-flex" style="min-height:100vh;">
  <?php if (file_exists('adminSidebar.php')) { include 'adminSidebar.php'; } ?>
  <div class="admin-commission-main" style="flex:1; padding:30px; min-width:0;">
    <?php
    // Use a static commission rate (change here if needed)
    $currentCommissionRate = 10;
    ?>
    <div class="mt-2 mb-5">
      <h1 class="mb-3 text-center" style="margin-top:0.5em;">Commission Settings</h1>
      <div class="commission-rate-box mb-4">
        <span class="icon"><i class="fa fa-percent"></i></span>
        <div>
          <div style="font-size:1.1em; color:#e6a100;">Current Platform Commission Rate</div>
          <div style="font-size:2em; font-weight:bold; color:#2d7a2d;"> <?php echo htmlspecialchars($currentCommissionRate); ?>% </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header admin-card-header">Set Platform Commission Rate</div>
        <div class="card-body">
          <form class="row g-3">
            <div class="col-md-6">
              <label for="commission" class="form-label">Platform Commission Rate (%)</label>
              <input type="number" min="0" max="100" step="0.1" class="form-control" id="commission" name="commission" value="<?php echo htmlspecialchars($currentCommissionRate); ?>" disabled>
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <button type="button" class="btn btn-update" disabled>Update</button>
            </div>
          </form>
          <div class="alert alert-admin-info mt-3">Commission rate management is currently handled by the platform administrator.</div>
        </div>
      </div>
      <div class="mt-4 text-muted" style="font-size:0.98em;">
        <i class="fa fa-info-circle"></i> The commission rate determines the percentage the platform takes from each transaction. Set to 0 for no commission, or up to 100%.
      </div>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
