<?php
include '../common/header.php';
?>
<style>
@media (max-width: 900px) {
  .admin-curation-flex { flex-direction: column !important; }
  .admin-curation-main { padding: 15px !important; }
}
</style>
<div class="admin-curation-flex d-flex" style="min-height:100vh;">
  <?php include 'adminSidebar.php'; ?>
  <div class="admin-curation-main" style="flex:1; padding:30px; min-width:0;">
    <?php
    include '../common/dbConnection.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['featured_artisan'], $_POST['featured_product'], $_POST['featured_story'])) {
      $conn->query("UPDATE artisans SET is_featured=0");
      $conn->query("UPDATE artisans SET is_featured=1 WHERE id=".intval($_POST['featured_artisan']));
      $conn->query("UPDATE products SET is_featured=0");
      $conn->query("UPDATE products SET is_featured=1 WHERE id=".intval($_POST['featured_product']));
      $conn->query("UPDATE stories SET is_featured=0");
      $conn->query("UPDATE stories SET is_featured=1 WHERE id=".intval($_POST['featured_story']));
      echo '<div class="alert alert-success mt-3">Homepage curation updated!</div>';
    }
    ?>
    <div class="my-5">
      <h1 class="mb-4 text-center">Homepage Curation</h1>
      <div class="card">
        <div class="card-header bg-success text-white">Feature Artisans, Products, Stories</div>
        <div class="card-body">
          <form method="post">
            <div class="mb-3">
              <label for="featured_artisan" class="form-label">Feature Artisan</label>
              <select class="form-select" id="featured_artisan" name="featured_artisan">
                <?php
                $res = $conn->query("SELECT id, business_name FROM artisans WHERE verification_status='verified'");
                while ($row = $res->fetch_assoc()) {
                  echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['business_name']) . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="featured_product" class="form-label">Feature Product</label>
              <select class="form-select" id="featured_product" name="featured_product">
                <?php
                $res = $conn->query("SELECT id, name FROM products");
                while ($row = $res->fetch_assoc()) {
                  echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="featured_story" class="form-label">Feature Story</label>
              <select class="form-select" id="featured_story" name="featured_story">
                <?php
                $res = $conn->query("SELECT id, title FROM stories");
                while ($row = $res->fetch_assoc()) {
                  echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['title']) . '</option>';
                }
                ?>
              </select>
            </div>
            <button type="submit" class="btn btn-success">Update Homepage</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
