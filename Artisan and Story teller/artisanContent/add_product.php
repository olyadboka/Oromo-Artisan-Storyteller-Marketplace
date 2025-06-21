<?php
include '../dbConnection/dbConnection.php';
session_start();
 $_SESSION['artisan_id'] =1;

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Product</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
  .dashboard-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #7c2d12 100%);
  }

  .stat-card {
    transition: all 0.3s ease;
  }

  .stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  }

  .product-card:hover .product-actions {
    opacity: 1;
  }

  .product-actions {
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  </style>
</head>

<body class="bg-gray-100">
  <!-- Dashboard Header -->
  <header class="dashboard-header text-white">
    <div class="container mx-auto px-4 py-6">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
        <div class="flex items-center space-x-4 mb-4 md:mb-0">
          <img src="profile-pic.jpg" alt="Your Profile"
            class="w-16 h-16 rounded-full border-4 border-white object-cover">
          <div>
            <h1 class="text-2xl font-bold">My Artisan Dashboard</h1>
            <p class="text-white text-opacity-80">
              <i class="fas fa-map-marker-alt"></i> Oromia, Ethiopia
              <span class="ml-3 px-2 py-1 bg-white bg-opacity-20 rounded-full text-xs">
                <i class="fas fa-check-circle"></i> Verified
              </span>
            </p>
          </div>
        </div>
        <nav>
          <ul class="flex space-x-4">
            <li><a href="#" class="px-3 py-2 bg-white bg-opacity-20 rounded-lg"><i class="fas fa-cog mr-2"></i>as
                Custormer</a></li>
            <li><a href="#" class="px-3 py-2 bg-white bg-opacity-20 rounded-lg"><i
                  class="fas fa-sign-out-alt mr-2"></i>Logout</a></li>
          </ul>
        </nav>
      </div>
    </div>
  </header>

  <!-- Dashboard Navigation -->
  <div class="bg-white shadow-sm">
    <div class="container mx-auto px-4">
      <nav class="flex overflow-x-auto">
        <a href="#" class="px-6 py-4 font-medium text-red-600 border-b-2 border-red-600">Overview</a>
        <a href="./product.php" class="px-6 py-4 font-medium text-gray-600 hover:text-red-600">Products</a>

        <a href="./orders.php" class="px-6 py-4 font-medium text-gray-600 hover:text-red-600">Orders</a>
        <a href="./earning.php" class="px-6 py-4 font-medium text-gray-600 hover:text-red-600">Earnings</a>


      </nav>
    </div>
  </div>



  <!-- Main Content -->
  <main class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-plus-circle text-red-600 mr-2"></i> Add New Product
      </h1>
      <a href="products.html" class="text-gray-600 hover:text-red-600">
        <i class="fas fa-times mr-1"></i> Cancel
      </a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <form method="post" enctype="multipart/form-data" name="add_product" action="./dbArtisan/add_productdb.php">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-lg font-medium text-gray-800">Basic Information</h2>
        </div>

        <!-- Add Bootstrap CSS -->


        <div class="p-6">
          <div class="row g-4">
            <!-- Left Column -->
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label text-gray-700">Product Name *</label>
                <input type="text" required class="form-control" name="product_name" placeholder="Enter product name">
              </div>
              <div class="mb-3">
                <label class="form-label text-gray-700">Category *</label>
                <select required class="form-select" name="product_category">
                  <option value="">Select a category</option>
                  <option value="baskets">Baskets</option>
                  <option value="textiles">Textiles</option>
                  <option value="jewelry">Jewelry</option>
                  <option value="pottery">Pottery</option>
                  <option value="woodCarving">Wood Carvings</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label text-gray-700">Created From *</label>
                <input type="text" required class="form-control" name="product_material"
                  placeholder="e.g. Bamboo, Silver">
              </div>
              <div class="mb-3">
                <label class="form-label text-gray-700">Description *</label>
                <textarea rows="4" required class="form-control" name="product_descrition"
                  placeholder="Describe your product"></textarea>
                <div class="form-text">Describe your product in detail, including materials used and cultural
                  significance.</div>
              </div>
            </div>
            <!-- Right Column -->
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label text-gray-700">Price (ETB) *</label>
                <input type="number" step="0.01" min="0" required class="form-control" name="product_price"
                  placeholder="e.g. 500">
              </div>
              <div class="mb-3">
                <label class="form-label text-gray-700">Quantity *</label>
                <input type="number" min="0" required class="form-control" name="product_quantity"
                  placeholder="e.g. 10">
              </div>
              <div class="mb-3">
                <label class="form-label text-gray-700">Product Images *</label>
                <input type="file" class="form-control mb-2" name="pImage[]" accept="image/*" multiple required>

                <div class="form-text">Upload up to 4 product images.</div>
              </div>
              <div class="mb-3">
                <label class="form-label text-gray-700">Related Images</label>
                <input type="file" class="form-control mb-2" name="rImage[]" accept="image/*" multiple>

                <div class="form-text">Optional: Add related images (e.g. artisan at work, raw materials).</div>
              </div>
              <div class="mb-3">
                <label class="form-label text-gray-700">Related Video</label>
                <input type="file" class="form-control" name="rVideo" accept="video/*">
                <div class="form-text">Optional: Add a short video about the product or its making.</div>
              </div>
            </div>
          </div>
        </div>

        <div class="p-6 border-t border-gray-200 bg-gray-50">
          <div class="flex justify-end space-x-3">
            <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-100">
              Save as Draft
            </button>
            <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium">
              Publish Product
            </button>
          </div>
        </div>
      </form>
    </div>
  </main>
  <?php
include '../common/footer.php';
?>