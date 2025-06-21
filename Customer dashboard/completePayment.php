<?php
session_start();
include '../Artisan and Story teller/dbConnection/dbConnection.php';

// Check if user is logged in (demo user ID = 1)
$user_id = 1;

// Get order ID from URL
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if (!$order_id) {
    header('Location: orderHistory.php');
    exit;
}

// Get order details
$sql = "SELECT * FROM orders WHERE id = ? AND user_id = ? AND payment_status = 'pending'";
$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    header('Location: orderHistory.php');
    exit;
}

// Get order items
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

// Store order info in session for checkout
$_SESSION['complete_payment_order'] = [
    'order_id' => $order_id,
    'order_number' => $order['order_number'],
    'total_amount' => $order['total_amount'],
    'shipping_address' => $order['shipping_address'],
    'shipping_city' => $order['shipping_city'],
    'shipping_phone' => $order['shipping_phone'],
    'items' => $items
];

// Redirect to checkout
header('Location: checkout.php?complete_payment=1');
exit;
?> 