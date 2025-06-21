<?php
session_start();
include '../common/dbConnection.php';

$status = $_GET['status'] ?? $_POST['status'] ?? '';
$tx_ref = $_GET['tx_ref'] ?? $_POST['tx_ref'] ?? '';


$status='success';

// Update order status if payment was successful
if ($status === 'success' && isset($_SESSION['pending_order'])) {
    $order_id = $_SESSION['pending_order']['order_id'];
    
    // Update order status to completed and payment status to paid
    $update_sql = "UPDATE orders SET status = 'delivered', payment_status = 'paid' WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    
    // Clear the pending order from session
    unset($_SESSION['pending_order']);
    
    $title = 'Payment Successful!';
    $message = "Thank you for your order. Your payment has been processed successfully and your order is now complete.";
    $color = 'green-600';
    $icon = 'fa-check-circle';
    $iconBg = 'bg-green-100';
    $iconColor = 'text-green-600';
} elseif ($status === 'failed' || $status === 'cancelled') {
    // Update order status to cancelled if payment failed
    if (isset($_SESSION['pending_order'])) {
        $order_id = $_SESSION['pending_order']['order_id'];
        $update_sql = "UPDATE orders SET status = 'cancelled', payment_status = 'failed' WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        unset($_SESSION['pending_order']);
    }
    
    $title = 'Payment Failed';
    $message = 'Sorry, your payment was not successful. Please try again or contact support.';
    $color = 'red-600';
    $icon = 'fa-times-circle';
    $iconBg = 'bg-red-100';
    $iconColor = 'text-red-600';
} else {
    $title = 'Payment Status';
    $message = 'Thank you for your payment. The payment was processed successfully.';
    $color = 'blue-600';
    $icon = 'fa-info-circle';
    $iconBg = 'bg-blue-100';
    $iconColor = 'text-blue-600';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Status</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .fade-in {
            animation: fadeIn 1s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-yellow-50 min-h-screen flex items-center justify-center">
    <div class="fade-in max-w-md w-full mx-auto border-t-8 rounded-xl shadow-xl p-8 text-center border-<?= $color ?> bg-white">
        <div class="flex justify-center mb-4">
            <div class="<?= $iconBg ?> rounded-full p-4 animate-bounce">
                <i class="fas <?= $icon ?> <?= $iconColor ?> text-5xl"></i>
            </div>
        </div>
        <h1 class="text-3xl font-extrabold mb-2 text-<?= $color ?>"><?= htmlspecialchars($title) ?></h1>
        <p class="mb-4 text-gray-700 text-lg"><?= htmlspecialchars($message) ?></p>
        <?php if ($tx_ref): ?>
            <div class="mb-4 text-xs text-gray-500">Transaction Reference: <span class="font-mono"><?= htmlspecialchars($tx_ref) ?></span></div>
        <?php endif; ?>
        <div class="flex flex-col gap-2">
            <a href="products.php" class="inline-block px-6 py-2 bg-<?= $color ?> text-white rounded-lg font-semibold shadow hover:bg-opacity-90 transition">Back to Products</a>
            <a href="storyLibrary.php" class="inline-block px-6 py-2 bg-blue-600 text-white rounded-lg font-semibold shadow hover:bg-blue-700 transition">Browse Stories</a>
            <a href="orderHistory.php" class="inline-block px-6 py-2 bg-green-600 text-white rounded-lg font-semibold shadow hover:bg-green-700 transition">View Order History</a>
        </div>
    </div>
</body>
</html>

