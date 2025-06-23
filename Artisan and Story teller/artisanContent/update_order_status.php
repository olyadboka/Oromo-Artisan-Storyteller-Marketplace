<?php
session_start();
include '../dbConnection/dbConnection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['artisan_id'])) {
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit();
}

if (!isset($_POST['order_number']) || !isset($_POST['new_status'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit();
}

$orderNumber = filter_var($_POST['order_number'], FILTER_SANITIZE_STRING);
$newStatus = filter_var($_POST['new_status'], FILTER_SANITIZE_STRING);
$artisanId = $_SESSION['artisan_id'];

$allowedStatuses = ['Pending', 'Processing', 'Shipped', 'Completed', 'Cancelled'];
if (!in_array($newStatus, $allowedStatuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

try {
    // Verify the order belongs to this artisan
    $verifySql = "SELECT o.order_number 
                  FROM orders o
                  JOIN order_items oi ON o.order_number = oi.order_number
                  JOIN products p ON oi.product_id = p.id
                  WHERE o.order_number = ? AND p.artisan_id = ?
                  LIMIT 1";
    
    $verifyStmt = mysqli_prepare($con, $verifySql);
    mysqli_stmt_bind_param($verifyStmt, "si", $orderNumber, $artisanId);
    mysqli_stmt_execute($verifyStmt);
    $verifyResult = mysqli_stmt_get_result($verifyStmt);
    
    if (mysqli_num_rows($verifyResult) == 0) {
        echo json_encode(['success' => false, 'message' => 'Order not found or access denied']);
        exit();
    }

    // Update the order status
    $updateSql = "UPDATE orders SET status = ? WHERE order_number = ?";
    $updateStmt = mysqli_prepare($con, $updateSql);
    mysqli_stmt_bind_param($updateStmt, "ss", $newStatus, $orderNumber);
    $success = mysqli_stmt_execute($updateStmt);

    if ($success) {
        echo json_encode(['success' => true, 'message' => 'Order status updated']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update order status']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}