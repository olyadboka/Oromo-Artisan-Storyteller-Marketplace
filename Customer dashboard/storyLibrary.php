<?php
session_start();
include '../common/commonHeader.php';
include '../common/dbConnection.php';

// --- Handle AJAX for favorites ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['favorite_id'])) {
    $id = $_POST['favorite_id'];
    if (!isset($_SESSION['favorites'])) $_SESSION['favorites'] = [];
    if (in_array($id, $_SESSION['favorites'])) {
        $_SESSION['favorites'] = array_diff($_SESSION['favorites'], [$id]);
        echo json_encode(['status' => 'removed']);
    } else {
        $_SESSION['favorites'][] = $id;
        echo json_encode(['status' => 'added']);
    }
    exit;
}

// --- Handle AJAX for comments ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_story_id'])) {
    $story_id = $_POST['comment_story_id'];
    $comment = trim($_POST['comment_text'] ?? '');
    if (!isset($_SESSION['comments'])) $_SESSION['comments'] = [];
    if (!isset($_SESSION['comments'][$story_id])) $_SESSION['comments'][$story_id] = [];
    if ($comment !== '') {
        $_SESSION['comments'][$story_id][] = $comment;
    }
    echo json_encode(['comments' => $_SESSION['comments'][$story_id]]);
    exit;
}

// --- Get purchased stories for current user ---
$user_id = 1; // Demo user ID (same as checkout.php)
$purchased_stories = [];
if ($user_id) {
    $purchased_query = "SELECT DISTINCT oi.item_id as story_id FROM order_items oi 
                       JOIN orders o ON oi.order_id = o.id 
                       WHERE o.user_id = $user_id AND oi.type = 'story' AND o.status = 'delivered'";
    $purchased_result = mysqli_query($conn, $purchased_query);
    while($row = mysqli_fetch_assoc($purchased_result)) {
        $purchased_stories[] = $row['story_id'];
    }
}

// --- Fetch filters ---
$categories = mysqli_query($conn, "SELECT DISTINCT category FROM stories");
$storytellers = mysqli_query($conn, "SELECT DISTINCT storyteller FROM stories");

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$storyteller = $_GET['storyteller'] ?? '';

$query = "SELECT * FROM stories WHERE 1=1";
if ($search) $query .= " AND title LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'";
if ($category) $query .= " AND category='" . mysqli_real_escape_string($conn, $category) . "'";
if ($storyteller) $query .= " AND storyteller='" . mysqli_real_escape_string($conn, $storyteller) . "'";

