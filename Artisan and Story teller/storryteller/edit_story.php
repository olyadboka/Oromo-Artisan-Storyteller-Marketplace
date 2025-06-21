<?php
// Database connection
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
$user_id = 7; // Jirenya Dhugaa for testing

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

// Get story ID from URL
$story_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch story data
$stmt = $conn->prepare("
    SELECT s.*, GROUP_CONCAT(st.theme) as themes
    FROM stories s
    LEFT JOIN story_themes st ON s.id = st.story_id
    WHERE s.id = :story_id AND s.storyteller_id = :storyteller_id
    GROUP BY s.id
");
$stmt->execute([
  'story_id' => $story_id,
  'storyteller_id' => $storyteller['id']
]);
$story = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$story) {
  die("Story not found or you don't have permission to edit it");
}

// Available themes (would normally come from database)
$available_themes = [
  'Folktale',
  'History',
  'Mythology',
  'Animal Story',
  'Moral Lesson',
  'Adventure',
  'Cultural',
  'Legend'
];

// Handle form submission
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Validate and sanitize input
  $title = trim($_POST['title'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $media_type = $_POST['media_type'] ?? '';
  $language = $_POST['language'] ?? '';
  $age_group = $_POST['age_group'] ?? 'all';
  $is_featured = isset($_POST['is_featured']) ? 1 : 0;
  $themes = $_POST['themes'] ?? [];

  // Duration/word count based on media type
  if ($media_type === 'audio' || $media_type === 'video') {
    $duration = intval($_POST['duration'] ?? 0);
    $word_count = null;
  } else {
    $word_count = intval($_POST['word_count'] ?? 0);
    $duration = null;
  }

  // Validate required fields
  if (empty($title)) {
    $errors['title'] = 'Title is required';
  }
  if (empty($description)) {
    $errors['description'] = 'Description is required';
  }
  if (!in_array($media_type, ['audio', 'video', 'text'])) {
    $errors['media_type'] = 'Invalid media type';
  }
  if (!in_array($language, ['Afaan Oromo', 'English', 'Amharic'])) {
    $errors['language'] = 'Invalid language';
  }
  if (($media_type === 'audio' || $media_type === 'video') && $duration <= 0) {
    $errors['duration'] = 'Duration must be positive';
  }
  if ($media_type === 'text' && $word_count <= 0) {
    $errors['word_count'] = 'Word count must be positive';
  }

  // Handle file upload if a new file was provided
  $media_url = $story['media_url'];
  if (isset($_FILES['media_file']) && $_FILES['media_file']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/stories/';
    if (!file_exists($upload_dir)) {
      mkdir($upload_dir, 0777, true);
    }

    // Validate file type based on media type
    $allowed_types = [];
    if ($media_type === 'audio') {
      $allowed_types = ['audio/mpeg', 'audio/wav', 'audio/ogg'];
    } elseif ($media_type === 'video') {
      $allowed_types = ['video/mp4', 'video/quicktime', 'video/x-msvideo'];
    } else {
      $allowed_types = ['text/plain'];
    }

    $file_info = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($file_info, $_FILES['media_file']['tmp_name']);
    finfo_close($file_info);

    if (!in_array($mime_type, $allowed_types)) {
      $errors['media_file'] = 'Invalid file type for selected media type';
    } else {
      // Generate unique filename
      $ext = pathinfo($_FILES['media_file']['name'], PATHINFO_EXTENSION);
      $filename = uniqid('story_') . '.' . $ext;
      $target_path = $upload_dir . $filename;

      if (move_uploaded_file($_FILES['media_file']['tmp_name'], $target_path)) {
        // Delete old file if it exists
        if ($media_url && file_exists($media_url)) {
          unlink($media_url);
        }
        $media_url = $target_path;
      } else {
        $errors['media_file'] = 'Failed to upload file';
      }
    }
  }

  // Update database if no errors
  if (empty($errors)) {
    try {
      $conn->beginTransaction();

      // Update story
      $stmt = $conn->prepare("
                UPDATE stories SET
                    title = :title,
                    description = :description,
                    media_type = :media_type,
                    media_url = :media_url,
                    duration = :duration,
                    word_count = :word_count,
                    language = :language,
                    age_group = :age_group,
                    is_featured = :is_featured
                WHERE id = :id AND storyteller_id = :storyteller_id
            ");
      $stmt->execute([
        'title' => $title,
        'description' => $description,
        'media_type' => $media_type,
        'media_url' => $media_url,
        'duration' => $duration,
        'word_count' => $word_count,
        'language' => $language,
        'age_group' => $age_group,
        'is_featured' => $is_featured,
        'id' => $story_id,
        'storyteller_id' => $storyteller['id']
      ]);

      // Update themes
      $conn->prepare("DELETE FROM story_themes WHERE story_id = ?")->execute([$story_id]);
      $theme_stmt = $conn->prepare("INSERT INTO story_themes (story_id, theme) VALUES (?, ?)");
      foreach ($themes as $theme) {
        if (in_array($theme, $available_themes)) {
          $theme_stmt->execute([$story_id, $theme]);
        }
      }

      $conn->commit();
      $success = true;

      // Refresh story data
      $stmt = $conn->prepare("
                SELECT s.*, GROUP_CONCAT(st.theme) as themes
                FROM stories s
                LEFT JOIN story_themes st ON s.id = st.story_id
                WHERE s.id = :story_id
                GROUP BY s.id
            ");
      $stmt->execute(['story_id' => $story_id]);
      $story = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
      $conn->rollBack();
      $errors['database'] = 'Database error: ' . $e->getMessage();
    }
  }
}

// Split themes into array for form
$current_themes = $story['themes'] ? explode(',', $story['themes']) : [];
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Story - Oromo Storyteller Network</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    .storyteller-header {
      background: linear-gradient(135deg, #1e3a8a 0%, #7c2d12 100%);
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
          <img src="<?php echo htmlspecialchars($storyteller['profile_image_url'] ?? 'uploads/profiles/default-profile.jpg'); ?>" alt="Storyteller" class="w-20 h-20 rounded-full border-4 border-white border-opacity-30 object-cover shadow-lg">
          <div>
            <h1 class="text-3xl font-bold"><?php echo htmlspecialchars($storyteller['artistic_name'] ?? 'Unknown Storyteller'); ?></h1>
            <p class="text-white text-opacity-80 flex items-center">
              <i class="fas fa-map-marker-alt mr-2"></i> <?php echo htmlspecialchars($storyteller['location'] ?? 'Unknown Location'); ?>
              <span class="ml-4 px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm">
                <i class="fas fa-certificate mr-1"></i>
                <?php echo ucfirst($storyteller['verification_status'] ?? 'Pending') ?> Storykeeper
              </span>
            </p>
          </div>
        </div>
        <div class="flex space-x-4">
          <button class="px-5 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg transition">
            <i class="fas fa-bell mr-2"></i> Notifications
          </button>
          <button class="px-5 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg transition">
            <i class="fas fa-cog mr-2"></i> Settings
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
        <a href="#" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-calendar-alt mr-2"></i> Events
        </a>
        <a href="#" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-comments mr-2"></i> Community
        </a>
        <a href="analytics.php" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-chart-line mr-2"></i> Analytics
        </a>
        <a href="#" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-wallet mr-2"></i> Earnings
        </a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">Edit Story</h1>
      <a href="mystory.php" class="text-blue-600 hover:text-blue-800">
        <i class="fas fa-arrow-left mr-1"></i> Back to My Stories
      </a>
    </div>

    <?php if ($success): ?>
      <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
        <p>Story updated successfully!</p>
      </div>
    <?php endif; ?>

    <?php if (!empty($errors['database'])): ?>
      <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
        <p><?php echo $errors['database']; ?></p>
      </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow overflow-hidden">
      <form method="post" enctype="multipart/form-data" class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Left Column -->
          <div>
            <!-- Story Title -->
            <div class="mb-4">
              <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Story Title *</label>
              <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($story['title']); ?>"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 <?php echo isset($errors['title']) ? 'border-red-500' : ''; ?>">
              <?php if (isset($errors['title'])): ?>
                <p class="mt-1 text-sm text-red-600"><?php echo $errors['title']; ?></p>
              <?php endif; ?>
            </div>

            <!-- Description -->
            <div class="mb-4">
              <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
              <textarea id="description" name="description" rows="4"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 <?php echo isset($errors['description']) ? 'border-red-500' : ''; ?>"><?php echo htmlspecialchars($story['description']); ?></textarea>
              <?php if (isset($errors['description'])): ?>
                <p class="mt-1 text-sm text-red-600"><?php echo $errors['description']; ?></p>
              <?php endif; ?>
            </div>

            <!-- Media Type -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Media Type *</label>
              <div class="grid grid-cols-3 gap-2">
                <label class="flex items-center space-x-2 p-3 border rounded-lg cursor-pointer <?php echo $story['media_type'] === 'audio' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'; ?>">
                  <input type="radio" name="media_type" value="audio" class="text-blue-600 focus:ring-blue-500"
                    <?php echo $story['media_type'] === 'audio' ? 'checked' : ''; ?>>
                  <div class="media-icon audio-bg text-white">
                    <i class="fas fa-volume-up"></i>
                  </div>
                  <span>Audio</span>
                </label>
                <label class="flex items-center space-x-2 p-3 border rounded-lg cursor-pointer <?php echo $story['media_type'] === 'video' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'; ?>">
                  <input type="radio" name="media_type" value="video" class="text-blue-600 focus:ring-blue-500"
                    <?php echo $story['media_type'] === 'video' ? 'checked' : ''; ?>>
                  <div class="media-icon video-bg text-white">
                    <i class="fas fa-video"></i>
                  </div>
                  <span>Video</span>
                </label>
                <label class="flex items-center space-x-2 p-3 border rounded-lg cursor-pointer <?php echo $story['media_type'] === 'text' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'; ?>">
                  <input type="radio" name="media_type" value="text" class="text-blue-600 focus:ring-blue-500"
                    <?php echo $story['media_type'] === 'text' ? 'checked' : ''; ?>>
                  <div class="media-icon text-bg text-white">
                    <i class="fas fa-align-left"></i>
                  </div>
                  <span>Text</span>
                </label>
              </div>
              <?php if (isset($errors['media_type'])): ?>
                <p class="mt-1 text-sm text-red-600"><?php echo $errors['media_type']; ?></p>
              <?php endif; ?>
            </div>

            <!-- Media File -->
            <div class="mb-4">
              <label for="media_file" class="block text-sm font-medium text-gray-700 mb-1">Media File</label>
              <div class="flex items-center space-x-4">
                <div class="flex-1">
                  <input type="file" id="media_file" name="media_file"
                    class="block w-full text-sm text-gray-500
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-lg file:border-0
                                                  file:text-sm file:font-semibold
                                                  file:bg-blue-50 file:text-blue-700
                                                  hover:file:bg-blue-100">
                  <p class="mt-1 text-xs text-gray-500">
                    Current file: <?php echo basename($story['media_url']); ?>
                  </p>
                </div>
              </div>
              <?php if (isset($errors['media_file'])): ?>
                <p class="mt-1 text-sm text-red-600"><?php echo $errors['media_file']; ?></p>
              <?php endif; ?>
            </div>
          </div>

          <!-- Right Column -->
          <div>
            <!-- Duration/Word Count -->
            <div class="mb-4">
              <?php if ($story['media_type'] === 'audio' || $story['media_type'] === 'video'): ?>
                <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes) *</label>
                <input type="number" id="duration" name="duration" min="1" value="<?php echo $story['duration']; ?>"
                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 <?php echo isset($errors['duration']) ? 'border-red-500' : ''; ?>">
                <?php if (isset($errors['duration'])): ?>
                  <p class="mt-1 text-sm text-red-600"><?php echo $errors['duration']; ?></p>
                <?php endif; ?>
              <?php else: ?>
                <label for="word_count" class="block text-sm font-medium text-gray-700 mb-1">Word Count *</label>
                <input type="number" id="word_count" name="word_count" min="1" value="<?php echo $story['word_count']; ?>"
                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 <?php echo isset($errors['word_count']) ? 'border-red-500' : ''; ?>">
                <?php if (isset($errors['word_count'])): ?>
                  <p class="mt-1 text-sm text-red-600"><?php echo $errors['word_count']; ?></p>
                <?php endif; ?>
              <?php endif; ?>
            </div>

            <!-- Language -->
            <div class="mb-4">
              <label for="language" class="block text-sm font-medium text-gray-700 mb-1">Language *</label>
              <select id="language" name="language"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 <?php echo isset($errors['language']) ? 'border-red-500' : ''; ?>">
                <option value="Afaan Oromo" <?php echo $story['language'] === 'Afaan Oromo' ? 'selected' : ''; ?>>Afaan Oromo</option>
                <option value="English" <?php echo $story['language'] === 'English' ? 'selected' : ''; ?>>English</option>
                <option value="Amharic" <?php echo $story['language'] === 'Amharic' ? 'selected' : ''; ?>>Amharic</option>
              </select>
              <?php if (isset($errors['language'])): ?>
                <p class="mt-1 text-sm text-red-600"><?php echo $errors['language']; ?></p>
              <?php endif; ?>
            </div>

            <!-- Age Group -->
            <div class="mb-4">
              <label for="age_group" class="block text-sm font-medium text-gray-700 mb-1">Age Group</label>
              <select id="age_group" name="age_group"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="all" <?php echo $story['age_group'] === 'all' ? 'selected' : ''; ?>>All Ages</option>
                <option value="children" <?php echo $story['age_group'] === 'children' ? 'selected' : ''; ?>>Children</option>
                <option value="adults" <?php echo $story['age_group'] === 'adults' ? 'selected' : ''; ?>>Adults</option>
              </select>
            </div>

            <!-- Featured -->
            <div class="mb-4">
              <label class="inline-flex items-center">
                <input type="checkbox" name="is_featured" class="rounded text-blue-600 focus:ring-blue-500"
                  <?php echo $story['is_featured'] ? 'checked' : ''; ?>>
                <span class="ml-2 text-sm text-gray-700">Feature this story</span>
              </label>
              <p class="mt-1 text-xs text-gray-500">Featured stories appear more prominently in search results.</p>
            </div>

            <!-- Themes -->
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Themes</label>
              <div class="grid grid-cols-2 gap-2">
                <?php foreach ($available_themes as $theme): ?>
                  <label class="inline-flex items-center">
                    <input type="checkbox" name="themes[]" value="<?php echo htmlspecialchars($theme); ?>"
                      class="rounded text-blue-600 focus:ring-blue-500"
                      <?php echo in_array($theme, $current_themes) ? 'checked' : ''; ?>>
                    <span class="ml-2 text-sm text-gray-700"><?php echo $theme; ?></span>
                  </label>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-8 flex justify-end space-x-4">
          <a href="mystory.php" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            Cancel
          </a>
          <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
            Save Changes
          </button>
        </div>
      </form>
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

  <script>
    // Show/hide duration or word count based on media type selection
    const mediaTypeRadios = document.querySelectorAll('input[name="media_type"]');
    const durationField = document.getElementById('duration');
    const wordCountField = document.getElementById('word_count');

    function toggleFields() {
      const selectedType = document.querySelector('input[name="media_type"]:checked').value;

      if (selectedType === 'text') {
        if (durationField) durationField.closest('.mb-4').style.display = 'none';
        if (wordCountField) wordCountField.closest('.mb-4').style.display = 'block';
      } else {
        if (durationField) durationField.closest('.mb-4').style.display = 'block';
        if (wordCountField) wordCountField.closest('.mb-4').style.display = 'none';
      }
    }

    mediaTypeRadios.forEach(radio => {
      radio.addEventListener('change', toggleFields);
    });

    // Initialize on page load
    toggleFields();
  </script>
</body>

</html>