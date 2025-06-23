<?php
// Database connection
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "oromo_artisan_and_storyteller";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $conn->exec("SET time_zone = '+03:00'");
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}

// Assume logged-in user (replace with proper authentication)
$user_id = $_SESSION['user_id'];// Jirenya Dhugaa for testing

// Fetch storyteller data
$stmt = $conn->prepare("
    SELECT s.*, u.username 
    FROM storytellers s 
    JOIN users u ON s.user_id = u.id 
    WHERE s.user_id = :user_id
");
$stmt->execute(['user_id' => $user_id]);
$storyteller = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$storyteller) {
  die("Storyteller not found for user ID: $user_id");
}

// Fetch earnings data
$total_earnings = 0;
$current_period_earnings = 0;
$earnings_data = [];
$transactions = [];

try {
  // Total earnings
  $stmt = $conn->prepare("
        SELECT COALESCE(SUM(amount), 0) as total 
        FROM earnings 
        WHERE storyteller_id = :storyteller_id
    ");
  $stmt->execute(['storyteller_id' => $storyteller['id']]);
  $total_earnings = $stmt->fetchColumn();

  // Current period earnings (last 30 days)
  $current_period_start = date('Y-m-d', strtotime('-30 days'));
  $stmt = $conn->prepare("
        SELECT COALESCE(SUM(amount), 0) as current 
        FROM earnings 
        WHERE storyteller_id = :storyteller_id
        AND period_end >= :period_start
    ");
  $stmt->execute([
    'storyteller_id' => $storyteller['id'],
    'period_start' => $current_period_start
  ]);
  $current_period_earnings = $stmt->fetchColumn();

  // Earnings for last 6 months for chart
  for ($i = 5; $i >= 0; $i--) {
    $month_start = date('Y-m-01', strtotime("-$i months"));
    $month_end = date('Y-m-t', strtotime("-$i months"));
    $month_name = date('M Y', strtotime($month_start));

    $stmt = $conn->prepare("
            SELECT COALESCE(SUM(amount), 0) as total
            FROM earnings
            WHERE storyteller_id = :storyteller_id
            AND period_start >= :month_start
            AND period_end <= :month_end
        ");
    $stmt->execute([
      'storyteller_id' => $storyteller['id'],
      'month_start' => $month_start,
      'month_end' => $month_end
    ]);
    $earnings_data[$month_name] = $stmt->fetchColumn();
  }

  // Recent transactions (last 5)
  $stmt = $conn->prepare("
        SELECT e.*, 
               CONCAT('Period: ', DATE_FORMAT(period_start, '%b %d'), ' - ', DATE_FORMAT(period_end, '%b %d')) as period_name
        FROM earnings e
        WHERE e.storyteller_id = :storyteller_id
        ORDER BY e.period_end DESC
        LIMIT 5
    ");
  $stmt->execute(['storyteller_id' => $storyteller['id']]);
  $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Error fetching earnings data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Earnings - Oromo Storyteller Network</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
  <style>
  .storyteller-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #7c2d12 100%);
  }

  .earning-card {
    transition: transform 0.2s;
  }

  .earning-card:hover {
    transform: translateY(-5px);
  }
  </style>
</head>

<body class="bg-gray-50">
  <!-- Dashboard Header -->
  <header class="storyteller-header text-white">
    <div class="container mx-auto px-4 py-8">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
        <div class="flex items-center space-x-6 mb-6 md:mb-0">
          <img
            src="<?php echo htmlspecialchars($storyteller['profile_image_url'] ?? 'uploads/profiles/default-profile.jpg'); ?>"
            alt="Storyteller"
            class="w-20 h-20 rounded-full border-4 border-white border-opacity-30 object-cover shadow-lg">
          <div>
            <h1 class="text-3xl font-bold">
              <?php echo htmlspecialchars($storyteller['artistic_name'] ?? 'Unknown Storyteller'); ?></h1>
            <p class="text-white text-opacity-80 flex items-center">
              <i class="fas fa-map-marker-alt mr-2"></i>
              <?php echo htmlspecialchars($storyteller['location'] ?? 'Unknown Location'); ?>
              <span class="ml-4 px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm">
                <i class="fas fa-certificate mr-1"></i>
                <?php echo ucfirst($storyteller['verification_status'] ?? 'Pending') ?> Storykeeper
              </span>
            </p>
          </div>
        </div>
        <div class="flex space-x-4">
          <button class="px-5 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg transition">
            <i class="fas fa-cog mr-2"></i> as customer
          </button>
          <button class="px-5 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg transition">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
          </button>
        </div>
      </div>
    </div>
  </header>

  <!-- Dashboard Navigation -->
  <nav class="bg-white shadow-sm sticky top-0 z-10">
    <div class="container mx-auto px-4">
      <div class="flex overflow-x-auto">
        <a href="storytellers.php" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-home mr-2"></i> Dashboard
        </a>
        <a href="mystory.php" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-book-open mr-2"></i> My Stories
        </a>
        <a href="#" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-calendar-alt mr-2"></i> Events
        </a>
        <a href="community.php" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-comments mr-2"></i> Community
        </a>
        <a href="analytics.php" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-chart-line mr-2"></i> Analytics
        </a>
        <a href="earning.php" class="px-6 py-4 font-medium text-blue-800 border-b-2 border-blue-800">
          <i class="fas fa-wallet mr-2"></i> Earnings
        </a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-wallet text-blue-600 mr-2"></i> Earnings Dashboard
      </h1>
      <div class="flex items-center space-x-4">
        <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
          <i class="fas fa-download mr-2"></i> Download Report
        </button>
      </div>
    </div>

    <!-- Earnings Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow p-6 earning-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500">Current Period Earnings</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1">ETB
              <?php echo number_format($current_period_earnings, 2); ?></h3>
            <p class="text-sm text-gray-500 mt-2">Last 30 days</p>
          </div>
          <div class="p-3 bg-blue-100 rounded-lg text-blue-800">
            <i class="fas fa-calendar-week text-2xl"></i>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6 earning-card">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500">Total Earnings</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1">ETB <?php echo number_format($total_earnings, 2); ?></h3>
            <p class="text-sm text-gray-500 mt-2">All time</p>
          </div>
          <div class="p-3 bg-green-100 rounded-lg text-green-800">
            <i class="fas fa-chart-line text-2xl"></i>
          </div>
        </div>
      </div>
    </div>

    <!-- Earnings Chart -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
      <h2 class="text-lg font-medium text-gray-800 mb-4">Earnings Overview (Last 6 Months)</h2>
      <div class="w-full" style="height: 300px;">
        <canvas id="earningsChart"></canvas>
      </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-medium text-gray-800">Recent Earnings Periods</h2>
      </div>
      <div class="divide-y divide-gray-200">
        <?php if (empty($transactions)): ?>
        <div class="p-8 text-center">
          <i class="fas fa-exchange-alt text-4xl text-gray-300 mb-4"></i>
          <h3 class="text-lg font-medium text-gray-700">No earnings recorded yet</h3>
          <p class="text-gray-500 mt-2">Your earnings will appear here after your first payment period</p>
        </div>
        <?php else: ?>
        <?php foreach ($transactions as $transaction): ?>
        <div class="p-4 hover:bg-gray-50">
          <div class="flex justify-between items-center">
            <div>
              <h3 class="font-medium"><?php echo htmlspecialchars($transaction['period_name']); ?></h3>
              <p class="text-sm text-gray-600">
                Recorded on <?php echo date('M j, Y', strtotime($transaction['created_at'])); ?>
              </p>
            </div>
            <div class="text-right">
              <p class="font-medium text-green-600">
                + ETB <?php echo number_format($transaction['amount'], 2); ?>
              </p>
              <p class="text-sm text-gray-500">
                <?php echo date('M j', strtotime($transaction['period_start'])); ?> -
                <?php echo date('M j, Y', strtotime($transaction['period_end'])); ?>
              </p>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <div class="px-6 py-4 border-t border-gray-200 text-center">
        <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">
          View All Earnings Periods <i class="fas fa-arrow-right ml-1"></i>
        </a>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-gray-800 text-white py-8 mt-12">
    <div class="container mx-auto px-4">
      <div class="flex flex-col md:flex-row justify-between">
        <div class="mb-6 md:mb-0">
          <h3 class="text-xl font-bold mb-4">Oromo Storyteller Network</h3>
          <p class="text-gray-400">Preserving oral traditions for future generations</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
          <div>
            <h4 class="font-semibold mb-3">Resources</h4>
            <ul class="space-y-2">
              <li><a href="#" class="text-gray-400 hover:text-white">Recording Guide</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white">Storytelling Tips</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white">Cultural Database</a></li>
            </ul>
          </div>
          <div>
            <h4 class="font-semibold mb-3">Support</h4>
            <ul class="space-y-2">
              <li><a href="#" class="text-gray-400 hover:text-white">Help Center</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white">Community</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white">Contact Us</a></li>
            </ul>
          </div>
          <div>
            <h4 class="font-semibold mb-3">Connect</h4>
            <div class="flex space-x-4">
              <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f"></i></a>
              <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
              <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-youtube"></i></a>
            </div>
          </div>
        </div>
      </div>
      <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400 text-sm">
        © 2025 Oromo Artisan & Storyteller Marketplace. All rights reserved.
      </div>
    </div>
  </footer>

  <script>
  // Earnings Chart
  const earningsCtx = document.getElementById('earningsChart').getContext('2d');
  new Chart(earningsCtx, {
    type: 'bar',
    data: {
      labels: <?php echo json_encode(array_keys($earnings_data)); ?>,
      datasets: [{
        label: 'Earnings (ETB)',
        data: <?php echo json_encode(array_values($earnings_data)); ?>,
        backgroundColor: 'rgba(59, 130, 246, 0.7)',
        borderColor: 'rgba(59, 130, 246, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return 'ETB ' + value;
            }
          }
        }
      }
    }
  });
  </script>
</body>

</html>