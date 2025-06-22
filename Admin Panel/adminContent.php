<?php
include '../common/header.php';
?>
<style>
@media (max-width: 900px) {
  .admin-content-flex { flex-direction: column !important; }
  .admin-content-main { padding: 15px !important; }
}
</style>
<div class="admin-content-flex d-flex" style="min-height:100vh;">
  <?php include 'adminSidebar.php'; ?>
  <div class="admin-content-main" style="flex:1; padding:30px; min-width:0;">
    <?php
    include '../common/dbConnection.php';
    // Handle flag/unflag actions
    if (isset($_GET['flag_product'])) {
      $id = intval($_GET['flag_product']);
      $conn->query("UPDATE products SET is_flagged=1 WHERE id=$id");
      header('Location: adminContent.php'); exit;
    }
    if (isset($_GET['unflag_product'])) {
      $id = intval($_GET['unflag_product']);
      $conn->query("UPDATE products SET is_flagged=0 WHERE id=$id");
      header('Location: adminContent.php'); exit;
    }
    if (isset($_GET['flag_story'])) {
      $id = intval($_GET['flag_story']);
      $conn->query("UPDATE stories SET is_flagged=1 WHERE id=$id");
      header('Location: adminContent.php'); exit;
    }
    if (isset($_GET['unflag_story'])) {
      $id = intval($_GET['unflag_story']);
      $conn->query("UPDATE stories SET is_flagged=0 WHERE id=$id");
      header('Location: adminContent.php'); exit;
    }
    ?>
    <div class="my-5">
      <h1 class="mb-4 text-center">Content Monitoring</h1>
      <div class="card mb-4">
        <div class="card-header bg-info text-dark">Uploaded Products</div>
        <div class="card-body table-responsive">
          <table class="table table-bordered table-sm">
            <thead><tr><th>Product</th><th>Artisan</th><th>Description</th><th>Category</th><th>Status</th><th>Flag</th></tr></thead>
            <tbody>
            <?php
            $res = $conn->query("SELECT p.*, a.business_name FROM products p LEFT JOIN artisans a ON p.artisan_id=a.id");
            while ($row = $res->fetch_assoc()) {
              echo '<tr>';
              echo '<td>' . htmlspecialchars($row['name']) . '</td>';
              echo '<td>' . htmlspecialchars($row['business_name']) . '</td>';
              echo '<td style="max-width:200px;">' . htmlspecialchars(substr($row['description'],0,120)) . '...</td>';
              echo '<td>' . htmlspecialchars($row['category']) . '</td>';
              echo '<td>' . ($row['is_featured'] ? 'Featured' : 'Normal');
              if (!empty($row['is_flagged'])) echo ' <span class="badge bg-danger">Flagged</span>';
              echo '</td>';
              echo '<td>';
              if (empty($row['is_flagged'])) {
                echo '<a href="?flag_product=' . $row['id'] . '" class="btn btn-warning btn-sm">Flag</a>';
              } else {
                echo '<a href="?unflag_product=' . $row['id'] . '" class="btn btn-secondary btn-sm">Unflag</a>';
              }
              echo '</td>';
              echo '</tr>';
            }
            ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card mb-4">
        <div class="card-header bg-info text-dark">Uploaded Stories</div>
        <div class="card-body table-responsive">
          <table class="table table-bordered table-sm">
            <thead><tr><th>Title</th><th>Storyteller</th><th>Description</th><th>Status</th><th>Flag</th></tr></thead>
            <tbody>
            <?php
            $res = $conn->query("SELECT s.*, st.artistic_name FROM stories s LEFT JOIN storytellers st ON s.storyteller_id=st.id");
            while ($row = $res->fetch_assoc()) {
              echo '<tr>';
              echo '<td>' . htmlspecialchars($row['title']) . '</td>';
              echo '<td>' . htmlspecialchars($row['artistic_name']) . '</td>';
              echo '<td style="max-width:200px;">' . htmlspecialchars(substr($row['description'],0,120)) . '...</td>';
              echo '<td>' . ($row['is_featured'] ? 'Featured' : 'Normal');
              if (!empty($row['is_flagged'])) echo ' <span class="badge bg-danger">Flagged</span>';
              echo '</td>';
              echo '<td>';
              if (empty($row['is_flagged'])) {
                echo '<a href="?flag_story=' . $row['id'] . '" class="btn btn-warning btn-sm">Flag</a>';
              } else {
                echo '<a href="?unflag_story=' . $row['id'] . '" class="btn btn-secondary btn-sm">Unflag</a>';
              }
              echo '</td>';
              echo '</tr>';
            }
            ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../common/footer.php'; ?>
