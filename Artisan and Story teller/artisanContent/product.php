<?php
include '../dbConnection/dbConnection.php';

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Products</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        <a href="#" class="px-6 py-4 font-medium text-gray-600 hover:text-red-600">Messages</a>
      </nav>
    </div>
  </div>
  <!-- Main Content -->
  <main class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-tshirt text-red-600 mr-2"></i> My Products
      </h1>
      <a href="add-product.html" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium">
        <i class="fas fa-plus mr-2"></i> Add New Product
      </a>
    </div>

    <!-- Product Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
      <div class="flex flex-col md:flex-row md:items-center md:space-x-6 space-y-4 md:space-y-0">
        <div class="flex-1">
          <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
          <input type="text" placeholder="Search products..."
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-red-500 focus:border-red-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
          <select class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-red-500 focus:border-red-500">
            <option>All Categories</option>
            <option>Baskets</option>
            <option>Textiles</option>
            <option>Jewelry</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select class="border border-gray-300 rounded-lg px-4 py-2 focus:ring-red-500 focus:border-red-500">
            <option>All Status</option>
            <option>Active</option>
            <option>Out of Stock</option>
            <option>Draft</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <!-- Product 1 -->
      <div class="product-card bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden relative">
        <div class="relative h-48 overflow-hidden">
          <img src="https://via.placeholder.com/300" alt="Product" class="w-full h-full object-cover">
          <div
            class="product-actions absolute top-2 right-2 bg-white bg-opacity-90 p-2 rounded-lg shadow-sm flex space-x-1">
            <button class="text-blue-600 hover:text-blue-800 p-1">
              <i class="fas fa-edit"></i>
            </button>
            <button class="text-red-600 hover:text-red-800 p-1">
              <i class="fas fa-trash"></i>
            </button>
            <button class="text-green-600 hover:text-green-800 p-1">
              <i class="fas fa-eye"></i>
            </button>
          </div>
          <span class="absolute top-2 left-2 bg-white text-xs px-2 py-1 rounded-full shadow-sm">
            <i class="fas fa-check-circle text-green-500 mr-1"></i> Active
          </span>
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-gray-800 mb-1">Traditional Oromo Basket</h3>
          <p class="text-sm text-gray-600 mb-2">Handwoven from natural fibers</p>
          <div class="flex justify-between items-center">
            <span class="font-bold text-gray-900">ETB 350.00</span>
            <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800">In Stock (12)</span>
          </div>
        </div>
      </div>

      <!-- Product 2 -->
      <div class="product-card bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden relative">
        <div class="relative h-48 overflow-hidden">
          <img src="https://via.placeholder.com/300" alt="Product" class="w-full h-full object-cover">
          <div
            class="product-actions absolute top-2 right-2 bg-white bg-opacity-90 p-2 rounded-lg shadow-sm flex space-x-1">
            <button class="text-blue-600 hover:text-blue-800 p-1">
              <i class="fas fa-edit"></i>
            </button>
            <button class="text-red-600 hover:text-red-800 p-1">
              <i class="fas fa-trash"></i>
            </button>
            <button class="text-green-600 hover:text-green-800 p-1">
              <i class="fas fa-eye"></i>
            </button>
          </div>
          <span class="absolute top-2 left-2 bg-white text-xs px-2 py-1 rounded-full shadow-sm">
            <i class="fas fa-check-circle text-green-500 mr-1"></i> Active
          </span>
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-gray-800 mb-1">Handwoven Coffee Mat</h3>
          <p class="text-sm text-gray-600 mb-2">Traditional design, 40cm diameter</p>
          <div class="flex justify-between items-center">
            <span class="font-bold text-gray-900">ETB 250.00</span>
            <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">Low Stock (3)</span>
          </div>
        </div>
      </div>

      <!-- Product 3 -->
      <div class="product-card bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden relative">
        <div class="relative h-48 overflow-hidden">
          <img src="https://via.placeholder.com/300" alt="Product" class="w-full h-full object-cover">
          <div
            class="product-actions absolute top-2 right-2 bg-white bg-opacity-90 p-2 rounded-lg shadow-sm flex space-x-1">
            <button class="text-blue-600 hover:text-blue-800 p-1">
              <i class="fas fa-edit"></i>
            </button>
            <button class="text-red-600 hover:text-red-800 p-1">
              <i class="fas fa-trash"></i>
            </button>
            <button class="text-green-600 hover:text-green-800 p-1">
              <i class="fas fa-eye"></i>
            </button>
          </div>
          <span class="absolute top-2 left-2 bg-white text-xs px-2 py-1 rounded-full shadow-sm">
            <i class="fas fa-times-circle text-red-500 mr-1"></i> Draft
          </span>
        </div>
        <div class="p-4">
          <h3 class="font-semibold text-gray-800 mb-1">Oromo Ceremonial Necklace</h3>
          <p class="text-sm text-gray-600 mb-2">Beaded with traditional patterns</p>
          <div class="flex justify-between items-center">
            <span class="font-bold text-gray-900">ETB 750.00</span>
            <span class="text-xs px-2 py-1 rounded-full bg-gray-100 text-gray-800">Not Listed</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Pagination -->
    <div class="mt-8 flex justify-center">
      <nav class="inline-flex rounded-md shadow">
        <a href="#" class="px-3 py-2 rounded-l-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-50">
          <i class="fas fa-chevron-left"></i>
        </a>
        <a href="#" class="px-4 py-2 border-t border-b border-gray-300 bg-white text-red-600 font-medium">1</a>
        <a href="#" class="px-4 py-2 border-t border-b border-gray-300 bg-white text-gray-500 hover:bg-gray-50">2</a>
        <a href="#" class="px-4 py-2 border-t border-b border-gray-300 bg-white text-gray-500 hover:bg-gray-50">3</a>
        <a href="#" class="px-3 py-2 rounded-r-md border border-gray-300 bg-white text-gray-500 hover:bg-gray-50">
          <i class="fas fa-chevron-right"></i>
        </a>
      </nav>
    </div>
  </main>

  <?php
include '../common/footer.php';
 ?>