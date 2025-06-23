<?php
session_start();
include '../common/dbConnection.php';

// $_SESSION['user_id'] =18;
if(!isset($_SESSION['user_id'])){

header("Location: ../../../User managemen/login.php");
}


// $user_id = $_SESSION['user_id'];
// $sql = "SELECT profileImage from users where id = $user_id";
// $result = mysqli_query($con, $sql);
// $profileData = null;
// if ($result) {
//   while($row = mysqli_fetch_assoc($result)){
//     $profileData = $row['profileImage'];
//   }
// }

 $stmt = mysqli_prepare($con, "SELECT profileImage FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "s", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        $imageData = '';
        if ($row = mysqli_fetch_assoc($result)) {
          $imageData = base64_encode($row['profileImage']); 
        }

?>



<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Artisan Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
  .dashboard-header {
    background: linear-gradient(135deg, #1e3a8a 0%, rgb(28, 185, 51) 100%);
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


          <?php if (!empty($profileData)): ?>
          <img src="data:image/jpeg;base64,' . $imageData . '" alt="Profile Image"
            class="w-16 h-16 rounded-full border-4 border-white object-cover">
          <?php else: ?>
          <img src="https://ui-avatars.com/api/?name=User" alt="Default Profile"
            class="w-16 h-16 rounded-full border-4 border-white object-cover">
          <?php endif; ?>
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

            <li><a href="../User managemen/logout.php" class="px-3 py-2 bg-white bg-opacity-20 rounded-lg"><i
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
        <a href="#" class="px-6 py-4 font-medium text-green-600 border-b-2 border-green-600">Overview</a>
        <a href="./artisanContent/product.php"
          class="px-6 py-4 font-medium text-gray-600 hover:text-green-600">Products</a>

        <a href="./artisanContent/orders.php"
          class="px-6 py-4 font-medium text-gray-600 hover:text-green-600">Orders</a>
        <a href="./artisanContent/earning.php"
          class="px-6 py-4 font-medium text-gray-600 hover:text-green-600">Earnings</a>

      </nav>
    </div>
  </div>
  <!-- Main Content -->

  <main class="container mx-auto px-4 py-8">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="stat-card bg-white rounded-lg shadow p-6 text-center">
        <div class="text-3xl font-bold text-green-600 mb-2">24</div>
        <div class="text-gray-600">Active Products</div>
        <a href="./artisanContent/product.php"
          class="mt-3 inline-block text-sm text-green-600 hover:text-red-700">Manage
          <i class="fas fa-arrow-right ml-1"></i></a>
      </div>
      <div class="stat-card bg-white rounded-lg shadow p-6 text-center">
        <div class="text-3xl font-bold text-green-600 mb-2">8</div>
        <div class="text-gray-600">Pending Orders</div>
        <a href="./artisanContent/orders.php" class="mt-3 inline-block text-sm text-green-600 hover:text-red-700">View
          <i class="fas fa-arrow-right ml-1"></i></a>
      </div>
      <div class="stat-card bg-white rounded-lg shadow p-6 text-center">
        <div class="text-3xl font-bold text-green-600 mb-2">ETB 12,450</div>
        <div class="text-gray-600">This Month's Earnings</div>
        <a href="./artisanContent/earning.php"
          class="mt-3 inline-block text-sm text-green-600 hover:text-green-700">Details
          <i class="fas fa-arrow-right ml-1"></i></a>
      </div>
      <div class="stat-card bg-white rounded-lg shadow p-6 text-center">
        <div class="text-3xl font-bold text-green-600 mb-2">4.8</div>
        <div class="text-gray-600">Average Rating</div>
        <a href="#" class="mt-3 inline-block text-sm text-green-600 hover:text-red-700">Reviews <i
            class="fas fa-arrow-right ml-1"></i></a>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
      <h2 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h2>
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="./artisanContent/add_product.php"
          class="border border-gray-200 rounded-lg p-4 text-center hover:border-red-500 transition-colors">
          <div class="text-green-600 mb-2"><i class="fas fa-plus-circle text-2xl"></i></div>
          <div class="font-medium">Add New Product</div>
        </a>


        <a href="./artisanContent/analytics.php"
          class="border border-gray-200 rounded-lg p-4 text-center hover:border-red-500 transition-colors">
          <div class="text-green-600 mb-2"><i class="fas fa-chart-line text-2xl"></i></div>
          <div class="font-medium">View Analytics</div>
        </a>
      </div>
    </div>

    <!-- Recent Activity -->
    <div class="flex flex-col lg:flex-row gap-8">
      <!-- Recent Orders -->
      <div class="lg:w-2/3">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Recent Orders</h2>
            <a href="#" class="text-red-600 hover:text-red-700 font-medium">View All</a>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#ORD-1245</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">John D.</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Jun 15, 2023</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <a href="#" class="text-red-600 hover:text-red-900">Process</a>
                  </td>
                </tr>
                <tr>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#ORD-1244</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Sarah M.</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Jun 14, 2023</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Shipped</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <a href="#" class="text-green-600 hover:text-red-900">Track</a>
                  </td>
                </tr>
                <tr>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#ORD-1243</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Michael T.</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Jun 12, 2023</td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span
                      class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                  </td>

                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Recent Products -->
      <div class="lg:w-1/3">
        <div class="bg-white rounded-lg shadow p-6">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Your Products</h2>
            <a href="./artisanContent/product.php" class="text-red-600 hover:text-red-700 font-medium">View All</a>
          </div>

          <div class="space-y-4">
            <div class="product-card border border-gray-200 rounded-lg overflow-hidden relative">
              <img src="product1.jpg" alt="Traditional Basket" class="w-full h-40 object-cover">
              <div class="p-4">
                <h3 class="font-semibold text-gray-800 mb-1">Traditional Basket</h3>
                <div class="flex justify-between items-center">
                  <span class="text-gray-600">ETB 350.00</span>
                  <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800">In Stock (12)</span>
                </div>
              </div>
              <div class="product-actions absolute top-2 right-2 bg-white bg-opacity-90 p-2 rounded-lg shadow-sm">
                <button class="text-blue-600 hover:text-blue-800 p-1"><i class="fas fa-edit"></i></button>
                <button class="text-red-600 hover:text-red-800 p-1"><i class="fas fa-trash"></i></button>
              </div>
            </div>

            <div class="product-card border border-gray-200 rounded-lg overflow-hidden relative">
              <img src="product2.jpg" alt="Oromo Necklace" class="w-full h-40 object-cover">
              <div class="p-4">
                <h3 class="font-semibold text-gray-800 mb-1">Oromo Necklace</h3>
                <div class="flex justify-between items-center">
                  <span class="text-gray-600">ETB 750.00</span>
                  <span class="text-xs px-2 py-1 rounded-full bg-yellow-100 text-yellow-800">Low Stock (3)</span>
                </div>
              </div>
              <div class="product-actions absolute top-2 right-2 bg-white bg-opacity-90 p-2 rounded-lg shadow-sm">
                <button class="text-blue-600 hover:text-blue-800 p-1"><i class="fas fa-edit"></i></button>
                <button class="text-red-600 hover:text-red-800 p-1"><i class="fas fa-trash"></i></button>
              </div>
            </div>
          </div>

          <button
            class="mt-4 w-full border-2 border-dashed border-gray-300 rounded-lg py-3 text-gray-500 hover:text-red-600 hover:border-red-300 transition-colors">
            <i class="fas fa-plus mr-2"></i> Add New Product
          </button>
        </div>
      </div>
    </div>
  </main>
  <?php 
include './common/footer.php';
?>