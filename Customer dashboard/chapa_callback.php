<?php include '../common/header.php'; ?>
<?php
session_start();
include '../common/dbConnection.php';

// Chapa API Key (replace with your real secret key)
$chapa_api_key = 'CHASECK_TEST-khE3ePpSLfXh6vIghHq8f1yQdDyWHBB4'; 

// Demo user ID (in real app, get from session)
$user_id = 1;

// Check if this is a complete payment scenario
$is_complete_payment = isset($_GET['complete_payment']) && $_GET['complete_payment'] == '1';
$complete_payment_order = $_SESSION['complete_payment_order'] ?? null;

if ($is_complete_payment && $complete_payment_order) {
    // Use existing order data for complete payment
    $all_items = $complete_payment_order['items'];
    $total = $complete_payment_order['total_amount'];
    $existing_address = $complete_payment_order['shipping_address'];
    $existing_phone = $complete_payment_order['shipping_phone'];
} else {
    // Regular checkout from cart
    $cart = $_SESSION['cart'] ?? [];
    $story_cart = $_SESSION['story_cart'] ?? [];
    $products = [];
    $stories = [];
    $total = 0;

    if ($cart) {
        $ids = implode(',', array_keys($cart));
        $result = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids)");
        while ($row = mysqli_fetch_assoc($result)) {
            $row['qty'] = $cart[$row['id']];
            $row['subtotal'] = $row['qty'] * $row['price'];
            $row['type'] = 'product';
            $products[] = $row;
            $total += $row['subtotal'];
        }
    }

    if ($story_cart) {
        $story_ids = implode(',', array_keys($story_cart));
        $story_result = mysqli_query($conn, "SELECT * FROM stories WHERE id IN ($story_ids)");
        while ($row = mysqli_fetch_assoc($story_result)) {
            $row['qty'] = $story_cart[$row['id']];
            $row['subtotal'] = $row['qty'] * ($row['price'] ?? 0);
            $row['type'] = 'story';
            $stories[] = $row;
            $total += $row['subtotal'];
        }
    }

    $all_items = array_merge($products, $stories);
    $existing_address = '';
    $existing_phone = '';
}

