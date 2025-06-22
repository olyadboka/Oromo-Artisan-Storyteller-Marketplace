<?php
session_start();
include '../common/header.php'; ?>
<?php

include '../common/dbConnection.php';

// Initialize story cart if not exists
if (!isset($_SESSION['story_cart'])) {
    $_SESSION['story_cart'] = [];
}

// Add product to cart
if (isset($_GET['add'])) {
    $id = intval($_GET['add']);
    if (!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = 1;
    } else {
        $_SESSION['cart'][$id]++;
    }
    header('Location: cart.php');
    exit;
}

// Add story to cart
if (isset($_GET['add_story'])) {
    $id = intval($_GET['add_story']);
    if (!isset($_SESSION['story_cart'][$id])) {
        $_SESSION['story_cart'][$id] = 1;
    } else {
        $_SESSION['story_cart'][$id]++;
    }
    header('Location: cart.php');
    exit;
}

// Remove product from cart
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    unset($_SESSION['cart'][$id]);
    header('Location: cart.php');
    exit;
}

// Remove story from cart
if (isset($_GET['remove_story'])) {
    $id = intval($_GET['remove_story']);
    unset($_SESSION['story_cart'][$id]);
    header('Location: cart.php');
    exit;
}

// Update quantities
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    // Update product quantities
    if (isset($_POST['qty'])) {
        foreach ($_POST['qty'] as $id => $qty) {
            if ($qty > 0) {
                $_SESSION['cart'][$id] = intval($qty);
            } else {
                unset($_SESSION['cart'][$id]);
            }
        }
    }
    
    // Update story quantities
    if (isset($_POST['story_qty'])) {
        foreach ($_POST['story_qty'] as $id => $qty) {
            if ($qty > 0) {
                $_SESSION['story_cart'][$id] = intval($qty);
            } else {
                unset($_SESSION['story_cart'][$id]);
            }
        }
    }
    
    header('Location: cart.php');
    exit;
}

// Fetch products in cart
$cart = $_SESSION['cart'] ?? [];
$products = [];
$total = 0;

if ($cart) {
    $ids = implode(',', array_keys($cart));
    $result = mysqli_query($con, "SELECT * FROM products WHERE id IN ($ids)");
    while ($row = mysqli_fetch_assoc($result)) {
        $row['qty'] = $cart[$row['id']];
        $row['subtotal'] = $row['qty'] * $row['price'];
        $products[] = $row;
        $total += $row['subtotal'];
    }
}

// Fetch stories in cart
$story_cart = $_SESSION['story_cart'] ?? [];
$stories = [];
$story_total = 0;

if ($story_cart) {
    $story_ids = implode(',', array_keys($story_cart));
    $result = mysqli_query($con, "SELECT * FROM stories WHERE id IN ($story_ids)");
    while ($row = mysqli_fetch_assoc($result)) {
        $row['qty'] = $story_cart[$row['id']];
        $row['subtotal'] = $row['qty'] * ($row['price'] ?? 0);
        $stories[] = $row;
        $story_total += $row['subtotal'];
    }
}

$grand_total = $total + $story_total;
?>
<!DOCTYPE html>
<html>

