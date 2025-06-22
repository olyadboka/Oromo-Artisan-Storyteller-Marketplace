<?php
// Include the modular admin header
include 'common/adminHeader.php';
?>
<!-- End Admin Dashboard Custom Header -->

<style>
@media (max-width: 900px) {
  .admin-content-flex { flex-direction: column !important; }
  .admin-content-main { padding: 15px !important; }
}
/* Restore admin content table and badge styles */
.admin-card-header {
  background: linear-gradient(120deg, #1a4a7a 60%, #2d7a2d 100%);
  color: #fff;
  font-weight: 700;
  font-size: 1.1em;
  border-radius: 10px 10px 0 0;
  padding: 14px 18px;
  letter-spacing: 0.01em;
}
.badge-flagged {
  background: #e17055;
  color: #fff;
  font-weight: 600;
  border-radius: 6px;
  padding: 2px 8px;
  font-size: 0.95em;
  margin-left: 6px;
}
.btn-flag {
  background: #e17055;
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 4px 12px;
  font-weight: 600;
  transition: background 0.2s;
}
.btn-flag:hover {
  background: #c0392b;
}
.btn-unflag {
  background: #1cb933;
  color: #fff;
  border: none;
  border-radius: 6px;
  padding: 4px 12px;
  font-weight: 600;
  transition: background 0.2s;
}
.btn-unflag:hover {
  background: #148f1a;
}
.table-bordered th, .table-bordered td {
  border: 1px solid #e0e6ed !important;
}
.table thead th {
  background: #1a4a7a;
  color: #fff;
  font-weight: 700;
  letter-spacing: 0.01em;
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
</style>
<div class="admin-content-flex d-flex" style="min-height:100vh;">
  <?php include 'adminSidebar.php'; ?>
  <div class="admin-content-main" style="flex:1; padding:30px; min-width:0;">
    <?php
    // Connect to the database
    include '../common/dbConnection.php';
    // Handle flag/unflag actions for products and stories
    if (isset($_GET['flag_product'])) {
      $productId = intval($_GET['flag_product']);
      $flagProduct = $conn->query("UPDATE products SET is_flagged=1 WHERE id=" . $productId);
      header('Location: adminContent.php');
      exit;
    }
    if (isset($_GET['unflag_product'])) {
      $productId = intval($_GET['unflag_product']);
      $unflagProduct = $conn->query("UPDATE products SET is_flagged=0 WHERE id=" . $productId);
      header('Location: adminContent.php');
      exit;
    }
    if (isset($_GET['flag_story'])) {
      $storyId = intval($_GET['flag_story']);
      $flagStory = $conn->query("UPDATE stories SET is_flagged=1 WHERE id=" . $storyId);
      header('Location: adminContent.php');
      exit;
    }
    if (isset($_GET['unflag_story'])) {
      $storyId = intval($_GET['unflag_story']);
      $unflagStory = $conn->query("UPDATE stories SET is_flagged=0 WHERE id=" . $storyId);
      header('Location: adminContent.php');
      exit;
    }
    ?>
    <div class="mt-2 mb-5">
      <h1 class="mb-3 text-center" style="margin-top:0.5em;">Content Monitoring</h1>
      <div class="card mb-4">
        <div class="card-header admin-card-header">Uploaded Products</div>
        <div class="card-body table-responsive">
          <table class="table table-bordered table-sm">
            <thead><tr><th>Product</th><th>Artisan</th><th>Description</th><th>Category</th><th>Status</th><th>Flag</th></tr></thead>
            <tbody>
            <?php
            // Get all products and their artisans
            $getProducts = $conn->query("SELECT p.*, a.business_name FROM products p LEFT JOIN artisans a ON p.artisan_id=a.id");
            while ($row = $getProducts->fetch_assoc()) {
              echo '<tr>';
              echo '<td>' . htmlspecialchars($row['name']) . '</td>';
              echo '<td>' . htmlspecialchars($row['business_name']) . '</td>';
              echo '<td style="max-width:200px;">' . htmlspecialchars(substr($row['description'],0,120)) . '...</td>';
              echo '<td>' . htmlspecialchars($row['category']) . '</td>';
              echo '<td>';
              if (!empty($row['is_featured'])) {
                echo 'Featured';
              } else {
                echo 'Normal';
              }
              if (!empty($row['is_flagged'])) echo ' <span class="badge-flagged">Flagged</span>';
              echo '</td>';
              echo '<td>';
              if (empty($row['is_flagged'])) {
                echo '<a href="?flag_product=' . $row['id'] . '" class="btn btn-flag btn-sm">Flag</a>';
              } else {
                echo '<a href="?unflag_product=' . $row['id'] . '" class="btn btn-unflag btn-sm">Unflag</a>';
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
        <div class="card-header admin-card-header">Uploaded Stories</div>
        <div class="card-body table-responsive">
          <table class="table table-bordered table-sm">
            <thead><tr><th>Title</th><th>Storyteller</th><th>Description</th><th>Status</th><th>Flag</th></tr></thead>
            <tbody>
            <?php
            // Get all stories and their storytellers
            $getStories = $conn->query("SELECT s.*, st.artistic_name FROM stories s LEFT JOIN storytellers st ON s.storyteller_id=st.id");
            while ($row = $getStories->fetch_assoc()) {
              echo '<tr>';
              echo '<td>' . htmlspecialchars($row['title']) . '</td>';
              echo '<td>' . htmlspecialchars($row['artistic_name']) . '</td>';
              echo '<td style="max-width:200px;">' . htmlspecialchars(substr($row['description'],0,120)) . '...</td>';
              echo '<td>';
              if (!empty($row['is_featured'])) {
                echo 'Featured';
              } else {
                echo 'Normal';
              }
              if (!empty($row['is_flagged'])) echo ' <span class="badge-flagged">Flagged</span>';
              echo '</td>';
              echo '<td>';
              if (empty($row['is_flagged'])) {
                echo '<a href="?flag_story=' . $row['id'] . '" class="btn btn-flag btn-sm">Flag</a>';
              } else {
                echo '<a href="?unflag_story=' . $row['id'] . '" class="btn btn-unflag btn-sm">Unflag</a>';
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
