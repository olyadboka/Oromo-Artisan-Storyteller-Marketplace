<?php
// This page is for commission settings in the admin panel
include '../common/header.php';
?>
<style>
@media (max-width: 900px) {
  .admin-commission-flex { flex-direction: column !important; }
  .admin-commission-main { padding: 15px !important; }
}
.commission-rate-box {
  background: #f5f5f5;
  border-radius: 10px;
  padding: 18px 16px;
  margin-bottom: 24px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.04);
  display: flex;
  align-items: center;
  gap: 18px;
}
.commission-rate-box .icon {
  font-size: 2.2em;
  color: #e6a100;
  flex-shrink: 0;
}
</style>
<div class="admin-commission-flex d-flex" style="min-height:100vh;">
  <?php include 'adminSidebar.php'; ?>
  <div class="admin-commission-main" style="flex:1; padding:30px; min-width:0;">
    <?php
    // Use a static commission rate (change here if needed)
    $currentCommissionRate = 10;
    ?>
    <div class="my-5">
      <h1 class="mb-4 text-center">Commission Settings</h1>
      <div class="commission-rate-box mb-4">
        <span class="icon"><i class="fa fa-percent"></i></span>
        <div>
          <div style="font-size:1.1em; color:#e6a100;">Current Platform Commission Rate</div>
          <div style="font-size:2em; font-weight:bold; color:#2d7a2d;"> <?php echo htmlspecialchars($currentCommissionRate); ?>% </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header bg-warning text-dark">Set Platform Commission Rate</div>
        <div class="card-body">
          <form class="row g-3">
            <div class="col-md-6">
              <label for="commission" class="form-label">Platform Commission Rate (%)</label>
              <input type="number" min="0" max="100" step="0.1" class="form-control" id="commission" name="commission" value="<?php echo htmlspecialchars($currentCommissionRate); ?>" disabled>
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <button type="button" class="btn btn-primary" disabled>Update</button>
            </div>
          </form>
          <div class="alert alert-info mt-3">Commission rate management is currently handled by the platform administrator.</div>
        </div>
      </div>
      <div class="mt-4 text-muted" style="font-size:0.98em;">
        <i class="fa fa-info-circle"></i> The commission rate determines the percentage the platform takes from each transaction. Set to 0 for no commission, or up to 100%.
      </div>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
