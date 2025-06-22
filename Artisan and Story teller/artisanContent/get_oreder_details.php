<?php
include '../dbConnection/dbConnection.php';

if (isset($_GET['order_id'])) {
    $orderId = $_GET['order_id'];
    $sql = "SELECT * FROM orders WHERE order_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    
    if ($order) {
        ?>
<input type="hidden" id="orderIdInModal" value="<?php echo $order['order_id']; ?>">
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
  <div>
    <h4 class="font-semibold">Order Information</h4>
    <p><span class="font-medium">Order ID:</span> #ORD-<?php echo $order['order_id']; ?></p>
    <p><span class="font-medium">Customer ID:</span> <?php echo $order['customer_id']; ?></p>
    <p><span class="font-medium">Date:</span> <?php echo date('M j, Y H:i', strtotime($order['date'])); ?></p>
  </div>
  <div>
    <h4 class="font-semibold">Order Summary</h4>
    <p><span class="font-medium">Items:</span> <?php echo $order['items']; ?></p>
    <p><span class="font-medium">Total:</span> ETB <?php echo number_format($order['total']); ?></p>
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
    } else {
        echo '<p class="text-red-500">Order not found.</p>';
    }
} else {
    echo '<p class="text-red-500">No order ID specified.</p>';
}