<?php
include '../dbConnection/dbConnection.php';

?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Earnings Dashboard</title>
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
        <a href="#" class="px-6 py-4 font-medium text-gray-600 hover:text-red-600">Earnings</a>

        <a href="#" class="px-6 py-4 font-medium text-gray-600 hover:text-red-600">Messages</a>
      </nav>
    </div>
  </div>
  <!-- Main Content -->
  <main class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-wallet text-red-600 mr-2"></i> Earnings Dashboard
      </h1>
      <div class="flex items-center space-x-4">
        <button class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium">
          <i class="fas fa-download mr-2"></i> Download Report
        </button>
        <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium">
          <i class="fas fa-money-bill-wave mr-2"></i> Request Payout
        </button>
      </div>
    </div>

    <!-- Earnings Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500">Available Balance</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1">ETB 8,950</h3>
          </div>
          <div class="p-3 bg-green-100 rounded-lg text-green-800">
            <i class="fas fa-coins text-2xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500">Pending Clearance</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1">ETB 3,500</h3>
          </div>
          <div class="p-3 bg-yellow-100 rounded-lg text-yellow-800">
            <i class="fas fa-clock text-2xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500">Total Earned</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1">ETB 42,150</h3>
          </div>
          <div class="p-3 bg-blue-100 rounded-lg text-blue-800">
            <i class="fas fa-chart-line text-2xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Earnings Chart -->
    <div class="bg-white rounded-lg shadow p-4 mb-8 max-w-2xl mx-auto">
      <h2 class="text-lg font-medium text-gray-800 mb-4">Earnings Overview</h2>
      <div class="w-full overflow-x-auto">
        <canvas id="earningsChart" height="200" width="400" style="max-width:100%;"></canvas>
      </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-800">Recent Transactions</h2>
      </div>
      <div class="divide-y divide-gray-200">
        <div class="p-4 hover:bg-gray-50">
          <div class="flex justify-between items-center">
            <div>
              <h3 class="font-medium">Order #ORD-1245</h3>
              <p class="text-sm text-gray-600">Jun 15, 2023 • 3 items</p>
            </div>
            <div class="text-right">
              <p class="font-medium text-green-600">+ ETB 1,250</p>
              <p class="text-sm text-gray-500">Completed</p>
            </div>
          </div>
        </div>
        <div class="p-4 hover:bg-gray-50">
          <div class="flex justify-between items-center">
            <div>
              <h3 class="font-medium">Order #ORD-1244</h3>
              <p class="text-sm text-gray-600">Jun 14, 2023 • 2 items</p>
            </div>
            <div class="text-right">
              <p class="font-medium text-green-600">+ ETB 850</p>
              <p class="text-sm text-gray-500">Completed</p>
            </div>
          </div>
        </div>
        <div class="p-4 hover:bg-gray-50">
          <div class="flex justify-between items-center">
            <div>
              <h3 class="font-medium">Payout to Bank</h3>
              <p class="text-sm text-gray-600">Jun 10, 2023 • Transfer</p>
            </div>
            <div class="text-right">
              <p class="font-medium text-red-600">- ETB 5,000</p>
              <p class="text-sm text-gray-500">Processed</p>
            </div>
          </div>
        </div>
      </div>
      <div class="px-6 py-4 border-t border-gray-200 text-center">
        <a href="#" class="text-red-600 hover:text-red-700 font-medium">
          View All Transactions <i class="fas fa-arrow-right ml-1"></i>
        </a>
      </div>
    </div>
  </main>

  <?php
include '../common/footer.php';
 ?>


  <script>
  // Earnings Chart
  const earningsCtx = document.getElementById('earningsChart').getContext('2d');
  const earningsChart = new Chart(earningsCtx, {
    type: 'bar',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
      datasets: [{
        label: 'Earnings (ETB)',
        data: [6500, 5900, 8000, 8100, 10500, 12450],
        backgroundColor: 'rgba(220, 38, 38, 0.7)',
        borderColor: 'rgb(220, 38, 38)',
        borderWidth: 1
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
  </script>
</body>

</html>