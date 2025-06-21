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

// Initialize variables
$errors = [];
$success = false;
$engagement_type = 'story_request';
$content = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $engagement_type = $_POST['engagement_type'] ?? 'story_request';
  $content = trim($_POST['content'] ?? '');

  // Validate input
  if (empty($content)) {
    $errors['content'] = 'Content is required';
  } elseif (strlen($content) > 1000) {
    $errors['content'] = 'Content must be less than 1000 characters';
  }

  if (empty($errors)) {
    try {
      $stmt = $conn->prepare("
                INSERT INTO community_engagement 
                (storyteller_id, type, content) 
                VALUES (:storyteller_id, :type, :content)
            ");
      $stmt->execute([
        'storyteller_id' => $storyteller['id'],
        'type' => $engagement_type,
        'content' => $content
      ]);

      $success = true;
      $content = ''; // Clear form on success
    } catch (PDOException $e) {
      $errors['database'] = 'Failed to save engagement: ' . $e->getMessage();
    }
  }
}

// Fetch community engagements
$engagements = [];
try {
  $stmt = $conn->prepare("
        SELECT ce.*, u.username, s.artistic_name, s.profile_image_url
        FROM community_engagement ce
        JOIN storytellers s ON ce.storyteller_id = s.id
        JOIN users u ON s.user_id = u.id
        ORDER BY ce.created_at DESC
        LIMIT 50
    ");
  $stmt->execute();
  $engagements = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  $errors['database'] = 'Failed to fetch engagements: ' . $e->getMessage();
}

// Count engagements by type for the current storyteller
$engagement_counts = ['story_request' => 0, 'question' => 0];
try {
  $stmt = $conn->prepare("
        SELECT type, COUNT(*) as count
        FROM community_engagement
        WHERE storyteller_id = :storyteller_id
        GROUP BY type
    ");
  $stmt->execute(['storyteller_id' => $storyteller['id']]);
  $counts = $stmt->fetchAll(PDO::FETCH_ASSOC);

  foreach ($counts as $count) {
    $engagement_counts[$count['type']] = $count['count'];
  }
} catch (PDOException $e) {
  // Silently fail, counts are not critical
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Community - Oromo Storyteller Network</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    .storyteller-header {
      background: linear-gradient(135deg, #1e3a8a 0%, #7c2d12 100%);
    }

    .engagement-story_request {
      border-left: 4px solid #3B82F6;
    }

    .engagement-question {
      border-left: 4px solid #10B981;
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
        <a href="mystory.php" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-book-open mr-2"></i> My Stories
        </a>
        <a href="events.php" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-calendar-alt mr-2"></i> Events
        </a>
        <a href="community.php" class="px-6 py-4 font-medium text-blue-800 border-b-2 border-blue-800">
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
      <h1 class="text-2xl font-bold text-gray-800">Community Engagement</h1>
      <div class="flex space-x-4">
        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
          <i class="fas fa-book mr-1"></i> <?php echo $engagement_counts['story_request']; ?> Requests
        </span>
        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
          <i class="fas fa-question mr-1"></i> <?php echo $engagement_counts['question']; ?> Questions
        </span>
      </div>
    </div>

    <?php if ($success): ?>
      <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
        <p>Your engagement has been posted successfully!</p>
      </div>
    <?php endif; ?>

    <?php if (!empty($errors['database'])): ?>
      <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
        <p><?php echo $errors['database']; ?></p>
      </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
      <!-- Left Column - New Engagement Form -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow p-6 sticky top-4">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Create New Engagement</h2>

          <form method="post">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Engagement Type *</label>
              <div class="grid grid-cols-2 gap-2">
                <label class="flex items-center space-x-2 p-3 border rounded-lg cursor-pointer <?php echo $engagement_type === 'story_request' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'; ?>">
                  <input type="radio" name="engagement_type" value="story_request" class="text-blue-600 focus:ring-blue-500"
                    <?php echo $engagement_type === 'story_request' ? 'checked' : ''; ?>>
                  <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-book text-sm"></i>
                  </div>
                  <span>Story Request</span>
                </label>
                <label class="flex items-center space-x-2 p-3 border rounded-lg cursor-pointer <?php echo $engagement_type === 'question' ? 'border-blue-500 bg-blue-50' : 'border-gray-300'; ?>">
                  <input type="radio" name="engagement_type" value="question" class="text-blue-600 focus:ring-blue-500"
                    <?php echo $engagement_type === 'question' ? 'checked' : ''; ?>>
                  <div class="w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                    <i class="fas fa-question text-sm"></i>
                  </div>
                  <span>Question</span>
                </label>
              </div>
            </div>

            <div class="mb-4">
              <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content *</label>
              <textarea id="content" name="content" rows="5"
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500 <?php echo isset($errors['content']) ? 'border-red-500' : ''; ?>"
                placeholder="<?php echo $engagement_type === 'story_request' ? 'What story would you like to hear?' : 'Ask your question here...'; ?>"><?php echo htmlspecialchars($content); ?></textarea>
              <?php if (isset($errors['content'])): ?>
                <p class="mt-1 text-sm text-red-600"><?php echo $errors['content']; ?></p>
              <?php endif; ?>
            </div>

            <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
              <i class="fas fa-paper-plane mr-2"></i> Post to Community
            </button>
          </form>
        </div>
      </div>

      <!-- Right Column - Engagement Feed -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-bold text-gray-800">Community Feed</h2>
            <p class="text-sm text-gray-600">Recent engagements from the community</p>
          </div>

          <?php if (empty($engagements)): ?>
            <div class="p-8 text-center">
              <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
              <h3 class="text-lg font-medium text-gray-700">No engagements yet</h3>
              <p class="text-gray-500 mt-2">Be the first to start a conversation!</p>
            </div>
          <?php else: ?>
            <div class="divide-y divide-gray-200">
              <?php foreach ($engagements as $engagement): ?>
                <div class="p-6 engagement-<?php echo $engagement['type']; ?>">
                  <div class="flex items-start space-x-4">
                    <img src="<?php echo htmlspecialchars($engagement['profile_image_url'] ?? 'uploads/profiles/default-profile.jpg'); ?>"
                      alt="<?php echo htmlspecialchars($engagement['artistic_name']); ?>"
                      class="w-12 h-12 rounded-full object-cover">
                    <div class="flex-1">
                      <div class="flex items-center justify-between">
                        <h3 class="font-medium text-gray-900">
                          <?php echo htmlspecialchars($engagement['artistic_name']); ?>
                          <span class="text-sm text-gray-500 ml-2">@<?php echo htmlspecialchars($engagement['username']); ?></span>
                        </h3>
                        <span class="text-xs text-gray-500">
                          <?php echo date('M j, Y g:i a', strtotime($engagement['created_at'])); ?>
                        </span>
                      </div>
                      <div class="mt-1 flex items-center space-x-2">
                        <?php if ($engagement['type'] === 'story_request'): ?>
                          <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                            <i class="fas fa-book mr-1"></i> Story Request
                          </span>
                        <?php else: ?>
                          <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                            <i class="fas fa-question mr-1"></i> Question
                          </span>
                        <?php endif; ?>
                      </div>
                      <p class="mt-2 text-gray-700 whitespace-pre-wrap"><?php echo htmlspecialchars($engagement['content']); ?></p>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
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
    // Update placeholder text when engagement type changes
    const engagementTypeRadios = document.querySelectorAll('input[name="engagement_type"]');
    const contentTextarea = document.getElementById('content');

    engagementTypeRadios.forEach(radio => {
      radio.addEventListener('change', function() {
        if (this.value === 'story_request') {
          contentTextarea.placeholder = 'What story would you like to hear?';
        } else {
          contentTextarea.placeholder = 'Ask your question here...';
        }
      });
    });
  </script>
</body>

</html>