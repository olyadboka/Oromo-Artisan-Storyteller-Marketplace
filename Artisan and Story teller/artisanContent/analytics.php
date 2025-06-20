<?php
include '../dbConnection/dbConnection.php';

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Artisan Analytics</title>
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
        <i class="fas fa-chart-line text-red-600 mr-2"></i> Analytics Dashboard
      </h1>
      <div class="flex items-center space-x-4">
        <div class="relative">
          <select
            class="appearance-none border border-gray-300 rounded-lg px-4 py-2 pr-8 focus:ring-red-500 focus:border-red-500">
            <option>Last 7 Days</option>
            <option>Last 30 Days</option>
            <option>Last 90 Days</option>
            <option>This Year</option>
            <option>Custom Range</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
            <i class="fas fa-chevron-down"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500">Total Views</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1">1,248</h3>
            <p class="text-sm text-green-600 mt-1">
              <i class="fas fa-arrow-up mr-1"></i> 12.5% from last period
            </p>
          </div>
          <div class="p-3 bg-blue-100 rounded-lg text-blue-800">
            <i class="fas fa-eye text-2xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500">Total Orders</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1">48</h3>
            <p class="text-sm text-green-600 mt-1">
              <i class="fas fa-arrow-up mr-1"></i> 8.3% from last period
            </p>
          </div>
          <div class="p-3 bg-green-100 rounded-lg text-green-800">
            <i class="fas fa-shopping-cart text-2xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500">Conversion Rate</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1">3.85%</h3>
            <p class="text-sm text-red-600 mt-1">
              <i class="fas fa-arrow-down mr-1"></i> 1.2% from last period
            </p>
          </div>
          <div class="p-3 bg-purple-100 rounded-lg text-purple-800">
            <i class="fas fa-percentage text-2xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500">Total Revenue</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1">ETB 12,450</h3>
            <p class="text-sm text-green-600 mt-1">
              <i class="fas fa-arrow-up mr-1"></i> 15.7% from last period
            </p>
          </div>
          <div class="p-3 bg-yellow-100 rounded-lg text-yellow-800">
            <i class="fas fa-coins text-2xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
      <!-- Sales Chart -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-medium text-gray-800 mb-4">Sales Overview</h2>
        <canvas id="salesChart" height="300"></canvas>
      </div>

      <!-- Traffic Chart -->
      <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-medium text-gray-800 mb-4">Traffic Sources</h2>
        <canvas id="trafficChart" height="300"></canvas>
      </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-800">Top Performing Products</h2>
      </div>
      <div class="divide-y divide-gray-200">
        <div class="p-4 hover:bg-gray-50">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <img src="https://via.placeholder.com/60" alt="Product" class="w-16 h-16 rounded object-cover">
              <div>
                <h3 class="font-medium">Traditional Oromo Basket</h3>
                <p class="text-sm text-gray-600">24 sales • ETB 8,400</p>
              </div>
            </div>
            <div class="text-right">
              <p class="font-medium">ETB 350.00</p>
              <p class="text-sm text-gray-500">per item</p>
            </div>
          </div>
        </div>
        <div class="p-4 hover:bg-gray-50">
          <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <img src="https://via.placeholder.com/60" alt="Product" class="w-16 h-16 rounded object-cover">
              <div>
                <h3 class="font-medium">Handwoven Coffee Mat</h3>
                <p class="text-sm text-gray-600">18 sales • ETB 4,500</p>
              </div>
            </div>
            <div class="text-right">
              <p class="font-medium">ETB 250.00</p>
              <p class="text-sm text-gray-500">per item</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <?php
include '../common/footer.php';
 ?>

  <script>
  // Sales Chart
  const salesCtx = document.getElementById('salesChart').getContext('2d');
  const salesChart = new Chart(salesCtx, {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
      datasets: [{
        label: 'Sales (ETB)',
        data: [6500, 5900, 8000, 8100, 10500, 12450],
        backgroundColor: 'rgba(220, 38, 38, 0.1)',
        borderColor: 'rgb(220, 38, 38)',
        borderWidth: 2,
        tension: 0.1,
        fill: true
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });

  // Traffic Chart
  const trafficCtx = document.getElementById('trafficChart').getContext('2d');
  const trafficChart = new Chart(trafficCtx, {
    type: 'doughnut',
    data: {
      labels: ['Direct', 'Social Media', 'Marketplace', 'Search Engines'],
      datasets: [{
        data: [45, 25, 20, 10],
        backgroundColor: [
          'rgb(220, 38, 38)',
          'rgb(16, 185, 129)',
          'rgb(59, 130, 246)',
          'rgb(245, 158, 11)'
        ],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          position: 'right'
        }
      }
    }
  });
  </script>
</body>

</html>