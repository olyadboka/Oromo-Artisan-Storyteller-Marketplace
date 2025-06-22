<?php
session_start();
include '../common/header.php';
include '../common/dbConnection.php';

// Demo user ID (same as checkout.php)

$user_id = 1; // Demo user ID (same as checkout.php)

// Get purchased stories for current user
$purchased_query = "SELECT DISTINCT s.*, o.order_number, o.created_at as purchase_date 
                   FROM stories s
                   JOIN order_items oi ON s.id = oi.item_id 
                   JOIN orders o ON oi.order_id = o.id 
                   WHERE o.user_id = $user_id AND oi.type = 'story' AND o.status = 'delivered'
                   ORDER BY o.created_at DESC";

$purchased_result = mysqli_query($con, $purchased_query);
$purchased_stories = [];
while($row = mysqli_fetch_assoc($purchased_result)) {
    $purchased_stories[] = $row;
}

// Handle search/filter
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$media_type = $_GET['media_type'] ?? '';

$filtered_stories = $purchased_stories;

if ($search) {
    $filtered_stories = array_filter($filtered_stories, function($story) use ($search) {
        return stripos($story['title'], $search) !== false || 
               stripos($story['description'], $search) !== false ||
               stripos($story['storyteller'], $search) !== false;
    });
}

if ($category) {
    $filtered_stories = array_filter($filtered_stories, function($story) use ($category) {
        return $story['category'] === $category;
    });
}

if ($media_type) {
    $filtered_stories = array_filter($filtered_stories, function($story) use ($media_type) {
        return $story['media_type'] === $media_type;
    });
}

// Get unique categories and media types for filters
$categories = array_unique(array_column($purchased_stories, 'category'));
$media_types = array_unique(array_column($purchased_stories, 'media_type'));
?>

<!DOCTYPE html>
<html>

<head>
  <title>My Stories</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
  .modal-bg {
    background: rgba(0, 0, 0, 0.5);
  }

  .modal {
    max-width: 600px;
  }

  .modal-animate {
    animation: modalIn 0.2s cubic-bezier(0.4, 0, 0.2, 1);
  }

  @keyframes modalIn {
    from {
      transform: scale(0.95);
      opacity: 0;
    }

    to {
      transform: scale(1);
      opacity: 1;
    }
  }
  </style>
</head>

