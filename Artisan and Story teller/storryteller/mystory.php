<?php
// Database connection
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "oromo_artisan_and_storyteller";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $conn->exec("SET time_zone = '+03:00'");
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}

// Assume logged-in user (replace with proper authentication)
$user_id = $_SESSION['user_id'];// Jirenya Dhugaa for testing

// Fetch storyteller data
$stmt = $conn->prepare("
    SELECT s.*, u.username 
    FROM storytellers s 
    JOIN users u ON s.user_id = u.id 
    WHERE s.user_id = :user_id
");
$stmt->execute(['user_id' => $user_id]);
$storyteller = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$storyteller) {
  die("Storyteller not found for user ID: $user_id");
}

// Get filter parameters
$media_type = isset($_GET['media_type']) ? $_GET['media_type'] : '';
$language = isset($_GET['language']) ? $_GET['language'] : '';
$age_group = isset($_GET['age_group']) ? $_GET['age_group'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch stories with filters
$story_query = "
    SELECT s.*, 
           GROUP_CONCAT(st.theme) as themes,
           COALESCE(sl.listen_count, 0) as listen_count
    FROM stories s
    LEFT JOIN story_themes st ON s.id = st.story_id
    LEFT JOIN story_listens sl ON s.id = sl.story_id
    WHERE s.storyteller_id = :storyteller_id
";
$params = ['storyteller_id' => $storyteller['id']];

if ($media_type && in_array($media_type, ['audio', 'video', 'text'])) {
  $story_query .= " AND s.media_type = :media_type";
  $params['media_type'] = $media_type;
}
if ($language && in_array($language, ['Afaan Oromo', 'English', 'Amharic'])) {
  $story_query .= " AND s.language = :language";
  $params['language'] = $language;
}
if ($age_group && in_array($age_group, ['all', 'children', 'adults'])) {
  $story_query .= " AND s.age_group = :age_group";
  $params['age_group'] = $age_group;
}
if ($search) {
  $story_query .= " AND (s.title LIKE :search OR s.description LIKE :search)";
  $params['search'] = "%$search%";
}

$story_query .= " GROUP BY s.id ORDER BY s.created_at DESC";
$stmt = $conn->prepare($story_query);
$stmt->execute($params);
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle story deletion
if (isset($_POST['delete_story'])) {
  $story_id = $_POST['story_id'];
  try {
    // Delete from related tables first
    $conn->beginTransaction();

    $conn->prepare("DELETE FROM story_themes WHERE story_id = ?")->execute([$story_id]);
    $conn->prepare("DELETE FROM story_listens WHERE story_id = ?")->execute([$story_id]);
    $conn->prepare("DELETE FROM story_comments WHERE story_id = ?")->execute([$story_id]);

    // Delete the story itself
    $stmt = $conn->prepare("DELETE FROM stories WHERE id = ? AND storyteller_id = ?");
    $stmt->execute([$story_id, $storyteller['id']]);

    $conn->commit();

    // Refresh the page to show updated list
    header("Location: mystory.php");
    exit();
  } catch (PDOException $e) {
    $conn->rollBack();
    $delete_error = "Failed to delete story: " . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Stories - Oromo Storyteller Network</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
  .storyteller-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #7c2d12 100%);
  }

  .table-header {
    background-color: #f8fafc;
  }

  .media-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
  }

  .audio-bg {
    background-color: #3B82F6;
  }

  .video-bg {
    background-color: #EF4444;
  }

  .text-bg {
    background-color: #10B981;
  }
  </style>
</head>

<body class="bg-gray-50">
  <!-- Dashboard Header -->
  <header class="storyteller-header text-white">
    <div class="container mx-auto px-4 py-8">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
        <div class="flex items-center space-x-6 mb-6 md:mb-0">
          <img
            src="<?php echo htmlspecialchars($storyteller['profile_image_url'] ?? 'uploads/profiles/default-profile.jpg'); ?>"
            alt="Storyteller"
            class="w-20 h-20 rounded-full border-4 border-white border-opacity-30 object-cover shadow-lg">
          <div>
            <h1 class="text-3xl font-bold">
              <?php echo htmlspecialchars($storyteller['artistic_name'] ?? 'Unknown Storyteller'); ?></h1>
            <p class="text-white text-opacity-80 flex items-center">
              <i class="fas fa-map-marker-alt mr-2"></i>
              <?php echo htmlspecialchars($storyteller['location'] ?? 'Unknown Location'); ?>
              <span class="ml-4 px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm">
                <i class="fas fa-certificate mr-1"></i>
                <?php echo ucfirst($storyteller['verification_status'] ?? 'Pending') ?> Storykeeper
              </span>
            </p>
          </div>
        </div>
        <div class="flex space-x-4">
          <button class="px-5 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg transition">
            <i class="fas fa-cog mr-2"></i> as customer
          </button>
          <button class="px-5 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg transition">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
          </button>
        </div>
      </div>
    </div>
  </header>

  <!-- Dashboard Navigation -->
  <nav class="bg-white shadow-sm sticky top-0 z-10">
    <div class="container mx-auto px-4">
      <div class="flex overflow-x-auto">
        <a href="storytellers.php" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-home mr-2"></i> Dashboard
        </a>
        <a href="mystory.php" class="px-6 py-4 font-medium text-blue-800 border-b-2 border-blue-800">
          <i class="fas fa-book-open mr-2"></i> My Stories
        </a>
        <a href="events.php" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-calendar-alt mr-2"></i> Events
        </a>
        <a href="community.php" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-comments mr-2"></i> Community
        </a>
        <a href="analytics.php" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-chart-line mr-2"></i> Analytics
        </a>
        <a href="earning.php" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-wallet mr-2"></i> Earnings
        </a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">My Stories</h1>
      <a href="add_story.php"
        class="mt-4 md:mt-0 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium">
        <i class="fas fa-plus mr-2"></i> Add New Story
      </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow p-6 mb-8">
      <h2 class="text-xl font-bold text-gray-800 mb-4">Filter Stories</h2>
      <form method="get" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
          <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Search stories...">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Media Type</label>
          <select name="media_type"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">All</option>
            <option value="audio" <?php echo $media_type == 'audio' ? 'selected' : ''; ?>>Audio</option>
            <option value="video" <?php echo $media_type == 'video' ? 'selected' : ''; ?>>Video</option>
            <option value="text" <?php echo $media_type == 'text' ? 'selected' : ''; ?>>Text</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Language</label>
          <select name="language"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">All</option>
            <option value="Afaan Oromo" <?php echo $language == 'Afaan Oromo' ? 'selected' : ''; ?>>Afaan Oromo</option>
            <option value="English" <?php echo $language == 'English' ? 'selected' : ''; ?>>English</option>
            <option value="Amharic" <?php echo $language == 'Amharic' ? 'selected' : ''; ?>>Amharic</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Age Group</label>
          <select name="age_group"
            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">All</option>
            <option value="all" <?php echo $age_group == 'all' ? 'selected' : ''; ?>>All Ages</option>
            <option value="children" <?php echo $age_group == 'children' ? 'selected' : ''; ?>>Children</option>
            <option value="adults" <?php echo $age_group == 'adults' ? 'selected' : ''; ?>>Adults</option>
          </select>
        </div>
        <div class="sm:col-span-2 md:col-span-4">
          <button type="submit" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium">
            <i class="fas fa-filter mr-2"></i> Apply Filters
          </button>
          <a href="mystory.php"
            class="mt-4 ml-2 bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-lg font-medium">
            <i class="fas fa-times mr-2"></i> Clear Filters
          </a>
        </div>
      </form>
    </div>

    <!-- Stories List -->
    <div class="bg-white rounded-xl shadow overflow-hidden mb-8">
      <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Your Stories (<?php echo count($stories); ?>)</h2>
        <p class="text-sm text-gray-600">
          <i class="fas fa-headphones mr-1"></i> Total Listens:
          <?php echo array_sum(array_column($stories, 'listen_count')); ?>
        </p>
      </div>

      <?php if (isset($delete_error)): ?>
      <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mx-6 mt-4">
        <p><?php echo $delete_error; ?></p>
      </div>
      <?php endif; ?>

      <?php if (empty($stories)): ?>
      <div class="p-8 text-center">
        <i class="fas fa-book-open text-4xl text-gray-300 mb-4"></i>
        <h3 class="text-lg font-medium text-gray-700">No stories found</h3>
        <p class="text-gray-500 mt-2">You haven't added any stories yet or no stories match your filters.</p>
        <a href="add_story.php"
          class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg font-medium">
          <i class="fas fa-plus mr-2"></i> Add Your First Story
        </a>
      </div>
      <?php else: ?>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="table-header">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Details</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Themes</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Listens</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php foreach ($stories as $story): ?>
            <tr>
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <?php
                      $icon_class = '';
                      $bg_class = '';
                      if ($story['media_type'] == 'audio') {
                        $icon_class = 'fas fa-volume-up';
                        $bg_class = 'audio-bg';
                      } elseif ($story['media_type'] == 'video') {
                        $icon_class = 'fas fa-video';
                        $bg_class = 'video-bg';
                      } else {
                        $icon_class = 'fas fa-align-left';
                        $bg_class = 'text-bg';
                      }
                      ?>
                  <div class="media-icon <?php echo $bg_class; ?> text-white mr-4">
                    <i class="<?php echo $icon_class; ?>"></i>
                  </div>
                  <div>
                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($story['title']); ?></div>
                    <div class="text-sm text-gray-500">
                      <?php echo date('M d, Y', strtotime($story['created_at'])); ?>
                    </div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span
                  class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo $story['media_type'] == 'audio' ? 'bg-blue-100 text-blue-800' : ($story['media_type'] == 'video' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'); ?>">
                  <?php echo ucfirst($story['media_type']); ?>
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div><strong>Language:</strong> <?php echo $story['language']; ?></div>
                <div><strong>Age:</strong> <?php echo ucfirst($story['age_group']); ?></div>
                <?php if ($story['media_type'] == 'audio' || $story['media_type'] == 'video'): ?>
                <div><strong>Duration:</strong> <?php echo $story['duration']; ?> min</div>
                <?php else: ?>
                <div><strong>Words:</strong> <?php echo $story['word_count']; ?></div>
                <?php endif; ?>
              </td>
              <td class="px-6 py-4 text-sm text-gray-500">
                <?php echo htmlspecialchars($story['themes'] ?? $storyteller['specialization']); ?>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                <div class="flex items-center">
                  <i class="fas fa-headphones mr-2 text-gray-400"></i>
                  <?php echo number_format($story['listen_count']); ?>
                  <?php if ($story['is_featured']): ?>
                  <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">
                    <i class="fas fa-star mr-1"></i> Featured
                  </span>
                  <?php endif; ?>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <a href="edit_story.php?id=<?php echo $story['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-4">
                  <i class="fas fa-edit"></i> Edit
                </a>
                <form method="post" class="inline"
                  onsubmit="return confirm('Are you sure you want to delete this story?');">
                  <input type="hidden" name="story_id" value="<?php echo $story['id']; ?>">
                  <button type="submit" name="delete_story" class="text-red-600 hover:text-red-900">
                    <i class="fas fa-trash"></i> Delete
                  </button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-gray-800 text-white py-8 mt-12">
    <div class="container mx-auto px-4">
      <div class="flex flex-col md:flex-row justify-between">
        <div class="mb-6 md:mb-0">
          <h3 class="text-xl font-bold mb-4">Oromo Storyteller Network</h3>
          <p class="text-gray-400">Preserving oral traditions for future generations</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
          <div>
            <h4 class="font-semibold mb-3">Resources</h4>
            <ul class="space-y-2">
              <li><a href="#" class="text-gray-400 hover:text-white">Recording Guide</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white">Storytelling Tips</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white">Cultural Database</a></li>
            </ul>
          </div>
          <div>
            <h4 class="font-semibold mb-3">Support</h4>
            <ul class="space-y-2">
              <li><a href="#" class="text-gray-400 hover:text-white">Help Center</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white">Community</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white">Contact Us</a></li>
            </ul>
          </div>
          <div>
            <h4 class="font-semibold mb-3">Connect</h4>
            <div class="flex space-x-4">
              <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f"></i></a>
              <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
              <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-youtube"></i></a>
            </div>
          </div>
        </div>
      </div>
      <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400 text-sm">
        © 2025 Oromo Artisan & Storyteller Marketplace. All rights reserved.
      </div>
    </div>
  </footer>
</body>

</html>