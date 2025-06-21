<?php
session_start();
include '../Artisan and Story teller/dbConnection/dbConnection.php';

// Demo user ID (in real app, get from session)
$user_id = 1;

// Get orders for the user
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Oromo Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        #orderModalContent {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }
        
        #orderModalContent::-webkit-scrollbar {
            width: 8px;
        }
        
        #orderModalContent::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 4px;
        }
        
        #orderModalContent::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }
        
        #orderModalContent::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-yellow-50 min-h-screen">
    <?php include '../common/commonHEADER.php'; ?>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-blue-900 mb-2">
                    <i class="fas fa-history text-blue-600 mr-3"></i>Order History
                </h1>
                <p class="text-gray-600">Track your orders and view order details</p>
            </div>

            <?php if (empty($orders)): ?>
                <!-- Empty State -->
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <div class="text-6xl text-gray-300 mb-4">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Orders Yet</h3>
                    <p class="text-gray-500 mb-6">Start shopping to see your order history here</p>
                    <a href="/Customer dashboard/products.php" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-shopping-cart mr-2"></i>Browse Products
                    </a>
                </div>
            <?php else: ?>
                <!-- Orders List -->
                <div class="space-y-4">
                    <?php foreach ($orders as $order): ?>
                        <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <!-- Order Info -->
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-3">
                                        <h3 class="text-lg font-semibold text-gray-800">
                                            Order #<?= htmlspecialchars($order['order_number']) ?>
                                        </h3>
                                        <span class="px-3 py-1 rounded-full text-sm font-medium
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
                                    
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm text-gray-600">
                                        <div>
                                            <span class="font-medium">Date:</span><br>
                                            <?= date('M d, Y', strtotime($order['created_at'])) ?>
                                        </div>
                                        <div>
                                            <span class="font-medium">Total:</span><br>
                                            <span class="font-semibold text-green-600">ETB <?= number_format($order['total_amount'], 2) ?></span>
                                        </div>
                                        <div>
                                            <span class="font-medium">Payment:</span><br>
                                            <span class="font-medium 
                                                <?= $order['payment_status'] === 'paid' ? 'text-green-600' : ($order['payment_status'] === 'pending' ? 'text-yellow-600' : 'text-red-600') ?>">
                                                <?= ucfirst(htmlspecialchars($order['payment_status'])) ?>
                                            </span>
                                        </div>
                                        <div>
                                            <span class="font-medium">Method:</span><br>
                                            <?= htmlspecialchars($order['payment_method']) ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="mt-4 md:mt-0 md:ml-6 flex flex-col sm:flex-row gap-2">
                                    <button onclick="viewOrderDetails(<?= $order['id'] ?>)" 
                                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                        <i class="fas fa-eye mr-2"></i>View Details
                                    </button>
                                    <?php if ($order['payment_status'] === 'pending'): ?>
                                        <button onclick="completePayment(<?= $order['id'] ?>)" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                            <i class="fas fa-credit-card mr-2"></i>Complete Payment
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($order['status'] === 'delivered'): ?>
                                        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                            <i class="fas fa-star mr-2"></i>Review
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($order['status'] === 'shipped'): ?>
                                        <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                            <i class="fas fa-truck mr-2"></i>Track
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div id="orderModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[85vh] flex flex-col">
                <div class="p-6 border-b border-gray-200 flex-shrink-0">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-gray-800">Order Details</h3>
                        <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                <div id="orderModalContent" class="flex-1 overflow-y-auto p-6" style="max-height: calc(85vh - 120px);">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <?php include '../common/footer.php'; ?>

    <script>
        function viewOrderDetails(orderId) {
            // Prevent background scroll
            document.body.style.overflow = 'hidden';
            
            // Show loading
            document.getElementById('orderModalContent').innerHTML = `
                <div class="text-center py-8">
                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                    <p class="mt-2 text-gray-600">Loading order details...</p>
                </div>
            `;
            document.getElementById('orderModal').classList.remove('hidden');

            // Fetch order details
            fetch('orderItemsAjax.php?order_id=' + orderId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('orderModalContent').innerHTML = data;
                    // Ensure scroll is at top
                    document.getElementById('orderModalContent').scrollTop = 0;
                })
                .catch(error => {
                    document.getElementById('orderModalContent').innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-exclamation-triangle text-red-500 text-3xl mb-4"></i>
                            <p class="text-red-600">Error loading order details</p>
                        </div>
                    `;
                });
        }

        function closeOrderModal() {
            document.getElementById('orderModal').classList.add('hidden');
            // Restore background scroll
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        document.getElementById('orderModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeOrderModal();
            }
        });

        // Prevent modal content clicks from closing modal
        document.getElementById('orderModalContent').addEventListener('click', function(e) {
            e.stopPropagation();
        });

        function completePayment(orderId) {
            if (confirm('Do you want to complete the payment for this order?')) {
                // Redirect to checkout with order ID
                window.location.href = 'completePayment.php?order_id=' + orderId;
            }
        }
    </script>
</body>
</html>