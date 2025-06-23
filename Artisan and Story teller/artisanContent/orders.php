<?php
session_start();
include '../dbConnection/dbConnection.php';


// $_SESSION['artisan_id'] = 1;

$artisan_id = $_SESSION['artisan_id'];

// Get orders for this artisan
$sql = "SELECT o.* 
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.id = p.id
        WHERE p.artisan_id = $artisan_id
        GROUP BY o.id
        ORDER BY o.date DESC";
$result = mysqli_query($con, $sql);
$orders = [];
if ($result && mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $orders[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Order Management</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="./CSS/orders.css">
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
        <a href="#" class="px-6 py-4 font-medium text-gray-600 hover:text-red-600">Orders</a>
        <a href="./earning.php" class="px-6 py-4 font-medium text-gray-600 hover:text-red-600">Earnings</a>
      </nav>
    </div>
  </div>

  <!-- Main Content -->
  <main class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-2xl font-bold text-gray-800">
        <i class="fas fa-shopping-cart text-red-600 mr-2"></i> Order Management
      </h1>
      <div class="flex items-center space-x-4">
        <div class="relative">
          <select id="statusFilter"
            class="appearance-none border border-gray-300 rounded-lg px-4 py-2 pr-8 focus:ring-red-500 focus:border-red-500">
            <option value="">All Status</option>
            <option value="Pending">Pending</option>
            <option value="Processing">Processing</option>
            <option value="Shipped">Shipped</option>
            <option value="Completed">Completed</option>
            <option value="Cancelled">Cancelled</option>
          </select>
          <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
            <i class="fas fa-chevron-down"></i>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($orders as $order): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                <?php echo htmlspecialchars($order['order_number']); ?></td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                Customer #<?php echo htmlspecialchars($order['user_id']); ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <?php echo date('M j, Y', strtotime($order['date'])); ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <?php echo htmlspecialchars($order['items']); ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ETB <?php echo number_format($order['total_amount'], 2); ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <?php 
                $statusClasses = [
                    'Pending' => 'bg-yellow-100 text-yellow-800',
                    'Processing' => 'bg-blue-100 text-blue-800',
                    'Shipped' => 'bg-green-100 text-green-800',
                    'Completed' => 'bg-purple-100 text-purple-800',
                    'Cancelled' => 'bg-red-100 text-red-800'
                ];
                $statusClass = $statusClasses[$order['status']] ?? 'bg-gray-100 text-gray-800';
                ?>
                <span class="px-2 py-1 text-xs font-semibold rounded-full <?php echo $statusClass; ?>">
                  <?php echo htmlspecialchars($order['status']); ?>
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <?php if ($order['status'] == 'Pending'): ?>
                <button class="text-red-600 hover:text-red-900 mr-3 process-btn"
                  data-order-number="<?php echo $order['order_number']; ?>">
                  <i class="fas fa-check"></i> Process
                </button>
                <?php elseif ($order['status'] == 'Processing'): ?>
                <button class="text-red-600 hover:text-red-900 mr-3 ship-btn"
                  data-order-number="<?php echo $order['order_number']; ?>">
                  <i class="fas fa-truck"></i> Ship
                </button>
                <?php endif; ?>
                <button class="text-gray-600 hover:text-gray-900 view-btn"
                  data-order-number="<?php echo $order['order_number']; ?>">
                  <i class="fas fa-eye"></i> View
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <div class="flex-1 flex justify-between sm:hidden">
          <a href="#"
            class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Previous
          </a>
          <a href="#"
            class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            Next
          </a>
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
          <div>
            <p class="text-sm text-gray-700">
              Showing <span class="font-medium">1</span> to <span
                class="font-medium"><?php echo count($orders); ?></span> of <span
                class="font-medium"><?php echo count($orders); ?></span> orders
            </p>
          </div>
          <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
              <a href="#"
                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <span class="sr-only">Previous</span>
                <i class="fas fa-chevron-left"></i>
              </a>
              <a href="#" aria-current="page"
                class="z-10 bg-red-50 border-red-500 text-red-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                1
              </a>
              <a href="#"
                class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                2
              </a>
              <a href="#"
                class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                3
              </a>
              <a href="#"
                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                <span class="sr-only">Next</span>
                <i class="fas fa-chevron-right"></i>
              </a>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Order Details Modal -->
  <div id="orderModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="flex justify-between items-center border-b pb-3">
        <h3 class="text-lg font-semibold text-gray-800">Order Details</h3>
        <button id="closeModal" class="text-gray-500 hover:text-gray-700">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="py-4">
        <div id="orderDetailsContent"></div>
      </div>
      <div class="flex justify-end pt-2 border-t">
        <button id="saveStatusBtn" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
          Save Changes
        </button>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
  $(document).ready(function() {
    // View order details
    $(".view-btn").click(function() {
      const orderNumber = $(this).data("order-number");
      $("#orderModal").removeClass("hidden");

      $.ajax({
        url: "get_order_details.php",
        type: "GET",
        data: {
          order_number: orderNumber
        },
        success: function(response) {
          $("#orderDetailsContent").html(response);
        },
        error: function() {
          $("#orderDetailsContent").html(
            '<p class="text-red-500">Error loading order details.</p>'
          );
        }
      });
    });

    // Close modal
    $("#closeModal").click(function() {
      $("#orderModal").addClass("hidden");
    });

    // Process order
    $(".process-btn").click(function() {
      const orderNumber = $(this).data("order-number");
      updateOrderStatus(orderNumber, "Processing");
    });

    // Ship order
    $(".ship-btn").click(function() {
      const orderNumber = $(this).data("order-number");
      updateOrderStatus(orderNumber, "Shipped");
    });

    // Save status changes
    $("#saveStatusBtn").click(function() {
      const orderNumber = $("#orderIdInModal").val();
      const newStatus = $("#statusSelect").val();
      updateOrderStatus(orderNumber, newStatus);
    });

    // Filter orders by status
    $("#statusFilter").change(function() {
      const status = $(this).val();
      window.location.href = `orders.php?status=${status}`;
    });

    // Function to update order status
    function updateOrderStatus(orderNumber, newStatus) {
      $.ajax({
        url: "update_order_status.php",
        type: "POST",
        data: {
          order_number: orderNumber,
          new_status: newStatus
        },
        success: function(response) {
          if (response.success) {
            alert("Order status updated successfully!");
            location.reload();
          } else {
            alert("Error updating order status: " + response.message);
          }
        },
        error: function() {
          alert("Error updating order status. Please try again.");
        }
      });
    }
  });
  </script>

  <?php include '../common/footer.php'; ?>
</body>

</html>