<body class="bg-gradient-to-br from-blue-50 to-yellow-50 min-h-screen">
  <div class="container mx-auto px-4 py-10">
    <h1 class="text-3xl font-extrabold text-center text-blue-900 mb-8 tracking-tight">📚 My Purchased Stories</h1>

    <?php if (empty($purchased_stories)): ?>
    <div class="text-center py-12">
      <i class="fas fa-book-open text-6xl text-gray-300 mb-4"></i>
      <h2 class="text-2xl font-semibold text-gray-600 mb-4">No purchased stories yet</h2>
      <p class="text-gray-500 mb-6">Purchase some stories to access them here!</p>
      <a href="storyLibrary.php"
        class="bg-gradient-to-r from-yellow-400 to-red-500 hover:from-yellow-500 hover:to-red-600 text-white px-6 py-3 rounded-lg font-semibold transition">
        <i class="fas fa-book mr-2"></i>Browse Story Library
      </a>
    </div>
    <?php else: ?>
    <!-- Filters -->
    <form method="get" class="flex flex-wrap gap-4 justify-center mb-8">
      <div class="relative">
        <input type="text" name="search" placeholder="Search my stories..." value="<?= htmlspecialchars($search) ?>"
          class="pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:outline-none shadow-sm w-56">
        <span class="absolute left-3 top-2.5 text-gray-400"><i class="fas fa-search"></i></span>
      </div>
      <select name="category" class="rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-400">
        <option value="">All Categories</option>
        <?php foreach($categories as $cat): ?>
        <option value="<?= htmlspecialchars($cat) ?>" <?= $category == $cat ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat) ?>
        </option>
        <?php endforeach; ?>
      </select>
      <select name="media_type" class="rounded-lg border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-blue-400">
        <option value="">All Types</option>
        <?php foreach($media_types as $type): ?>
        <option value="<?= htmlspecialchars($type) ?>" <?= $media_type == $type ? 'selected' : '' ?>>
          <?= ucfirst(htmlspecialchars($type)) ?>
        </option>
        <?php endforeach; ?>
      </select>
      <button type="submit"
        class="bg-blue-700 hover:bg-blue-900 text-white px-6 py-2 rounded-lg font-semibold shadow transition">Filter</button>
      <a href="myStories.php"
        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-semibold shadow transition">Clear</a>
    </form>

    <!-- Stories Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
      <?php if (empty($filtered_stories)): ?>
      <div class="col-span-full text-center text-gray-500 text-lg">No stories match your filters.</div>
      <?php endif; ?>
      <?php foreach($filtered_stories as $story): ?>
      <div
        class="bg-white rounded-2xl shadow-lg overflow-hidden transform hover:-translate-y-2 hover:shadow-2xl transition relative group">
        <div class="absolute top-3 left-3 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
          ✓ Purchased
        </div>

        <img src="<?= htmlspecialchars($story['thumbnail']) ?>" alt="Story"
          class="w-full h-40 object-cover group-hover:scale-105 transition">
        <div class="p-5">
          <h2 class="font-bold text-lg text-blue-900 mb-1"><?= htmlspecialchars($story['title']) ?></h2>
          <p class="text-gray-600 text-sm mb-2"><?= htmlspecialchars($story['description']) ?></p>
          <div class="flex items-center justify-between mb-2">
            <span
              class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded"><?= htmlspecialchars($story['category']) ?></span>
            <span
              class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded"><?= htmlspecialchars($story['storyteller']) ?></span>
          </div>
          <div class="flex items-center justify-between mb-3">
            <span class="text-sm text-gray-500"><?= ucfirst($story['media_type']) ?></span>
            <span class="text-xs text-gray-400">Purchased:
              <?= date('M j, Y', strtotime($story['purchase_date'])) ?></span>
          </div>
          <button onclick="openStory(<?= htmlspecialchars(json_encode($story), ENT_QUOTES, "UTF-8") ?>)"
            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-4 py-2 rounded-lg font-semibold shadow flex items-center gap-2 transition justify-center">
            <i class="fas fa-play"></i> Read/Listen/Watch
          </button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>

  <!-- Story Modal -->
  <div id="storyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col">
        <div class="p-6 border-b border-gray-200 flex-shrink-0">
          <div class="flex justify-between items-center">
            <h3 class="text-xl font-semibold text-gray-800">Story Content</h3>
            <button onclick="closeStoryModal()" class="text-gray-400 hover:text-gray-600">
              <i class="fas fa-times text-xl"></i>
            </button>
          </div>
        </div>
        <div id="storyModalContent" class="flex-1 overflow-y-auto p-6" style="max-height: calc(90vh - 120px);">
          <!-- Content will be loaded here -->
        </div>
      </div>
    </div>
  </div>

  <script>
  function openStory(story) {
    let mediaHtml = '';

    if (story.media_type === 'audio') {
      mediaHtml = `
                <div class="text-center mb-6">
                    <i class="fas fa-headphones text-4xl text-blue-600 mb-4"></i>
                    <h4 class="text-xl font-semibold mb-2">Audio Story</h4>
                </div>
                <audio controls class="w-full mb-4">
                    <source src="${story.media_url}" type="audio/wav">
                    Your browser does not support the audio element.
                </audio>
                <div class="text-center">
                    <a href="${story.media_url}" download class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                        <i class="fas fa-download mr-2"></i>Download Audio
                    </a>
                </div>`;
    } else if (story.media_type === 'video') {
      if (story.media_url.includes('youtube.com/embed/')) {
        mediaHtml =
          `
                    <div class="text-center mb-6">
                        <i class="fas fa-video text-4xl text-red-600 mb-4"></i>
                        <h4 class="text-xl font-semibold mb-2">Video Story</h4>
                    </div>
                    <iframe class="w-full mb-4" style="height: 400px;" src="${story.media_url}" frameborder="0" allowfullscreen></iframe>`;
      } else {
        mediaHtml = `
                    <div class="text-center mb-6">
                        <i class="fas fa-video text-4xl text-red-600 mb-4"></i>
                        <h4 class="text-xl font-semibold mb-2">Video Story</h4>
                    </div>
                    <video controls class="w-full mb-4" style="max-height: 400px;">
                        <source src="${story.media_url}" type="video/mp4">
                        Your browser does not support the video element.
                    </video>
                    <div class="text-center">
                        <a href="${story.media_url}" download class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                            <i class="fas fa-download mr-2"></i>Download Video
                        </a>
                    </div>`;
      }
    } else if (story.media_type === 'text') {
      mediaHtml = `
                <div class="text-center mb-6">
                    <i class="fas fa-file-text text-4xl text-green-600 mb-4"></i>
                    <h4 class="text-xl font-semibold mb-2">Text Story</h4>
                </div>
                <div class="text-center">
                    <a href="${story.media_url}" target="_blank" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition text-lg">
                        <i class="fas fa-external-link-alt mr-2"></i>Read Full Story
                    </a>
                </div>`;
    }

    document.getElementById('storyModalContent').innerHTML = `
            <img src="${story.thumbnail}" alt="Story" class="w-full h-48 object-cover rounded mb-6">
            <h2 class="font-bold text-3xl text-blue-900 mb-2">${story.title}</h2>
            <div class="flex items-center gap-2 mb-4">
                <span class="bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded">${story.category}</span>
                <span class="bg-yellow-100 text-yellow-800 text-sm px-3 py-1 rounded">By ${story.storyteller}</span>
                <span class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded">✓ Purchased</span>
            </div>
            <p class="text-gray-700 mb-6 text-lg leading-relaxed">${story.description}</p>
            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <p class="text-sm text-gray-600">
                    <strong>Purchase Date:</strong> ${new Date(story.purchase_date).toLocaleDateString()}<br>
                    <strong>Order Number:</strong> ${story.order_number}<br>
                    <strong>Price Paid:</strong> ETB ${parseFloat(story.price || 0).toFixed(2)}
                </p>
            </div>
            ${mediaHtml}
        `;

    document.getElementById('storyModal').classList.remove('hidden');
  }

  function closeStoryModal() {
    document.getElementById('storyModal').classList.add('hidden');
  }

  // Close modal when clicking outside
  document.getElementById('storyModal').addEventListener('click', function(e) {
    if (e.target === this) {
      closeStoryModal();
    }
  });
  </script>
</body>

</html>