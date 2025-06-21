<?php
include '../Artisan and Story teller/dbConnection/dbConnection.php';

if (!isset($_GET['order_id'])) {
    echo '<div class="text-center py-8 text-red-600">Order ID required</div>';
    exit;
}

$order_id = (int)$_GET['order_id'];
$user_id = 1; // Demo user ID

// Get order details
$sql = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo '<div class="text-center py-8 text-red-600">Order not found</div>';
    exit;
}

// Get order items (both products and stories)
$sql = "SELECT oi.*, 
        CASE 
            WHEN oi.type = 'product' THEN p.name 
            WHEN oi.type = 'story' THEN s.title 
        END as item_name,
        CASE 
            WHEN oi.type = 'product' THEN p.category 
            WHEN oi.type = 'story' THEN s.category 
        END as item_category,
        CASE 
            WHEN oi.type = 'product' THEN p.location 
            WHEN oi.type = 'story' THEN s.storyteller 
        END as item_secondary
        FROM order_items oi 
        LEFT JOIN products p ON oi.item_id = p.id AND oi.type = 'product'
        LEFT JOIN stories s ON oi.item_id = s.id AND oi.type = 'story'
        WHERE oi.order_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!-- Order Summary -->
<div class="mb-6 p-4 bg-gray-50 rounded-lg">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
        <div>
            <span class="font-medium text-gray-600">Order Number:</span><br>
            <span class="font-semibold"><?= htmlspecialchars($order['order_number']) ?></span>
        </div>
        <div>
            <span class="font-medium text-gray-600">Order Date:</span><br>
            <span class="font-semibold"><?= date('M d, Y g:i A', strtotime($order['created_at'])) ?></span>
        </div>
        <div>
            <span class="font-medium text-gray-600">Status:</span><br>
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
        <div>
            <span class="font-medium text-gray-600">Total Amount:</span><br>
            <span class="font-semibold text-green-600">ETB <?= number_format($order['total_amount'], 2) ?></span>
        </div>
    </div>
</div>

<!-- Shipping Information -->
<div class="mb-6">
    <h4 class="font-semibold text-gray-800 mb-3">
        <i class="fas fa-shipping-fast text-blue-600 mr-2"></i>Shipping Information
    </h4>
    <div class="bg-gray-50 rounded-lg p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium text-gray-600">Address:</span><br>
                <span class="break-words"><?= htmlspecialchars($order['shipping_address']) ?></span>
            </div>
            <div>
                <span class="font-medium text-gray-600">City:</span><br>
                <span><?= htmlspecialchars($order['shipping_city']) ?></span>
            </div>
            <div>
                <span class="font-medium text-gray-600">Phone:</span><br>
                <span><?= htmlspecialchars($order['shipping_phone']) ?></span>
            </div>
            <div>
                <span class="font-medium text-gray-600">Payment Method:</span><br>
                <span><?= htmlspecialchars($order['payment_method']) ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Order Items -->
<div class="mb-6">
    <h4 class="font-semibold text-gray-800 mb-3">
        <i class="fas fa-box text-blue-600 mr-2"></i>Order Items (<?= count($items) ?>)
    </h4>
    <div class="space-y-3">
        <?php foreach ($items as $item): ?>
            <div class="flex flex-col sm:flex-row items-start sm:items-center p-3 bg-gray-50 rounded-lg gap-3">
                <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=100&q=80" 
                     alt="<?= htmlspecialchars($item['item_name']) ?>" 
                     class="w-16 h-16 object-cover rounded-lg flex-shrink-0">
                <div class="flex-1 min-w-0">
                    <h5 class="font-medium text-gray-800 truncate"><?= htmlspecialchars($item['item_name']) ?></h5>
                    <p class="text-sm text-gray-600"><?= htmlspecialchars($item['item_category']) ?></p>
                    <p class="text-sm text-gray-500">
                        <?php if ($item['type'] === 'product'): ?>
                            Location: <?= htmlspecialchars($item['item_secondary']) ?>
                        <?php else: ?>
                            Storyteller: <?= htmlspecialchars($item['item_secondary']) ?>
                        <?php endif; ?>
                    </p>
                    <p class="text-sm text-gray-500">Quantity: <?= $item['quantity'] ?></p>
                    <span class="text-xs px-2 py-1 rounded <?= $item['type'] === 'story' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' ?>">
                        <?= ucfirst($item['type']) ?>
                    </span>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="font-medium text-gray-800">ETB <?= number_format($item['unit_price'], 2) ?></p>
                    <p class="text-sm text-gray-600">Total: ETB <?= number_format($item['total_price'], 2) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Order Summary -->
<div class="border-t border-gray-200 pt-4 mb-6">
    <div class="flex justify-between items-center">
        <span class="text-lg font-semibold text-gray-800">Total:</span>
        <span class="text-xl font-bold text-green-600">ETB <?= number_format($order['total_amount'], 2) ?></span>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex flex-wrap gap-3 pb-4">
    <?php if ($order['payment_status'] === 'pending'): ?>
        <button onclick="window.location.href='completePayment.php?order_id=<?= $order['id'] ?>'" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-credit-card mr-2"></i>Complete Payment
        </button>
    <?php endif; ?>
    
    <?php if ($order['status'] === 'delivered'): ?>
        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-star mr-2"></i>Leave Review
        </button>
    <?php endif; ?>
    
    <?php if ($order['status'] === 'shipped'): ?>
        <button class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-truck mr-2"></i>Track Package
        </button>
    <?php endif; ?>
    
    <?php if ($order['status'] === 'pending'): ?>
        <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-times mr-2"></i>Cancel Order
        </button>
    <?php endif; ?>
    
    <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
        <i class="fas fa-print mr-2"></i>Print Receipt
    </button>
</div>