$result = mysqli_query($conn, $query);
$favorites = $_SESSION['favorites'] ?? [];
$story_cart = $_SESSION['story_cart'] ?? [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Story Library</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .modal-bg { background: rgba(0,0,0,0.5); }
        .modal { max-width: 500px; }
        .modal-animate { animation: modalIn 0.2s cubic-bezier(0.4,0,0.2,1); }
        @keyframes modalIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .purchase-success { animation: purchaseSuccess 0.5s ease; }
        @keyframes purchaseSuccess { 
            0% { transform: scale(1); } 
            50% { transform: scale(1.1); } 
            100% { transform: scale(1); } 
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 to-yellow-50 min-h-screen">
    <div class="container mx-auto px-4 py-10">
        <h1 class="text-3xl font-extrabold text-center text-blue-900 mb-8 tracking-tight">📚 Oromo Story Library</h1>
        
        <!-- My Stories Link -->
        <div class="text-center mb-6">
            <a href="myStories.php" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-semibold shadow transition inline-flex items-center gap-2">
                <i class="fas fa-book-open"></i> My Purchased Stories
            </a>
        </div>
        
        <form method="get" class="flex flex-wrap gap-4 justify-center mb-8">
            <div class="relative">
                <input type="text" name="search" placeholder="Search stories..." value="<?= htmlspecialchars($search) ?>"
                    class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:outline-none shadow-sm w-56">
                <span class="absolute left-3 top-2.5 text-gray-400"><i class="fas fa-search"></i></span>
            </div>
            <select name="category" class="rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-400">
                <option value="">All Categories</option>
                <?php while($row = mysqli_fetch_assoc($categories)): ?>
                    <option value="<?= htmlspecialchars($row['category']) ?>" <?= $category == $row['category'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['category']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <select name="storyteller" class="rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-400">
                <option value="">All Storytellers</option>
                <?php mysqli_data_seek($storytellers, 0); while($row = mysqli_fetch_assoc($storytellers)): ?>
                    <option value="<?= htmlspecialchars($row['storyteller']) ?>" <?= $storyteller == $row['storyteller'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($row['storyteller']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit" class="bg-blue-700 hover:bg-blue-900 text-white px-6 py-2 rounded-lg font-semibold shadow transition">Filter</button>
        </form>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <?php if(mysqli_num_rows($result) == 0): ?>
                <div class="col-span-full text-center text-gray-500 text-lg">No stories found.</div>
            <?php endif; ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden transform hover:-translate-y-2 hover:shadow-2xl transition relative group">
                    <!-- Favorite (like) button -->
                    <button class="absolute top-3 right-3 z-10 text-gray-400 hover:text-red-500 transition favorite-btn" 
                        data-id="<?= $row['id'] ?>" title="Favorite">
                        <i class="fas fa-heart <?= in_array($row['id'], $favorites) ? 'text-red-500' : '' ?>"></i>
                    </button>
                    
                    <img src="<?= htmlspecialchars($row['thumbnail']) ?>" alt="Story" class="w-full h-40 object-cover group-hover:scale-105 transition">
                    <div class="p-5">
                        <h2 class="font-bold text-lg text-blue-900 mb-1"><?= htmlspecialchars($row['title']) ?></h2>
                        <p class="text-gray-600 text-sm mb-2"><?= htmlspecialchars($row['description']) ?></p>
                        <div class="flex items-center justify-between mb-2">
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded"><?= htmlspecialchars($row['category']) ?></span>
                            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded"><?= htmlspecialchars($row['storyteller']) ?></span>
                        </div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-bold text-green-700 text-lg">ETB <?= number_format($row['price'] ?? 0, 2) ?></span>
                            <?php if (in_array($row['id'], $purchased_stories)): ?>
                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">✓ Purchased</span>
                            <?php endif; ?>
                        </div>
                        <?php if (in_array($row['id'], $purchased_stories)): ?>
                            <button onclick="window.location.href='myStories.php'" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold shadow flex items-center gap-2 transition w-full justify-center">
                                <i class="fas fa-play"></i> Read/Listen/Watch
                            </button>
                        <?php else: ?>
                            <button onclick="purchaseStory(<?= $row['id'] ?>)" class="bg-gradient-to-r from-yellow-400 to-red-500 hover:from-yellow-500 hover:to-red-600 text-white px-4 py-2 rounded-lg font-semibold shadow flex items-center gap-2 transition w-full justify-center">
                                <i class="fas fa-shopping-cart"></i> Purchase
                            </button>
                        <?php endif; ?>
                        <button class="block mt-4 w-full bg-blue-100 hover:bg-blue-200 text-blue-900 font-semibold py-1 rounded open-modal" data-story='<?= htmlspecialchars(json_encode($row), ENT_QUOTES, "UTF-8") ?>'>
                            View Details
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Modal -->
    <div id="storyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[85vh] flex flex-col">
                <div class="p-6 border-b border-gray-200 flex-shrink-0">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-gray-800">Story Details</h3>
                        <button onclick="closeStoryModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                <div id="storyModalContent" class="flex-1 overflow-y-auto p-6" style="max-height: calc(85vh - 120px);">
                    <!-- Content will be loaded here -->
                </div>
                <div class="p-6 border-t border-gray-200 flex-shrink-0">
                    <div class="flex items-center justify-between">
                        <div id="modalFavoriteBtn" class="text-gray-400 hover:text-red-500 cursor-pointer transition text-2xl" title="Favorite">
                            <i class="fas fa-heart"></i>
                        </div>
                        <div id="modalPurchaseBtn" class="bg-gradient-to-r from-yellow-400 to-red-500 hover:from-yellow-500 hover:to-red-600 text-white px-4 py-2 rounded-lg font-semibold shadow flex items-center gap-2 transition">
                            <i class="fas fa-shopping-cart"></i> Purchase Story
                        </div>
                    </div>
                    <div class="mt-4">
                        <h3 class="font-bold text-lg mb-2">Comments</h3>
                        <div id="commentsList" class="mb-2 max-h-32 overflow-y-auto"></div>
                        <form id="commentForm" class="flex gap-2">
                            <input type="text" id="commentInput" class="flex-1 border rounded px-2 py-1" placeholder="Add a comment...">
                            <button type="submit" class="bg-blue-700 text-white px-4 py-1 rounded">Post</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Purchased stories array from PHP
    const purchasedStories = <?= json_encode($purchased_stories) ?>;
    
    function closeStoryModal() {
        document.getElementById('storyModal').classList.add('hidden');
        document.getElementById('commentsList').innerHTML = '';
        document.getElementById('commentInput').value = '';
    }

    function updateFavoriteIcon(btn, isFav) {
        const icon = btn.querySelector('i');
        if (isFav) {
            icon.classList.add('text-red-500');
        } else {
            icon.classList.remove('text-red-500');
        }
    }
    
    function purchaseStory(storyId) {
        // Add story to cart
        window.location.href = 'cart.php?add_story=' + storyId;
    }
    
    function removeFromCart(storyId) {
        // Remove story from cart
        window.location.href = 'cart.php?remove_story=' + storyId;
    }
    
    document.querySelectorAll('.favorite-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            const id = this.dataset.id;
            const isFav = this.querySelector('i').classList.contains('text-red-500');
            updateFavoriteIcon(this, !isFav); // Toggle immediately
            fetch('', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: 'favorite_id=' + encodeURIComponent(id)
            })
            .then(res => res.json())
            .then((data) => {
                updateFavoriteIcon(this, data.status === 'added');
                if (window.currentStory && window.currentStory.id == id) {
                    updateFavoriteIcon(document.getElementById('modalFavoriteBtn'), data.status === 'added');
                }
            });
        });
    });

    let currentStory = null;
    function renderModal(story) {
        const price = story.price ? parseFloat(story.price).toFixed(2) : '0.00';
        const isInCart = <?= json_encode($story_cart) ?>.hasOwnProperty(story.id);
        const isPurchased = purchasedStories.includes(parseInt(story.id)) || purchasedStories.includes(story.id.toString());
        
        let mediaHtml = '';
        if (isPurchased) {
            // Show full content for purchased stories
            if (story.media_type === 'audio') {
                mediaHtml = `<audio controls class="w-full mt-2"><source src="${story.media_url}" type="audio/wav"></audio>
                    <a href="${story.media_url}" download class="inline-block mt-2 px-4 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm"><i class="fas fa-download mr-1"></i>Download</a>`;
            } else if (story.media_type === 'video') {
                // Check if it's a YouTube embed URL
                if (story.media_url.includes('youtube.com/embed/')) {
                    mediaHtml = `<iframe class="w-full mt-2" style="height: 220px;" src="${story.media_url}" frameborder="0" allowfullscreen></iframe>`;
                } else {
                    mediaHtml = `<video controls class="w-full mt-2" style="max-height:220px;"><source src="${story.media_url}" type="video/mp4"></video>
                        <a href="${story.media_url}" download class="inline-block mt-2 px-4 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm"><i class="fas fa-download mr-1"></i>Download</a>`;
                }
            } else if (story.media_type === 'text') {
                mediaHtml = `<a href="${story.media_url}" target="_blank" class="text-blue-700 underline mt-2 inline-block">Read Full Story</a>`;
            }
        } else {
            // Show purchase restriction for non-purchased stories
            if (story.media_type === 'audio') {
                mediaHtml = `<div class="bg-gray-100 p-6 rounded-lg mt-2 text-center">
                    <i class="fas fa-headphones text-4xl text-gray-400 mb-3"></i>
                    <h4 class="font-semibold text-gray-700 mb-2">Audio Story</h4>
                    <p class="text-gray-600 mb-3">Listen to the complete audio narration</p>
                    <div class="bg-yellow-100 border border-yellow-300 rounded p-3">
                        <p class="text-yellow-800 text-sm"><i class="fas fa-lock mr-1"></i>Purchase to unlock full audio</p>
                    </div>
                </div>`;
            } else if (story.media_type === 'video') {
                mediaHtml = `<div class="relative mt-2">
                    <img src="${story.thumbnail}" alt="Video Preview" class="w-full h-48 object-cover rounded">
                    <div class="absolute inset-0 bg-black bg-opacity-60 flex items-center justify-center">
                        <div class="text-center text-white">
                            <i class="fas fa-play-circle text-5xl mb-3"></i>
                            <h4 class="font-semibold mb-2">Video Story</h4>
                            <p class="mb-3">Watch the complete video narration</p>
                            <div class="bg-yellow-500 bg-opacity-80 rounded p-2">
                                <p class="text-sm"><i class="fas fa-lock mr-1"></i>Purchase to unlock full video</p>
                            </div>
                        </div>
                    </div>
                </div>`;
            } else if (story.media_type === 'text') {
                // Show first 200 characters of description as preview
                const preview = story.description.length > 200 ? story.description.substring(0, 200) + '...' : story.description;
                mediaHtml = `<div class="bg-gray-50 p-6 rounded-lg mt-2">
                    <h4 class="font-semibold text-gray-800 mb-3">Story Preview:</h4>
                    <p class="text-gray-600 mb-4">${preview}</p>
                    <div class="bg-blue-100 border border-blue-300 rounded p-3">
                        <p class="text-blue-800 text-sm"><i class="fas fa-lock mr-1"></i>Purchase to read the complete story</p>
                    </div>
                </div>`;
            }
        }
        
        document.getElementById('storyModalContent').innerHTML = `
            <img src="${story.thumbnail}" alt="Story" class="w-full h-48 object-cover rounded mb-4">
            <h2 class="font-bold text-2xl text-blue-900 mb-1">${story.title}</h2>
            <div class="flex items-center gap-2 mb-2">
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">${story.category}</span>
                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">${story.storyteller}</span>
                ${isPurchased ? '<span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">✓ Purchased</span>' : ''}
            </div>
            <p class="text-gray-700 mb-2">${story.description}</p>
            <div class="flex items-center justify-between mb-3">
                <span class="font-bold text-green-700 text-xl">ETB ${price}</span>
                <span class="text-sm text-gray-500">${story.media_type} story</span>
            </div>
            ${mediaHtml}
        `;
        
        // Update modal purchase button
        const modalPurchaseBtn = document.getElementById('modalPurchaseBtn');
        if (isPurchased) {
            modalPurchaseBtn.innerHTML = '<i class="fas fa-check"></i> Already Purchased';
            modalPurchaseBtn.className = 'bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold shadow flex items-center gap-2 transition';
            modalPurchaseBtn.onclick = () => {};
        } else if (isInCart) {
            modalPurchaseBtn.innerHTML = '<i class="fas fa-check"></i> In Cart';
            modalPurchaseBtn.className = 'bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-semibold shadow flex items-center gap-2 transition';
            modalPurchaseBtn.onclick = () => window.location.href = 'cart.php';
        } else {
            modalPurchaseBtn.innerHTML = '<i class="fas fa-shopping-cart"></i> Purchase Story';
            modalPurchaseBtn.className = 'bg-gradient-to-r from-yellow-400 to-red-500 hover:from-yellow-500 hover:to-red-600 text-white px-4 py-2 rounded-lg font-semibold shadow flex items-center gap-2 transition';
            modalPurchaseBtn.onclick = () => purchaseStory(story.id);
        }
        
        var modalFavBtn = document.getElementById('modalFavoriteBtn');
        if (modalFavBtn) {
            var cardFavIcon = document.querySelector(`.favorite-btn[data-id='${story.id}'] i`);
            updateFavoriteIcon(modalFavBtn, cardFavIcon && cardFavIcon.classList.contains('text-red-500'));
        }
    }
    
    document.querySelectorAll('.open-modal').forEach(function(el) {
        el.addEventListener('click', function() {
            currentStory = JSON.parse(this.dataset.story);
            window.currentStory = currentStory;
            renderModal(currentStory);
            document.getElementById('storyModal').classList.remove('hidden');
            document.querySelector('.modal').classList.add('modal-animate');
            loadComments();
        });
    });
    
    function closeStoryModal() {
        document.getElementById('storyModal').classList.add('hidden');
        document.getElementById('commentsList').innerHTML = '';
        document.getElementById('commentInput').value = '';
    }
    
    document.querySelector('.modal-bg').onclick = function() {
        closeStoryModal();
    };

    document.getElementById('modalFavoriteBtn').onclick = function() {
        if (!window.currentStory) return;
        var id = window.currentStory.id;
        var isFav = this.querySelector('i').classList.contains('text-red-500');
        updateFavoriteIcon(this, !isFav); // Toggle immediately
        fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'favorite_id=' + encodeURIComponent(id)
        })
        .then(res => res.json())
        .then((data) => {
            updateFavoriteIcon(this, data.status === 'added');
            var cardBtn = document.querySelector(`.favorite-btn[data-id='${id}']`);
            if (cardBtn) updateFavoriteIcon(cardBtn, data.status === 'added');
        });
    };

    function loadComments() {
        fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'comment_story_id=' + encodeURIComponent(currentStory.id)
        })
        .then(res => res.json())
        .then(data => {
            renderComments(data.comments || []);
        });
    }
    
    function renderComments(comments) {
        var list = document.getElementById('commentsList');
        list.innerHTML = comments.length ? comments.map(function(c) { return `<div class="bg-gray-100 rounded px-2 py-1 mb-1">${c}</div>`; }).join('') : '<div class="text-gray-400">No comments yet.</div>';
    }
    
    document.getElementById('commentForm').onsubmit = function(e) {
        e.preventDefault();
        var text = document.getElementById('commentInput').value.trim();
        if (!text) return;
        fetch('', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'comment_story_id=' + encodeURIComponent(currentStory.id) + '&comment_text=' + encodeURIComponent(text)
        })
        .then(res => res.json())
        .then(data => {
            renderComments(data.comments || []);
            document.getElementById('commentInput').value = '';
        });
    };
    </script>
</body>
</html>