// Server-side validation
$errors = [];
$orderSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pay'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if ($name === '') $errors[] = "Full Name is required.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid Email is required.";
    if (!preg_match('/^[0-9+\- ]{7,20}$/', $phone)) $errors[] = "A valid Phone Number is required.";
    if ($address === '') $errors[] = "Shipping Address is required.";

    if (!$errors) {
        // Prepare Chapa payment data
        $tx_ref = 'TX-' . uniqid();
        $callback_url = 'https://yourdomain.com/Customer%20dashboard/chapa_callback.php'; // TODO: Replace with your real callback URL

        $data = [
            'amount' => $total,
            'currency' => 'ETB',
            'email' => $email,
            'first_name' => $name,
            'phone_number' => $phone,
            'tx_ref' => $tx_ref,
            'callback_url' => $callback_url,
            'return_url' => $callback_url,
            'customization' => [
                'title' => 'Oromo Market',
                'description' => 'Order Payment'
            ]
        ];

        // Initialize cURL with SSL verification disabled (for development only)
        $ch = curl_init('https://api.chapa.co/v1/transaction/initialize');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $chapa_api_key,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        // Disable SSL verification (development only)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($err) {
            error_log('Chapa cURL Error: ' . $err);
            $errors[] = 'Payment initialization failed. Please try again or contact support.';
        } else {
            $result = json_decode($response, true);
            if (!$result) {
                error_log('Chapa API Invalid Response: ' . $response);
                $errors[] = 'Invalid response from payment gateway.';
            } elseif (isset($result['status']) && $result['status'] == 'success') {
                if ($is_complete_payment && $complete_payment_order) {
                    // Update existing order for complete payment
                    $order_id = $complete_payment_order['order_id'];
                    
                    // Store order info in session for callback
                    $_SESSION['pending_order'] = [
                        'order_id' => $order_id,
                        'order_number' => $complete_payment_order['order_number'],
                        'tx_ref' => $tx_ref
                    ];
                    
                    // Clear the complete payment session
                    unset($_SESSION['complete_payment_order']);
                    
                    // Redirect to Chapa payment page
                    header('Location: ' . $result['data']['checkout_url']);
                    exit;
                } else {
                    // Save new order to database
                    $order_number = 'ORD-' . date('Y') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
                    
                    // Insert order
                    $order_sql = "INSERT INTO orders (user_id, order_number, total_amount, status, shipping_address, shipping_city, shipping_phone, payment_method, payment_status) VALUES (?, ?, ?, 'pending', ?, ?, ?, ?, 'pending')";
                    $stmt = $conn->prepare($order_sql);
                    $city = 'Addis Ababa';
                    $payment_method = 'Chapa';
                    $stmt->bind_param("issssss", $user_id, $order_number, $total, $address, $city, $phone, $payment_method);
                    
                    if ($stmt->execute()) {
                        $order_id = $conn->insert_id;
                        
                        // Insert order items
                        foreach ($all_items as $item) {
                            $item_sql = "INSERT INTO order_items (order_id, item_id, quantity, unit_price, total_price, type) VALUES (?, ?, ?, ?, ?, ?)";
                            $item_stmt = $conn->prepare($item_sql);
                            $item_stmt->bind_param("iiidds", $order_id, $item['id'], $item['qty'], $item['price'], $item['subtotal'], $item['type']);
                            $item_stmt->execute();
                        }
                        
                        // Store order info in session for callback
                        $_SESSION['pending_order'] = [
                            'order_id' => $order_id,
                            'order_number' => $order_number,
                            'tx_ref' => $tx_ref
                        ];
                        
                        // Clear carts
                        $_SESSION['cart'] = [];
                        $_SESSION['story_cart'] = [];
                        
                        // Redirect to Chapa payment page
                        header('Location: ' . $result['data']['checkout_url']);
                        exit;
                    } else {
                        $errors[] = 'Failed to save order to database.';
                    }
                }
            } else {
                $msg = $result['message'] ?? 'Unknown error';
                error_log('Chapa API Error: ' . print_r($result, true));
                $errors[] = 'Payment failed: ' . (is_array($msg) ? implode(' ', $msg) : $msg);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>

<head>
  <title>Checkout</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-blue-50 to-yellow-50 min-h-screen">
  <div class="container mx-auto px-4 py-10">
    <h1 class="text-3xl font-extrabold text-center text-blue-900 mb-8 tracking-tight">
      <?= $is_complete_payment ? 'Complete Payment' : 'Checkout' ?>
    </h1>
    <?php if ($orderSuccess): ?>
    <div class="bg-green-100 border border-green-300 text-green-800 px-6 py-4 rounded-lg text-center mb-8">
      <i class="fas fa-check-circle"></i> Thank you! Your order has been placed.
    </div>
    <div class="text-center">
      <a href="products.php" class="text-blue-700 underline">Continue Shopping</a>
    </div>
    <?php elseif (!$all_items): ?>
    <div class="text-center text-gray-500 text-lg">Your cart is empty.
      <a href="products.php" class="text-blue-700 underline">Browse products</a> or
      <a href="storyLibrary.php" class="text-blue-700 underline">explore stories</a>
    </div>
    <?php else: ?>
    <div class="grid md:grid-cols-2 gap-8">
      <!-- Order Summary -->
      <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-xl font-bold mb-4 text-blue-900">
          <?= $is_complete_payment ? 'Order Summary (Complete Payment)' : 'Order Summary' ?>
        </h2>
        <?php if ($is_complete_payment): ?>
        <div class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
          <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
            <span class="text-yellow-800 font-medium">Order
              #<?= htmlspecialchars($complete_payment_order['order_number']) ?></span>
          </div>
          <p class="text-sm text-yellow-700 mt-1">Payment Status: Pending</p>
        </div>
        <?php endif; ?>
        <ul>
          <?php foreach ($all_items as $item): ?>
          <li class="flex justify-between items-center border-b py-2">
            <div>
              <span
                class="font-medium"><?= htmlspecialchars($item['name'] ?? $item['title'] ?? $item['item_name']) ?></span>
              <span class="text-xs text-gray-500">x<?= $item['qty'] ?? $item['quantity'] ?></span>
              <div class="text-xs text-gray-400">
                <?php if ($item['type'] === 'product'): ?>
                <?= htmlspecialchars($item['category'] ?? $item['item_category'] ?? '') ?> |
                <?= htmlspecialchars($item['location'] ?? $item['item_secondary'] ?? '') ?>
                <?php else: ?>
                <?= htmlspecialchars($item['category'] ?? $item['item_category'] ?? '') ?> |
                <?= htmlspecialchars($item['storyteller'] ?? $item['item_secondary'] ?? '') ?>
                <?php endif; ?>
              </div>
              <span
                class="text-xs px-2 py-1 rounded <?= $item['type'] === 'story' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' ?>">
                <?= ucfirst($item['type']) ?>
              </span>
            </div>
            <span class="font-semibold">ETB <?= number_format($item['subtotal'] ?? $item['total_price'], 2) ?></span>
          </li>
          <?php endforeach; ?>
        </ul>
        <div class="flex justify-between items-center mt-4 text-lg font-bold">
          <span>Total:</span>
          <span class="text-green-700">ETB <?= number_format($total, 2) ?></span>
        </div>
      </div>
      <!-- Customer Info & Payment -->
      <form method="post" class="bg-white rounded-xl shadow-lg p-6 flex flex-col gap-4" id="checkoutForm" novalidate>
        <h2 class="text-xl font-bold mb-4 text-blue-900">Customer Information</h2>
        <?php if ($errors): ?>
        <div class="bg-red-100 border border-red-300 text-red-800 px-4 py-2 rounded mb-2">
          <ul class="list-disc pl-5">
            <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
        <input type="text" name="name" id="name" placeholder="Full Name" required class="border rounded px-4 py-2"
          value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        <input type="email" name="email" id="email" placeholder="Email" required class="border rounded px-4 py-2"
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        <input type="text" name="phone" id="phone" placeholder="Phone Number" required class="border rounded px-4 py-2"
          value="<?= htmlspecialchars($_POST['phone'] ?? $existing_phone) ?>">
        <input type="text" name="address" id="address" placeholder="Shipping Address" required
          class="border rounded px-4 py-2" value="<?= htmlspecialchars($_POST['address'] ?? $existing_address) ?>">
        <button type="submit" name="pay"
          class="bg-gradient-to-r from-yellow-400 to-red-500 hover:from-yellow-500 hover:to-red-600 text-white px-8 py-3 rounded-lg font-bold shadow transition flex items-center gap-2 justify-center">
          <i class="fas fa-credit-card"></i> <?= $is_complete_payment ? 'Complete Payment' : 'Pay Now' ?>
        </button>
        <div class="text-xs text-gray-500 text-center mt-2">
          <i class="fas fa-lock"></i> Secure payment powered by Chapa, Telebirr, HelloCash, Amole
        </div>
        <?php if ($is_complete_payment): ?>
        <div class="text-center mt-4">
          <a href="orderHistory.php" class="text-blue-600 hover:text-blue-700 underline">
            <i class="fas fa-arrow-left mr-1"></i>Back to Order History
          </a>
        </div>
        <?php endif; ?>
      </form>
    </div>
    <?php endif; ?>
  </div>
  <script>
  // Client-side validation
  document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    let valid = true;
    let messages = [];

    // Name validation
    const name = document.getElementById('name').value.trim();
    if (name === '') {
      messages.push('Full Name is required.');
      valid = false;
    }

    // Email validation
    const email = document.getElementById('email').value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      messages.push('A valid Email is required.');
      valid = false;
    }

    // Phone validation
    const phone = document.getElementById('phone').value.trim();
    const phoneRegex = /^[0-9+\- ]{7,20}$/;
    if (!phoneRegex.test(phone)) {
      messages.push('A valid Phone Number is required.');
      valid = false;
    }

    // Address validation
    const address = document.getElementById('address').value.trim();
    if (address === '') {
      messages.push('Shipping Address is required.');
      valid = false;
    }

    if (!valid) {
      e.preventDefault();
      alert('Please fix the following errors:\n' + messages.join('\n'));
    }
  });
  </script>
</body>

</html>