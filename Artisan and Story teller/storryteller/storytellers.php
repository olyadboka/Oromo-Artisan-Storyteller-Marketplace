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

$user_id = 8; // Jirenya Dhugaa for testing

// Fetch storyteller data
$stmt = $conn->prepare("
    SELECT s.*, u.username 
    FROM storytellers s 
    JOIN users u ON s.user_id = u.id 
    WHERE s.user_id = :user_id
");
$stmt->execute(['user_id' => $user_id]);
$storyteller = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle case where storyteller is not found
if (!$storyteller) {
  die("Storyteller not found for user ID: $user_id");
}

// Fetch storyteller specializations
$specializations = $storyteller['specialization'] ? explode(',', $storyteller['specialization']) : [];

// Fetch stories with listen counts and themes
$stmt = $conn->prepare("
    SELECT s.*, COALESCE(sl.listen_count, 0) as listen_count,
           GROUP_CONCAT(st.theme) as themes
    FROM stories s
    LEFT JOIN story_listens sl ON s.id = sl.story_id
    LEFT JOIN story_themes st ON s.id = st.story_id
    WHERE s.storyteller_id = :storyteller_id
    GROUP BY s.id
    ORDER BY s.created_at DESC
    LIMIT 5
");
$stmt->execute(['storyteller_id' => $storyteller['id']]);
$stories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch total story count
$stmt = $conn->prepare("
    SELECT COUNT(*) as total_stories 
    FROM stories 
    WHERE storyteller_id = :storyteller_id
");
$stmt->execute(['storyteller_id' => $storyteller['id']]);
$total_stories = $stmt->fetchColumn();

// Fetch monthly listeners
$stmt = $conn->prepare("
    SELECT SUM(sl.listen_count) as total_listens
    FROM story_listens sl
    JOIN stories s ON sl.story_id = s.id
    WHERE s.storyteller_id = :storyteller_id
    AND sl.last_updated >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
");
$stmt->execute(['storyteller_id' => $storyteller['id']]);
$monthly_listeners = $stmt->fetchColumn() ?: 0;

// Fetch earnings
$stmt = $conn->prepare("
    SELECT SUM(amount) as total_earnings
    FROM earnings
    WHERE storyteller_id = :storyteller_id
    AND period_start >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
");
$stmt->execute(['storyteller_id' => $storyteller['id']]);
$earnings = $stmt->fetchColumn() ?: 0;

// Fetch upcoming events
$stmt = $conn->prepare("
    SELECT *
    FROM events
    WHERE storyteller_id = :storyteller_id
    AND event_date >= CURDATE()
    ORDER BY event_date ASC
    LIMIT 2
");
$stmt->execute(['storyteller_id' => $storyteller['id']]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch community engagement
$stmt = $conn->prepare("
    SELECT type, COUNT(*) as count
    FROM community_engagement
    WHERE storyteller_id = :storyteller_id
    GROUP BY type
");
$stmt->execute(['storyteller_id' => $storyteller['id']]);
$engagement = $stmt->fetchAll(PDO::FETCH_ASSOC);
$story_requests = 0;
$questions = 0;
foreach ($engagement as $eng) {
  if ($eng['type'] == 'story_request') $story_requests = $eng['count'];
  if ($eng['type'] == 'question') $questions = $eng['count'];
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Oromo Storyteller Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    .storyteller-header {
      background: linear-gradient(135deg, #1e3a8a 0%, #7c2d12 100%);
    }

    .story-card:hover .story-actions {
      opacity: 1;
    }

    .story-actions {
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .media-icon {
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
    }

    .audio-icon {
      background-color: rgba(59, 130, 246, 0.1);
      color: #3b82f6;
    }

    .video-icon {
      background-color: rgba(220, 38, 38, 0.1);
      color: #dc2626;
    }

    .text-icon {
      background-color: rgba(5, 150, 105, 0.1);
      color: #059669;
    }
  </style>
</head>

<body class="bg-gray-50">

  </div>
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


  </div>
  <a href="#" class="mt-4 inline-block text-blue-600 hover:text-blue-800 text-sm font-medium">
    Withdraw funds <i class="fas fa-arrow-right ml-1"></i>
  </a>
  </div>
  </div>

  <!-- Quick Actions -->
  <div class="bg-white rounded-xl shadow p-6 mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-6">Quick Actions</h2>
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <a href="add_story.php"
        class="border-2 border-dashed border-gray-200 hover:border-blue-300 rounded-lg p-5 text-center transition-colors">
        <div class="text-blue-600 mb-3"><i class="fas fa-microphone text-3xl"></i></div>
        <h3 class="font-medium text-gray-800">Record New Story</h3>
        <p class="text-sm text-gray-500 mt-1">Audio or video</p>
      </a>
      <a href="events.php"
        class="border-2 border-dashed border-gray-200 hover:border-blue-300 rounded-lg p-5 text-center transition-colors">
        <div class="text-red-600 mb-3"><i class="fas fa-calendar-plus text-3xl"></i></div>
        <h3 class="font-medium text-gray-800">Schedule Events</h3>
        <p class="text-sm text-gray-500 mt-1">Live or virtual</p>
      </a>
      <a href="add_story.php"
        class="border-2 border-dashed border-gray-200 hover:border-blue-300 rounded-lg p-5 text-center transition-colors">
        <div class="text-green-600 mb-3"><i class="fas fa-pen-fancy text-3xl"></i></div>
        <h3 class="font-medium text-gray-800">Write Story</h3>
        <p class="text-sm text-gray-500 mt-1">Text version</p>
      </a>
    </div>
  </div>

  <!-- Recent Stories & Activities -->
  <div class="flex flex-col lg:flex-row gap-8">
    <!-- Recent Stories -->
    <div class="lg:w-2/3">
      <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-800">Recent Stories</h2>
            <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">View All</a>
          </div>
        </div>
        <div class="divide-y divide-gray-200">
          <?php foreach ($stories as $story): ?>
            <div class="story-card p-6 hover:bg-gray-50 transition">
              <div class="flex items-start">
                <div
                  class="media-icon <?php echo $story['media_type'] == 'audio' ? 'audio-icon' : ($story['media_type'] == 'video' ? 'video-icon' : 'text-icon'); ?> mr-4">
                  <i
                    class="fas fa-<?php echo $story['media_type'] == 'audio' ? 'music' : ($story['media_type'] == 'video' ? 'video' : 'file-alt'); ?>"></i>
                </div>
                <div class="flex-1">
                  <h3 class="font-bold text-lg text-gray-800"><?php echo htmlspecialchars($story['title']); ?></h3>
                  <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($story['description']); ?></p>
                  <div class="flex items-center mt-3 text-sm text-gray-500">
                    <span class="mr-4"><i class="fas fa-tag mr-1"></i>
                      <?php echo htmlspecialchars($story['themes'] ?? $storyteller['specialization']); ?></span>
                    <span class="mr-4"><i class="fas fa-language mr-1"></i> <?php echo $story['language']; ?></span>
                    <span><i class="fas fa-clock mr-1"></i>
                      <?php echo $story['duration'] ? ($story['duration'] . ' min') : ($story['word_count'] . ' words'); ?></span>
                  </div>
                </div>
                <div class="story-actions flex space-x-2 ml-4">
                  <button class="p-2 text-blue-600 hover:bg-blue-50 rounded-full">
                    <i class="fas fa-edit"></i>
                  </button>
                  <button class="p-2 text-red-600 hover:bg-red-50 rounded-full">
                    <i class="fas fa-trash"></i>
                  </button>
                  <button class="p-2 text-green-600 hover:bg-green-50 rounded-full">
                    <i class="fas fa-share-alt"></i>
                  </button>
                </div>
              </div>
              <div class="mt-4 flex items-center justify-between">
                <div class="flex items-center">
                  <div class="flex items-center text-yellow-400 mr-3">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                  </div>
                  <span class="text-sm text-gray-500"><?php echo $story['listen_count']; ?>
                    <?php echo $story['media_type'] == 'video' ? 'views' : 'listens'; ?></span>
                </div>
                <a href="mystory.php" class="text-sm text-blue-600 hover:text-blue-800">View details →</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 text-center">
          <a href="add_story.php" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-plus-circle mr-2"></i> Add New Story
          </a>
        </div>
      </div>
    </div>

    <!-- Upcoming Events & Quick Tools -->
    <div class="lg:w-1/3 space-y-6">
      <!-- Upcoming Events -->
      <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-xl font-bold text-gray-800">Upcoming Performances</h2>
        </div>
        <div class="p-6">
          <div class="space-y-4">
            <?php foreach ($events as $event): ?>
              <div class="flex items-start">
                <div class="bg-blue-100 text-blue-800 rounded-lg p-3 text-center mr-4">
                  <div class="font-bold"><?php echo date('d', strtotime($event['event_date'])); ?></div>
                  <div class="text-xs uppercase"><?php echo date('M', strtotime($event['event_date'])); ?></div>
                </div>
                <div>
                  <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($event['title']); ?></h3>
                  <p class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($event['description']); ?></p>
                  <div class="mt-2 flex items-center text-sm text-gray-500">
                    <i class="fas fa-clock mr-1"></i>
                    <?php echo date('h:i A', strtotime($event['start_time'])) . ' - ' . date('h:i A', strtotime($event['end_time'])); ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          <a href="events.php">
            <button class="mt-6 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium">
              <i class="fas fa-plus mr-2"></i> Schedule New Events
            </button>
          </a>
        </div>
      </div>


    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Translate to</label>
      <select
        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
        <option>Afaan Oromo</option>
        <option>English</option>
        <option>Amharic</option>
      </select>
    </div>
    <button class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-medium">
      <i class="fas fa-language mr-2"></i> Generate Translation
    </button>
  </div>
  </div>


  <!-- Community Engagement -->
  <div class="bg-white rounded-xl shadow p-6">
    <h2 class="text-xl font-bold text-gray-800 mb-4">Community</h2>
    <div class="space-y-4">
      <div class="flex items-center p-3 bg-blue-50 rounded-lg">
        <div class="bg-blue-100 text-blue-800 p-2 rounded-full mr-3">
          <i class="fas fa-comments"></i>
        </div>
        <div>
          <h3 class="font-medium"><?php echo $story_requests; ?> New Story Requests</h3>
          <p class="text-sm text-gray-600">From listeners worldwide</p>
        </div>

        <!-- Recent Stories & Activities -->
        <div class="flex flex-col lg:flex-row gap-8">
          <!-- Recent Stories -->
          <div class="lg:w-2/3">
            <div class="bg-white rounded-xl shadow overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                  <h2 class="text-xl font-bold text-gray-800">Recent Stories</h2>
                  <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">View All</a>
                </div>
              </div>
              <div class="divide-y divide-gray-200">
                <?php foreach ($stories as $story): ?>
                  <div class="story-card p-6 hover:bg-gray-50 transition">
                    <div class="flex items-start">
                      <div class="media-icon <?php echo $story['media_type'] == 'audio' ? 'audio-icon' : ($story['media_type'] == 'video' ? 'video-icon' : 'text-icon'); ?> mr-4">
                        <i class="fas fa-<?php echo $story['media_type'] == 'audio' ? 'music' : ($story['media_type'] == 'video' ? 'video' : 'file-alt'); ?>"></i>
                      </div>
                      <div class="flex-1">
                        <h3 class="font-bold text-lg text-gray-800"><?php echo htmlspecialchars($story['title']); ?></h3>
                        <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($story['description']); ?></p>
                        <div class="flex items-center mt-3 text-sm text-gray-500">
                          <span class="mr-4"><i class="fas fa-tag mr-1"></i> <?php echo htmlspecialchars($story['themes'] ?? $storyteller['specialization']); ?></span>
                          <span class="mr-4"><i class="fas fa-language mr-1"></i> <?php echo $story['language']; ?></span>
                          <span><i class="fas fa-clock mr-1"></i> <?php echo $story['duration'] ? ($story['duration'] . ' min') : ($story['word_count'] . ' words'); ?></span>
                        </div>
                      </div>
                      <!-- <div class="story-actions flex space-x-2 ml-4">
                                        <button class="p-2 text-blue-600 hover:bg-blue-50 rounded-full">
                                            <a href="edit_story.php">
                                                <i class="fas fa-edit"></i></a>
                                        </button>
                                        <button class="p-2 text-red-600 hover:bg-red-50 rounded-full">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <button class="p-2 text-green-600 hover:bg-green-50 rounded-full">
                                            <i class="fas fa-share-alt"></i>
                                        </button>
                                    </div> -->
                    </div>
                    <div class="mt-4 flex items-center justify-between">
                      <div class="flex items-center">
                        <div class="flex items-center text-yellow-400 mr-3">
                          <i class="fas fa-star"></i>
                          <i class="fas fa-star"></i>
                          <i class="fas fa-star"></i>
                          <i class="fas fa-star"></i>
                          <i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="text-sm text-gray-500"><?php echo $story['listen_count']; ?> <?php echo $story['media_type'] == 'video' ? 'views' : 'listens'; ?></span>
                      </div>
                      <a href="mystory.php" class="text-sm text-blue-600 hover:text-blue-800">View details →</a>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
              <div class="px-6 py-4 border-t border-gray-200 text-center">
                <a href="add_story.php" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                  <i class="fas fa-plus-circle mr-2"></i> Add New Story
                </a>
              </div>
            </div>
          </div>
          <div class="flex items-center p-3 bg-green-50 rounded-lg">
            <div class="bg-green-100 text-green-800 p-2 rounded-full mr-3">
              <i class="fas fa-question-circle"></i>
            </div>
            <div>
              <h3 class="font-medium"><?php echo $questions; ?> Questions</h3>
              <p class="text-sm text-gray-600">About your stories</p>
            </div>
          </div>
          <a href="community.php" class="block text-center text-blue-600 hover:text-blue-800 font-medium mt-4">
            View All Engagement <i class="fas fa-arrow-right ml-1"></i>
          </a>
        </div>
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
    document.addEventListener('DOMContentLoaded', function() {
      const storyCards = document.querySelectorAll('.story-card');
      storyCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.querySelector('.story-actions').style.opacity = '1';
        });
        card.addEventListener('mouseleave', function() {
          this.querySelector('.story-actions').style.opacity = '0';
        });
      });
    });
  </script>
</body>

</html>