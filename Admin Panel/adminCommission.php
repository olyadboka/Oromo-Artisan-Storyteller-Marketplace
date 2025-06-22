<?php
include '../common/header.php';
?>
<style>
@media (max-width: 900px) {
  .admin-commission-flex { flex-direction: column !important; }
  .admin-commission-main { padding: 15px !important; }
}
</style>
<div class="admin-commission-flex d-flex" style="min-height:100vh;">
  <?php include 'adminSidebar.php'; ?>
  <div class="admin-commission-main" style="flex:1; padding:30px; min-width:0;">
    <?php
    include '../common/dbConnection.php';
    function getCommissionRate($conn) {
      $res = $conn->query("SELECT commission_rate FROM settings LIMIT 1");
      if ($row = $res->fetch_assoc()) return $row['commission_rate'];
      return 10;
    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['commission'])) {
      $rate = floatval($_POST['commission']);
      $conn->query("UPDATE settings SET commission_rate = $rate");
      echo '<div class="alert alert-success mt-3">Commission rate updated!</div>';
    }
    ?>
    <div class="my-5">
      <h1 class="mb-4 text-center">Commission Settings</h1>
      <div class="card">
        <div class="card-header bg-warning text-dark">Set Platform Commission Rate</div>
        <div class="card-body">
          <form method="post" class="row g-3">
            <div class="col-md-6">
              <label for="commission" class="form-label">Platform Commission Rate (%)</label>
              <input type="number" min="0" max="100" step="0.1" class="form-control" id="commission" name="commission" value="<?php echo getCommissionRate($conn); ?>">
            </div>
            <div class="col-md-6 d-flex align-items-end">
              <button type="submit" class="btn btn-primary">Update</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
