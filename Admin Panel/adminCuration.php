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
<?php
// Include the modular admin header
include 'common/adminHeader.php';
?>
<!-- End Admin Dashboard Custom Header -->

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
    // Connect to the database
    include '../common/dbConnection.php';
    // Handle homepage curation form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['featured_artisan'], $_POST['featured_product'], $_POST['featured_story'])) {
      // Unfeature all, then feature selected
      $resetArtisans = $conn->query("UPDATE artisans SET is_featured=0");
      $featureArtisan = $conn->query("UPDATE artisans SET is_featured=1 WHERE id=" . intval($_POST['featured_artisan']));
      $resetProducts = $conn->query("UPDATE products SET is_featured=0");
      $featureProduct = $conn->query("UPDATE products SET is_featured=1 WHERE id=" . intval($_POST['featured_product']));
      $resetStories = $conn->query("UPDATE stories SET is_featured=0");
      $featureStory = $conn->query("UPDATE stories SET is_featured=1 WHERE id=" . intval($_POST['featured_story']));
      echo '<div class="alert alert-success mt-3">Homepage curation updated!</div>';
    }
    ?>
    <div class="mt-2 mb-5" style="margin-top:0.5em;">
      <h1 class="mb-4 text-center">Homepage Curation</h1>
      <div class="card">
        <div class="card-header bg-success text-white">Feature Artisans, Products, Stories</div>
        <div class="card-body">
          <form method="post">
            <div class="mb-3">
              <label for="featured_artisan" class="form-label">Feature Artisan</label>
              <select class="form-select" id="featured_artisan" name="featured_artisan">
                <?php
                // Get all verified artisans
                $getArtisans = $conn->query("SELECT id, business_name FROM artisans WHERE verification_status='verified'");
                while ($row = $getArtisans->fetch_assoc()) {
                  echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['business_name']) . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="featured_product" class="form-label">Feature Product</label>
              <select class="form-select" id="featured_product" name="featured_product">
                <?php
                // Get all products
                $getProducts = $conn->query("SELECT id, name FROM products");
                while ($row = $getProducts->fetch_assoc()) {
                  echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['name']) . '</option>';
                }
                ?>
              </select>
            </div>
            <div class="mb-3">
              <label for="featured_story" class="form-label">Feature Story</label>
              <select class="form-select" id="featured_story" name="featured_story">
                <?php
                // Get all stories
                $getStories = $conn->query("SELECT id, title FROM stories");
                while ($row = $getStories->fetch_assoc()) {
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
