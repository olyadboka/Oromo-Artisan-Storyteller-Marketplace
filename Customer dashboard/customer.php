<?php
session_start();
include '../Artisan and Story teller/dbConnection/dbConnection.php';
// $_SESSION['user_id'] = 8;
// Demo user data (in real app, get from session)
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['username'];
$user_email = $_SESSION['email'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Customer Dashboard - Oromo Marketplace</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-blue-50 to-yellow-50 min-h-screen">
  <?php include '../common/header.php'; ?>

  <div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
      <!-- Dashboard Header -->
      <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
          <div class="flex items-center mb-4 md:mb-0">
            <div
              class="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mr-4">
              <?= strtoupper(substr($user_name, 0, 1)) ?>
            </div>
            <div>
              <h1 class="text-2xl font-bold text-gray-800">Welcome back, <?= htmlspecialchars($user_name) ?>!</h1>
              <p class="text-gray-600"><?= htmlspecialchars($user_email) ?></p>
            </div>
          </div>
          <div class="flex space-x-3">
            <a href="products.php"
              class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
              <i class="fas fa-shopping-cart mr-2"></i>Shop Now
            </a>
            <a href="../User managemen/logout.php"
              class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
              <i class="fas fa-sign-out-alt mr-2"></i>Logout
            </a>
          </div>
        </div>
      </div>

      <!-- Navigation Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Order History -->
        <a href="orderHistory.php" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow group">
          <div class="text-blue-600 mb-4">
            <i class="fas fa-history text-3xl group-hover:scale-110 transition-transform"></i>
          </div>
          <h3 class="text-lg font-semibold text-gray-800 mb-2">Order History</h3>
          <p class="text-gray-600 text-sm">View and track your past orders</p>
        </a>

        <!-- Shopping Cart -->
        <a href="cart.php" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow group">
          <div class="text-green-600 mb-4">
            <i class="fas fa-shopping-cart text-3xl group-hover:scale-110 transition-transform"></i>
          </div>
          <h3 class="text-lg font-semibold text-gray-800 mb-2">Shopping Cart</h3>
          <p class="text-gray-600 text-sm">Manage your cart items</p>
        </a>

        <!-- Story Library -->
        <a href="storyLibrary.php" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow group">
          <div class="text-purple-600 mb-4">
            <i class="fas fa-book-open text-3xl group-hover:scale-110 transition-transform"></i>
          </div>
          <h3 class="text-lg font-semibold text-gray-800 mb-2">Story Library</h3>
          <p class="text-gray-600 text-sm">Explore Oromo stories and folklore</p>
        </a>

        <!-- Browse Products -->
        <a href="products.php" class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow group">
          <div class="text-orange-600 mb-4">
            <i class="fas fa-store text-3xl group-hover:scale-110 transition-transform"></i>
          </div>
          <h3 class="text-lg font-semibold text-gray-800 mb-2">Browse Products</h3>
          <p class="text-gray-600 text-sm">Discover artisan products</p>
        </a>
      </div>

      <!-- Quick Stats -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <?php
                // Get order stats
                $sql = "SELECT 
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
                    SUM(total_amount) as total_spent
                    FROM orders WHERE user_id = ?";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stats = $stmt->get_result()->fetch_assoc();
                ?>

        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg mr-4">
              <i class="fas fa-shopping-bag text-blue-600 text-xl"></i>
            </div>
            <div>
              <p class="text-gray-600 text-sm">Total Orders</p>
              <p class="text-2xl font-bold text-gray-800"><?= $stats['total_orders'] ?? 0 ?></p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg mr-4">
              <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <div>
              <p class="text-gray-600 text-sm">Delivered</p>
              <p class="text-2xl font-bold text-gray-800"><?= $stats['delivered_orders'] ?? 0 ?></p>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
          <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-lg mr-4">
              <i class="fas fa-coins text-yellow-600 text-xl"></i>
            </div>
            <div>
              <p class="text-gray-600 text-sm">Total Spent</p>
              <p class="text-2xl font-bold text-gray-800">ETB <?= number_format($stats['total_spent'] ?? 0, 2) ?></p>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Orders -->
      <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold text-gray-800">Recent Orders</h2>
          <a href="orderHistory.php" class="text-blue-600 hover:text-blue-700 font-medium">View All</a>
        </div>

        <?php
                $sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 3";
                $stmt = $con->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $recent_orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
                ?>

        <?php if (empty($recent_orders)): ?>
        <div class="text-center py-8 text-gray-500">
          <i class="fas fa-shopping-bag text-4xl mb-4"></i>
          <p>No orders yet. Start shopping!</p>
        </div>
        <?php else: ?>
        <div class="space-y-4">
          <?php foreach ($recent_orders as $order): ?>
          <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
            <div>
              <h4 class="font-medium text-gray-800"><?= htmlspecialchars($order['order_number']) ?></h4>
              <p class="text-sm text-gray-600"><?= date('M d, Y', strtotime($order['created_at'])) ?></p>
            </div>
            <div class="text-right">
              <p class="font-semibold text-gray-800">ETB <?= number_format($order['total_amount'], 2) ?></p>
              <span class="px-2 py-1 rounded-full text-xs font-medium
                                        <?php
                                        switch($order['status']) {
                                            case 'pending': echo 'bg-yellow-100 text-yellow-800'; break;
                                            case 'processing': echo 'bg-blue-100 text-blue-800'; break;
                                            case 'shipped': echo 'bg-purple-100 text-purple-800'; break;
                                            case 'delivered': echo 'bg-green-100 text-green-800'; break;
                                            case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                        }
                                        ?>">
                <?= ucfirst(htmlspecialchars($order['status'])) ?>
              </span>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <?php include '../common/footer.php'; ?>
</body>

</html>