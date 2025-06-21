<?php
include '../dbConnection/dbConnection.php';
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['error' => 'Product ID is required']);
    exit();
}

$productId = filter_var($_GET['id'], FILTER_VALIDATE_INT);
if ($productId === false || $productId <= 0) {
    echo json_encode(['error' => 'Invalid Product ID']);
    exit();
}
$_SESSION['artisan_id']=1;

if (!isset($_SESSION['artisan_id'])) {
    echo json_encode(['error' => 'Authentication required']);
    exit();
}
$artisanId = $_SESSION['artisan_id'];

try {
    
    $sql = "SELECT p.*, 
                   pi.rImage1, pi.rImage2, pi.rImage3, pi.rVideo
            FROM products p
            LEFT JOIN product_images pi ON p.id = pi.product_id AND pi.is_primary = 1
            WHERE p.id = ? AND p.artisan_id = ?
            LIMIT 1";

    $stmt = mysqli_prepare($con, $sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . mysqli_error($con));
    }

    mysqli_stmt_bind_param($stmt, "ii", $productId, $artisanId);
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Failed to execute statement: ' . mysqli_stmt_error($stmt));
    }

    $result = mysqli_stmt_get_result($stmt);
    if (!$result) {
        throw new Exception('Failed to get result: ' . mysqli_error($con));
    }

    $product = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if (!$product) {
        echo json_encode(['error' => 'Product not found or access denied']);
        exit();
    }

    // Convert NULL values to empty strings for JSON
    $product = array_map(function($value) {
        return $value === null ? '' : $value;
    }, $product);

    echo json_encode($product);
    
} catch (Exception $e) {
    error_log("Error in get_product_details.php: " . $e->getMessage());
    echo json_encode(['error' => 'Database error occurred: ' . $e->getMessage()]);
}