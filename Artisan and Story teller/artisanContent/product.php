<?php
include '../dbConnection/dbConnection.php';
session_start();
$_SESSION['artisan_id'] = 1;

$artisan_id = $_SESSION['artisan_id'];

$sql = "SELECT p.*, 
               pi.rImage1, pi.rImage2, pi.rImage3, pi.rVideo 
        FROM products p
        LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
        WHERE p.artisan_id = $artisan_id
        ORDER BY p.id DESC";
$result = mysqli_query($con, $sql);

// Function to detect image type from BLOB data
function getImageType($imageData)
{
  if (empty($imageData)) return false;
}

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Products</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="./CSS/products.css">
</head>

<body class="bg-gray-100">
  <!-- Header and navigation content -->
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
            </p>
          </div>
        </div>
        <nav>
          <ul class="flex space-x-4">
            <li><a href="#" class="px-3 py-2 bg-white bg-opacity-20 rounded-lg"><i
                  class="fas fa-sign-out-alt mr-2"></i>Logout</a></li>
          </ul>
        </nav>
      </div>
    </div>
  </header>

  <div class="bg-white shadow-sm">
    <div class="container mx-auto px-4">
      <nav class="flex overflow-x-auto">
        <a href="#" class="px-6 py-4 font-medium text-red-600 border-b-2 border-red-600">Overview</a>
        <a href="./product.php" class="px-6 py-4 font-medium text-gray-600 hover:text-red-600">Products</a>
        <a href="./orders.php" class="px-6 py-4 font-medium text-gray-600 hover:text-red-600">Orders</a>
      </nav>
    </div>
  </div>

  <main class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-tshirt text-red-600 mr-2"></i> My Products
      </h1>
      <a href="add_product.php" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium">
        <i class="fas fa-plus mr-2"></i> Add New Product
      </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <?php while ($product = mysqli_fetch_assoc($result)): ?>
        <div class="product-card bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden relative">
          <div class="relative h-48 overflow-hidden">
            <?php if ($product['pImage1']): ?>
              <img src="../uploads/products/<?php echo htmlspecialchars($product['pImage1']); ?>"
                alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover">
            <?php else: ?>
              <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                <i class="fas fa-image text-gray-400 text-4xl"></i>
              </div>
            <?php endif; ?>
            <div
              class="product-actions absolute top-2 right-2 bg-white bg-opacity-90 p-2 rounded-lg shadow-sm flex space-x-1">
              <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="text-blue-600 hover:text-blue-800 p-1">
                <i class="fas fa-edit"></i>
              </a>
              <a href="#" class="text-green-600 hover:text-green-800 p-1 view-product"
                data-id="<?php echo $product['id']; ?>">
                <i class="fas fa-eye"></i>
              </a>
            </div>
          </div>
          <div class="p-4">
            <h3 class="font-semibold text-gray-800 mb-1"><?php echo htmlspecialchars($product['name']); ?></h3>
            <p class="text-sm text-gray-600 mb-2">
              <?php echo substr(htmlspecialchars($product['description']), 0, 50); ?>...</p>
            <div class="flex justify-between items-center">
              <span class="font-bold text-gray-900">ETB <?php echo number_format($product['price'], 2); ?></span>
              <span
                class="text-xs px-2 py-1 rounded-full <?php echo $product['quantity'] > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                <?php echo $product['quantity'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
              </span>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </main>


  <?php include '../common/footer.php'; ?>
  <script src="./js/product.js"></script>

</body>

</html>