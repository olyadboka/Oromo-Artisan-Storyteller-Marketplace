<?php
// Start session for user authentication and messages
session_start();

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

// Assume logged-in user (replace with proper authentication in production)
$user_id = 7; // Jirenya Dhugaa for testing
$_SESSION['user_id'] = $user_id; // Simulate session

// Initialize message variables
$success_message = '';
$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['action'])) {
    $action = $_POST['action'];

    // Add new event
    if ($action === 'add_event') {
      $title = trim($_POST['title'] ?? '');
      $description = trim($_POST['description'] ?? '');
      $event_date = $_POST['event_date'] ?? '';
      $start_time = $_POST['start_time'] ?? '';
      $end_time = $_POST['end_time'] ?? '';
      $location = trim($_POST['location'] ?? '');
      $is_virtual = isset($_POST['is_virtual']) ? 1 : 0;

      // Basic validation
      if (empty($title) || empty($event_date) || empty($start_time) || empty($end_time) || empty($location)) {
        $error_message = "Please fill in all required fields.";
      } elseif (strtotime($start_time) >= strtotime($end_time)) {
        $error_message = "End time must be after start time.";
      } else {
        try {
          $stmt = $conn->prepare("
                        INSERT INTO events (storyteller_id, title, description, event_date, start_time, end_time, location, is_virtual, created_at)
                        VALUES (:storyteller_id, :title, :description, :event_date, :start_time, :end_time, :location, :is_virtual, NOW())
                    ");
          $stmt->execute([
            'storyteller_id' => $_POST['storyteller_id'],
            'title' => $title,
            'description' => $description,
            'event_date' => $event_date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'location' => $location,
            'is_virtual' => $is_virtual
          ]);
          $success_message = "Event added successfully!";
        } catch (PDOException $e) {
          $error_message = "Failed to add event: " . $e->getMessage();
        }
      }
    }

    // Edit event
    if ($action === 'edit_event') {
      $event_id = $_POST['event_id'] ?? 0;
      $title = trim($_POST['title'] ?? '');
      $description = trim($_POST['description'] ?? '');
      $event_date = $_POST['event_date'] ?? '';
      $start_time = $_POST['start_time'] ?? '';
      $end_time = $_POST['end_time'] ?? '';
      $location = trim($_POST['location'] ?? '');
      $is_virtual = isset($_POST['is_virtual']) ? 1 : 0;

      // Basic validation
      if (empty($title) || empty($event_date) || empty($start_time) || empty($end_time) || empty($location)) {
        $error_message = "Please fill in all required fields.";
      } elseif (strtotime($start_time) >= strtotime($end_time)) {
        $error_message = "End time must be after start time.";
      } else {
        try {
          $stmt = $conn->prepare("
                        UPDATE events 
                        SET title = :title, description = :description, event_date = :event_date, 
                            start_time = :start_time, end_time = :end_time, location = :location, 
                            is_virtual = :is_virtual
                        WHERE id = :event_id AND storyteller_id = :storyteller_id
                    ");
          $stmt->execute([
            'title' => $title,
            'description' => $description,
            'event_date' => $event_date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'location' => $location,
            'is_virtual' => $is_virtual,
            'event_id' => $event_id,
            'storyteller_id' => $_POST['storyteller_id']
          ]);
          $success_message = "Event updated successfully!";
        } catch (PDOException $e) {
          $error_message = "Failed to update event: " . $e->getMessage();
        }
      }
    }

    // Delete event
    if ($action === 'delete_event') {
      $event_id = $_POST['event_id'] ?? 0;
      try {
        $stmt = $conn->prepare("
                    DELETE FROM events 
                    WHERE id = :event_id AND storyteller_id = :storyteller_id
                ");
        $stmt->execute([
          'event_id' => $event_id,
          'storyteller_id' => $_POST['storyteller_id']
        ]);
        $success_message = "Event deleted successfully!";
      } catch (PDOException $e) {
        $error_message = "Failed to delete event: " . $e->getMessage();
      }
    }
  }
}

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

// Fetch upcoming events
$stmt = $conn->prepare("
    SELECT *
    FROM events
    WHERE storyteller_id = :storyteller_id
    AND event_date >= CURDATE()
    ORDER BY event_date ASC
");
$stmt->execute(['storyteller_id' => $storyteller['id']]);
$upcoming_events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch past events
$stmt = $conn->prepare("
    SELECT *
    FROM events
    WHERE storyteller_id = :storyteller_id
    AND event_date < CURDATE()
    ORDER BY event_date DESC
");
$stmt->execute(['storyteller_id' => $storyteller['id']]);
$past_events = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch event for editing if requested
$edit_event = null;
if (isset($_GET['edit_event_id'])) {
  $stmt = $conn->prepare("
        SELECT *
        FROM events
        WHERE id = :event_id AND storyteller_id = :storyteller_id
    ");
  $stmt->execute([
    'event_id' => $_GET['edit_event_id'],
    'storyteller_id' => $storyteller['id']
  ]);
  $edit_event = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Events - Oromo Storyteller Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    .storyteller-header {
      background: linear-gradient(135deg, #1e3a8a 0%, #7c2d12 100%);
    }

    .event-card:hover .event-actions {
      opacity: 1;
    }

    .event-actions {
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .event-icon {
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      background-color: rgba(59, 130, 246, 0.1);
      color: #3b82f6;
    }

    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 50;
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background-color: white;
      border-radius: 0.5rem;
      max-width: 600px;
      width: 90%;
      max-height: 80vh;
      overflow-y: auto;
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
        <a href="storytellers.php" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-book-open mr-2"></i> My Stories
        </a>
        <a href="events.php" class="px-6 py-4 font-medium text-blue-800 border-b-2 border-blue-800">
          <i class="fas fa-calendar-alt mr-2"></i> Events
        </a>
        <a href="community.php" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-comments mr-2"></i> Community
        </a>
        <a href="analytics.php" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-chart-line mr-2"></i> Analytics
        </a>
        <a href="earnings.php" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-wallet mr-2"></i> Earnings
        </a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="container mx-auto px-4 py-8">
    <!-- Messages -->
    <?php if ($success_message): ?>
      <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg flex items-center">
        <i class="fas fa-check-circle mr-2"></i> <?php echo htmlspecialchars($success_message); ?>
      </div>
    <?php endif; ?>
    <?php if ($error_message): ?>
      <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg flex items-center">
        <i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars($error_message); ?>
      </div>
    <?php endif; ?>

    <!-- Add/Edit Event Form Modal -->
    <div id="eventModal" class="modal">
      <div class="modal-content p-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-xl font-bold text-gray-800"><?php echo $edit_event ? 'Edit Event' : 'Add New Event'; ?></h2>
          <button id="closeModal" class="text-gray-600 hover:text-gray-800">
            <i class="fas fa-times"></i>
          </button>
        </div>
        <form action="events.php" method="POST">
          <input type="hidden" name="action" value="<?php echo $edit_event ? 'edit_event' : 'add_event'; ?>">
          <input type="hidden" name="storyteller_id" value="<?php echo $storyteller['id']; ?>">
          <?php if ($edit_event): ?>
            <input type="hidden" name="event_id" value="<?php echo $edit_event['id']; ?>">
          <?php endif; ?>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Event Title <span class="text-red-600">*</span></label>
              <input type="text" name="title" value="<?php echo htmlspecialchars($edit_event['title'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
              <textarea name="description" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500" rows="4"><?php echo htmlspecialchars($edit_event['description'] ?? ''); ?></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Event Date <span class="text-red-600">*</span></label>
              <input type="date" name="event_date" value="<?php echo htmlspecialchars($edit_event['event_date'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Time <span class="text-red-600">*</span></label>
                <input type="time" name="start_time" value="<?php echo htmlspecialchars($edit_event['start_time'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500" required>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Time <span class="text-red-600">*</span></label>
                <input type="time" name="end_time" value="<?php echo htmlspecialchars($edit_event['end_time'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500" required>
              </div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Location <span class="text-red-600">*</span></label>
              <input type="text" name="location" value="<?php echo htmlspecialchars($edit_event['location'] ?? ''); ?>" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div>
              <label class="flex items-center">
                <input type="checkbox" name="is_virtual" <?php echo ($edit_event && $edit_event['is_virtual']) ? 'checked' : ''; ?> class="mr-2">
                <span class="text-sm font-medium text-gray-700">Virtual Event</span>
              </label>
            </div>
            <div class="flex justify-end space-x-2">
              <button type="button" id="cancelModal" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">Cancel</button>
              <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-save mr-2"></i> <?php echo $edit_event ? 'Update Event' : 'Add Event'; ?>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Upcoming Events -->
    <div class="bg-white rounded-xl shadow overflow-hidden mb-8">
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-800">Upcoming Events</h2>
          <button id="openAddModal" class="text-blue-600 hover:text-blue-800 font-medium">
            <i class="fas fa-plus-circle mr-1"></i> Schedule New Event
          </button>
        </div>
      </div>
      <div class="divide-y divide-gray-200">
        <?php if (empty($upcoming_events)): ?>
          <div class="p-6 text-center text-gray-500">
            No upcoming events scheduled.
          </div>
        <?php else: ?>
          <?php foreach ($upcoming_events as $event): ?>
            <div class="event-card p-6 hover:bg-gray-50 transition">
              <div class="flex items-start">
                <div class="event-icon mr-4">
                  <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="flex-1">
                  <h3 class="font-bold text-lg text-gray-800"><?php echo htmlspecialchars($event['title']); ?></h3>
                  <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($event['description']); ?></p>
                  <div class="flex items-center mt-3 text-sm text-gray-500">
                    <span class="mr-4"><i class="fas fa-map-marker-alt mr-1"></i> <?php echo htmlspecialchars($event['location']); ?></span>
                    <span class="mr-4"><i class="fas fa-clock mr-1"></i> <?php echo date('h:i A', strtotime($event['start_time'])) . ' - ' . date('h:i A', strtotime($event['end_time'])); ?></span>
                    <span><i class="fas fa-globe mr-1"></i> <?php echo $event['is_virtual'] ? 'Virtual' : 'In-Person'; ?></span>
                  </div>
                  <div class="mt-2 text-sm text-gray-500">
                    <i class="fas fa-calendar-day mr-1"></i> <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                  </div>
                </div>
                <div class="event-actions flex space-x-2 ml-4">
                  <a href="events.php?edit_event_id=<?php echo $event['id']; ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-full">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form action="events.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this event?');">
                    <input type="hidden" name="action" value="delete_event">
                    <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                    <input type="hidden" name="storyteller_id" value="<?php echo $storyteller['id']; ?>">
                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-full">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Past Events -->
    <div class="bg-white rounded-xl shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h2 class="text-xl font-bold text-gray-800">Past Events</h2>
          <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">View All</a>
        </div>
      </div>
      <div class="divide-y divide-gray-200">
        <?php if (empty($past_events)): ?>
          <div class="p-6 text-center text-gray-500">
            No past events found.
          </div>
        <?php else: ?>
          <?php foreach ($past_events as $event): ?>
            <div class="event-card p-6 hover:bg-gray-50 transition">
              <div class="flex items-start">
                <div class="event-icon mr-4">
                  <i class="fas fa-calendar-check"></i>
                </div>
                <div class="flex-1">
                  <h3 class="font-bold text-lg text-gray-800"><?php echo htmlspecialchars($event['title']); ?></h3>
                  <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($event['description']); ?></p>
                  <div class="flex items-center mt-3 text-sm text-gray-500">
                    <span class="mr-4"><i class="fas fa-map-marker-alt mr-1"></i> <?php echo htmlspecialchars($event['location']); ?></span>
                    <span class="mr-4"><i class="fas fa-clock mr-1"></i> <?php echo date('h:i A', strtotime($event['start_time'])) . ' - ' . date('h:i A', strtotime($event['end_time'])); ?></span>
                    <span><i class="fas fa-globe mr-1"></i> <?php echo $event['is_virtual'] ? 'Virtual' : 'In-Person'; ?></span>
                  </div>
                  <div class="mt-2 text-sm text-gray-500">
                    <i class="fas fa-calendar-day mr-1"></i> <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                  </div>
                </div>
                <div class="event-actions flex space-x-2 ml-4">
                  <a href="events.php?edit_event_id=<?php echo $event['id']; ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-full">
                    <i class="fas fa-edit"></i>
                  </a>
                  <form action="events.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this event?');">
                    <input type="hidden" name="action" value="delete_event">
                    <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                    <input type="hidden" name="storyteller_id" value="<?php echo $storyteller['id']; ?>">
                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-full">
                      <i class="fas fa-trash"></i>
                    </button>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
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
      // Event card hover effects
      const eventCards = document.querySelectorAll('.event-card');
      eventCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
          this.querySelector('.event-actions').style.opacity = '1';
        });
        card.addEventListener('mouseleave', function() {
          this.querySelector('.event-actions').style.opacity = '0';
        });
      });

      // Modal handling
      const modal = document.getElementById('eventModal');
      const openAddModal = document.getElementById('openAddModal');
      const closeModal = document.getElementById('closeModal');
      const cancelModal = document.getElementById('cancelModal');

      openAddModal.addEventListener('click', function() {
        modal.style.display = 'flex';
      });

      closeModal.addEventListener('click', function() {
        modal.style.display = 'none';
        window.location.href = 'events.php'; // Reset URL
      });

      cancelModal.addEventListener('click', function() {
        modal.style.display = 'none';
        window.location.href = 'events.php'; // Reset URL
      });

      // Auto-open modal for editing
      <?php if ($edit_event): ?>
        modal.style.display = 'flex';
      <?php endif; ?>
    });
  </script>
</body>

</html>