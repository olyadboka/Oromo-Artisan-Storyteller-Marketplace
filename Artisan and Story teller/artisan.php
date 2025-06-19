<?php
// Check authentication and artisan role
session_start();
require_once 'auth_check.php';
require_once 'db_connection.php';

$artisan_id = $_SESSION['user_id'];

// Get artisan details
$stmt = $pdo->prepare("SELECT * FROM artisans WHERE user_id = ?");
$stmt->execute([$artisan_id]);
$artisan = $stmt->fetch(PDO::FETCH_ASSOC);

// Get artisan's products
$products_stmt = $pdo->prepare("SELECT * FROM products WHERE artisan_id = ? ORDER BY created_at DESC");
$products_stmt->execute([$artisan['id']]);
$products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header
include 'header.php';
?>

<div class="container">
  <h1>Artisan Dashboard</h1>

  <!-- Profile Section -->
  <div class="card mb-4">
    <div class="card-header">
      <h2>Your Profile</h2>
    </div>
    <div class="card-body">
      <form action="update_profile.php" method="POST" enctype="multipart/form-data">
        <!-- Profile form fields -->
        <div class="form-group">
          <label>Business Name</label>
          <input type="text" name="business_name" class="form-control"
            value="<?= htmlspecialchars($artisan['business_name'] ?? '') ?>">
        </div>
        <!-- More fields... -->
        <button type="submit" class="btn btn-primary">Update Profile</button>
      </form>
    </div>
  </div>

  <!-- Products Section -->
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h2>Your Products</h2>
      <a href="add_product.php" class="btn btn-success">Add New Product</a>
    </div>
    <div class="card-body">
      <?php if (count($products) > 0): ?>
      <div class="row">
        <?php foreach ($products as $product): ?>
        <div class="col-md-4 mb-4">
          <div class="card">
            <?php 
                                // Get primary image
                                $img_stmt = $pdo->prepare("SELECT image_url FROM product_images 
                                                          WHERE product_id = ? AND is_primary = 1 LIMIT 1");
                                $img_stmt->execute([$product['id']]);
                                $primary_img = $img_stmt->fetch(PDO::FETCH_ASSOC);
                                ?>
            <img src="<?= htmlspecialchars($primary_img['image_url'] ?? 'placeholder.jpg') ?>" class="card-img-top"
              alt="<?= htmlspecialchars($product['name']) ?>">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
              <p class="card-text"><?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...</p>
              <p class="text-success">ETB <?= number_format($product['price'], 2) ?></p>
              <a href="edit_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
              <a href="delete_product.php?id=<?= $product['id'] ?>" class="btn btn-sm btn-danger"
                onclick="return confirm('Are you sure?')">Delete</a>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <p>You haven't added any products yet.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>