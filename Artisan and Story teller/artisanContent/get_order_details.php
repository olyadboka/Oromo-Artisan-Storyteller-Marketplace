<?php
session_start();
include '../dbConnection/dbConnection.php';

header('Content-Type: text/html');

if (!isset($_GET['order_number']) || empty($_GET['order_number'])) {
    echo '<p class="text-red-500">Order number is required</p>';
    exit();
}
$orderNumber = filter_var($_GET['order_number'], FILTER_SANITIZE_STRING);
$artisanId = $_SESSION['artisan_id'] ?? null;

if (!$artisanId) {
    echo '<p class="text-red-500">Authentication required</p>';
    exit();
}

try {
    // Get order details
    $sql = "SELECT o.* 
            FROM orders o
            JOIN order_items oi ON o.order_number = oi.order_number
            JOIN products p ON oi.product_id = p.id
            WHERE o.order_number = ? AND p.artisan_id = ?
            LIMIT 1";
    
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $orderNumber, $artisanId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $order = mysqli_fetch_assoc($result);

    if (!$order) {
        echo '<p class="text-red-500">Order not found or access denied</p>';
        exit();
    }

    // Get order items
    $itemsSql = "SELECT oi.*, p.name, p.price, p.pImage1 
                 FROM order_items oi
                 JOIN products p ON oi.product_id = p.id
                 WHERE oi.order_number = ? AND p.artisan_id = ?";
    
    $itemsStmt = mysqli_prepare($con, $itemsSql);
    mysqli_stmt_bind_param($itemsStmt, "si", $orderNumber, $artisanId);
    mysqli_stmt_execute($itemsStmt);
    $itemsResult = mysqli_stmt_get_result($itemsStmt);
    $items = [];
    
    while ($item = mysqli_fetch_assoc($itemsResult)) {
        $items[] = $item;
    }

    // Output the order details
    ?>
<input type="hidden" id="orderIdInModal" value="<?php echo htmlspecialchars($order['order_number']); ?>">

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
  <div>
    <h4 class="font-semibold">Order Information</h4>
    <p><span class="font-medium">Order #:</span> <?php echo htmlspecialchars($order['order_number']); ?></p>
    <p><span class="font-medium">Customer ID:</span> <?php echo htmlspecialchars($order['user_id']); ?></p>
    <p><span class="font-medium">Date:</span> <?php echo date('M j, Y H:i', strtotime($order['date'])); ?></p>
  </div>
  <div>
    <h4 class="font-semibold">Order Summary</h4>
    <p><span class="font-medium">Status:</span>
      <span class="px-2 py-1 text-xs font-semibold rounded-full 
            <?php 
            $statusClasses = [
                'Pending' => 'bg-yellow-100 text-yellow-800',
                'Processing' => 'bg-blue-100 text-blue-800',
                'Shipped' => 'bg-green-100 text-green-800',
                'Completed' => 'bg-purple-100 text-purple-800',
                'Cancelled' => 'bg-red-100 text-red-800'
            ];
            echo $statusClasses[$order['status']] ?? 'bg-gray-100 text-gray-800';
            ?>">
        <?php echo htmlspecialchars($order['status']); ?>
      </span>
    </p>
    <p><span class="font-medium">Total:</span> ETB <?php echo number_format($order['total_amount'], 2); ?></p>
  </div>
</div>

<div class="mb-6">
  <h4 class="font-semibold mb-2">Order Items</h4>
  <div class="space-y-4">
    <?php foreach ($items as $item): ?>
    <div class="flex items-start border-b pb-4">
      <div class="flex-shrink-0 h-16 w-16 rounded-md overflow-hidden">
        <?php if ($item['pImage1']): ?>
        <img src="../uploads/products/<?php echo htmlspecialchars($item['pImage1']); ?>"
          alt="<?php echo htmlspecialchars($item['name']); ?>" class="h-full w-full object-cover">
        <?php else: ?>
        <div class="h-full w-full bg-gray-200 flex items-center justify-center">
          <i class="fas fa-image text-gray-400"></i>
        </div>
        <?php endif; ?>
      </div>
      <div class="ml-4 flex-1">
        <h5 class="font-medium"><?php echo htmlspecialchars($item['name']); ?></h5>
        <p class="text-sm text-gray-600">Quantity: <?php echo $item['quantity']; ?></p>
        <p class="text-sm text-gray-600">ETB <?php echo number_format($item['price'], 2); ?> each</p>
      </div>
      <div class="ml-4">
        <p class="font-medium">ETB <?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="mb-4">
  <h4 class="font-semibold">Update Status</h4>
  <select id="statusSelect"
    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-red-500 focus:border-red-500 sm:text-sm rounded-md">
    <option value="Pending" <?php echo $order['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
    <option value="Processing" <?php echo $order['status'] == 'Processing' ? 'selected' : ''; ?>>Processing</option>
    <option value="Shipped" <?php echo $order['status'] == 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
    <option value="Completed" <?php echo $order['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
    <option value="Cancelled" <?php echo $order['status'] == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
  </select>
</div>
<?php
} catch (Exception $e) {
    echo '<p class="text-red-500">Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
?>