<head>
  <title>Shopping Cart</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-blue-50 to-yellow-50 min-h-screen">
  <div class="container mx-auto px-4 py-10">
    <h1 class="text-3xl font-extrabold text-center text-blue-900 mb-8 tracking-tight">🛒 Your Shopping Cart</h1>
    <?php if (!$products && !$stories): ?>
    <div class="text-center text-gray-500 text-lg">Your cart is empty.
      <a href="products.php" class="text-blue-700 underline">Browse products</a> or
      <a href="storyLibrary.php" class="text-blue-700 underline">Browse stories</a>
    </div>
    <?php else: ?>
    <form method="post">
      <div class="space-y-8">
        <!-- Products Section -->
        <?php if ($products): ?>
        <div>
          <h2 class="text-xl font-semibold text-blue-900 mb-4 flex items-center">
            <i class="fas fa-shopping-bag mr-2"></i>Products
          </h2>
          <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-xl shadow-lg">
              <thead>
                <tr class="bg-blue-100 text-blue-900">
                  <th class="py-3 px-4 text-left">Product</th>
                  <th class="py-3 px-4 text-left">Price</th>
                  <th class="py-3 px-4 text-left">Quantity</th>
                  <th class="py-3 px-4 text-left">Subtotal</th>
                  <th class="py-3 px-4"></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($products as $item): ?>
                <tr class="border-b">
                  <td class="py-3 px-4 flex items-center gap-4">
                    <img
                      src="<?= htmlspecialchars($item['image'] ?: 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80') ?>"
                      alt="Product" class="w-16 h-16 object-cover rounded shadow">
                    <div>
                      <div class="font-bold text-blue-900"><?= htmlspecialchars($item['name']) ?></div>
                      <div class="text-xs text-gray-500"><?= htmlspecialchars($item['category']) ?> |
                        <?= htmlspecialchars($item['location']) ?></div>
                    </div>
                  </td>
                  <td class="py-3 px-4 font-semibold text-green-700">ETB <?= $item['price'] ?></td>
                  <td class="py-3 px-4">
                    <input type="number" name="qty[<?= $item['id'] ?>]" value="<?= $item['qty'] ?>" min="1"
                      class="w-16 border rounded px-2 py-1 text-center">
                  </td>
                  <td class="py-3 px-4 font-semibold">ETB <?= number_format($item['subtotal'], 2) ?></td>
                  <td class="py-3 px-4">
                    <a href="cart.php?remove=<?= $item['id'] ?>" class="text-red-600 hover:text-red-800"
                      title="Remove"><i class="fas fa-trash"></i></a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php endif; ?>

        <!-- Stories Section -->
        <?php if ($stories): ?>
        <div>
          <h2 class="text-xl font-semibold text-blue-900 mb-4 flex items-center">
            <i class="fas fa-book mr-2"></i>Stories
          </h2>
          <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-xl shadow-lg">
              <thead>
                <tr class="bg-yellow-100 text-yellow-900">
                  <th class="py-3 px-4 text-left">Story</th>
                  <th class="py-3 px-4 text-left">Price</th>
                  <th class="py-3 px-4 text-left">Quantity</th>
                  <th class="py-3 px-4 text-left">Subtotal</th>
                  <th class="py-3 px-4"></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($stories as $item): ?>
                <tr class="border-b">
                  <td class="py-3 px-4 flex items-center gap-4">
                    <img
                      src="<?= htmlspecialchars($item['thumbnail'] ?: 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80') ?>"
                      alt="Story" class="w-16 h-16 object-cover rounded shadow">
                    <div>
                      <div class="font-bold text-blue-900"><?= htmlspecialchars($item['title']) ?></div>
                      <div class="text-xs text-gray-500">By <?= htmlspecialchars($item['storyteller']) ?> |
                        <?= htmlspecialchars($item['category']) ?></div>
                    </div>
                  </td>
                  <td class="py-3 px-4 font-semibold text-green-700">ETB <?= number_format($item['price'] ?? 0, 2) ?>
                  </td>
                  <td class="py-3 px-4">
                    <input type="number" name="story_qty[<?= $item['id'] ?>]" value="<?= $item['qty'] ?>" min="1"
                      class="w-16 border rounded px-2 py-1 text-center">
                  </td>
                  <td class="py-3 px-4 font-semibold">ETB <?= number_format($item['subtotal'], 2) ?></td>
                  <td class="py-3 px-4">
                    <a href="cart.php?remove_story=<?= $item['id'] ?>" class="text-red-600 hover:text-red-800"
                      title="Remove"><i class="fas fa-trash"></i></a>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <div class="flex flex-col md:flex-row justify-between items-center mt-8 gap-4">
        <div>
          <button type="submit" name="update"
            class="bg-blue-700 hover:bg-blue-900 text-white px-6 py-2 rounded-lg font-semibold shadow transition">Update
            Cart</button>
        </div>
        <div class="text-xl font-bold text-blue-900">
          Total: <span class="text-green-700">ETB <?= number_format($grand_total, 2) ?></span>
        </div>
        <div>
          <a href="checkout.php"
            class="bg-gradient-to-r from-yellow-400 to-red-500 hover:from-yellow-500 hover:to-red-600 text-white px-8 py-3 rounded-lg font-bold shadow transition flex items-center gap-2">
            <i class="fas fa-credit-card"></i> Checkout
          </a>
        </div>
      </div>
    </form>
    <?php endif; ?>
  </div>
</body>

</html>