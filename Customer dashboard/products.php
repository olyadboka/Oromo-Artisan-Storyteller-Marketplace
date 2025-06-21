<?php include '../common/commonHeader.php'; ?>
<?php
include '../common/dbConnection.php';

// Fetch unique categories, locations, artisans for filters
$categories = mysqli_query($conn, "SELECT DISTINCT category FROM products");
$locations = mysqli_query($conn, "SELECT DISTINCT location FROM products");
$artisans = mysqli_query($conn, "SELECT DISTINCT artisan FROM products");

// Handle search/filter
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$location = $_GET['location'] ?? '';
$artisan = $_GET['artisan'] ?? '';

$query = "SELECT * FROM products WHERE 1=1";
if ($search) $query .= " AND name LIKE '%$search%'";
if ($category) $query .= " AND category='$category'";
if ($location) $query .= " AND location='$location'";
if ($artisan) $query .= " AND artisan='$artisan'";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Browse Products</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-yellow-50 min-h-screen">
  <div class="container mx-auto px-4 py-10">
    <h1 class="text-3xl font-extrabold text-center text-blue-900 mb-8 tracking-tight">🛍️ Explore Oromo Artisan Products</h1>
    <form method="get" class="flex flex-wrap gap-4 justify-center mb-8">
      <div class="relative">
        <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>"
          class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:outline-none shadow-sm w-56">
        <span class="absolute left-3 top-2.5 text-gray-400"><i class="fas fa-search"></i></span>
      </div>
      <select name="category" class="rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-400">
        <option value="">All Categories</option>
        <?php while($row = mysqli_fetch_assoc($categories)): ?>
          <option value="<?= htmlspecialchars($row['category']) ?>" <?= $category == $row['category'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($row['category']) ?>
          </option>
        <?php endwhile; ?>
      </select>
      <select name="location" class="rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-400">
        <option value="">All Locations</option>
        <?php mysqli_data_seek($locations, 0); while($row = mysqli_fetch_assoc($locations)): ?>
          <option value="<?= htmlspecialchars($row['location']) ?>" <?= $location == $row['location'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($row['location']) ?>
          </option>
        <?php endwhile; ?>
      </select>
      <select name="artisan" class="rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-400">
        <option value="">All Artisans</option>
        <?php mysqli_data_seek($artisans, 0); while($row = mysqli_fetch_assoc($artisans)): ?>
          <option value="<?= htmlspecialchars($row['artisan']) ?>" <?= $artisan == $row['artisan'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($row['artisan']) ?>
          </option>
        <?php endwhile; ?>
      </select>
      <button type="submit" class="bg-blue-700 hover:bg-blue-900 text-white px-6 py-2 rounded-lg font-semibold shadow transition">Filter</button>
    </form>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
      <?php if(mysqli_num_rows($result) == 0): ?>
        <div class="col-span-full text-center text-gray-500 text-lg">No products found.</div>
      <?php endif; ?>
      <?php 
      $sampleImages = [
        'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80',
        'https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=400&q=80',
        'https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80',
        'https://images.unsplash.com/photo-1526178613658-3f1622045557?auto=format&fit=crop&w=400&q=80',
        'https://images.unsplash.com/photo-1519985176271-adb1088fa94c?auto=format&fit=crop&w=400&q=80',
        'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=400&q=80',
        'https://images.unsplash.com/photo-1465101178521-c1a9136a3b99?auto=format&fit=crop&w=400&q=80',
        'https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80',
      ];
      $imgIdx = 0;
      while($row = mysqli_fetch_assoc($result)):
        $img = $row['image'] ? htmlspecialchars($row['image']) : $sampleImages[$imgIdx % count($sampleImages)];
        $imgIdx++;
      ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform hover:-translate-y-2 hover:shadow-2xl transition relative group">
          <img src="<?= $img ?>" alt="Product" class="w-full h-48 object-cover group-hover:scale-105 transition">
          <div class="p-5">
            <h2 class="font-bold text-lg text-blue-900 mb-1"><?= htmlspecialchars($row['name']) ?></h2>
            <p class="text-gray-600 text-sm mb-2"><?= htmlspecialchars($row['description']) ?></p>
            <div class="flex items-center justify-between mb-2">
              <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded"><?= htmlspecialchars($row['category']) ?></span>
              <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded"><?= htmlspecialchars($row['location']) ?></span>
            </div>
            <div class="flex items-center justify-between">
              <span class="font-bold text-green-700 text-lg">ETB <?= $row['price'] ?></span>
              <button onclick="addToCart(<?= $row['id'] ?>)" class="bg-gradient-to-r from-yellow-400 to-red-500 hover:from-yellow-500 hover:to-red-600 text-white px-4 py-2 rounded-lg font-semibold shadow flex items-center gap-2 transition">
                <i class="fas fa-cart-plus"></i> Add to Cart
              </button>
            </div>
          </div>
          <div class="absolute top-3 right-3 bg-white bg-opacity-80 rounded-full px-3 py-1 text-xs font-semibold text-gray-700 shadow">
            By <?= htmlspecialchars($row['artisan']) ?>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>
  <script>
    function addToCart(productId) {
      // Use AJAX or redirect to add to cart
      window.location.href = 'cart.php?add=' + productId;
    }
  </script>
</body>
</html>