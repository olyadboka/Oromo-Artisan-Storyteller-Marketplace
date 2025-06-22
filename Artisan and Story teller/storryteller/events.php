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

// File upload configuration
$upload_dir = 'uploads/events/';
$allowed_image_types = ['jpg', 'jpeg', 'png', 'gif'];
$allowed_video_types = ['mp4', 'mov', 'avi'];
$max_file_size = 10 * 1024 * 1024; // 10MB

// Create upload directory if not exists
if (!file_exists($upload_dir)) {
  mkdir($upload_dir, 0777, true);
}

// Initialize message variables
$success_message = '';
$error_message = '';

// Get logged-in user ID from session
$user_id = 8;
// $_SESSION['user_id'] ?? null;  --- after login page 

if (!$user_id) {
  header("Location: login.php");
  exit();
}

// Fetch storyteller data
try {
  $stmt = $conn->prepare("
        SELECT s.*, u.username 
        FROM storytellers s 
        JOIN users u ON s.user_id = u.id 
        WHERE s.user_id = :user_id
    ");
  $stmt->execute(['user_id' => $user_id]);
  $storyteller = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$storyteller) {
    $_SESSION['error'] = "Storyteller profile not found!";
    header("Location: login.php");
    exit();
  }
} catch (PDOException $e) {
  die("Database error: " . $e->getMessage());
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
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
    $image_url = null;
    $video_url = null;

    // Validate required fields
    if (empty($title) || empty($event_date) || empty($start_time) || empty($end_time) || empty($location)) {
      $error_message = "Please fill in all required fields.";
    } elseif (strtotime($start_time) >= strtotime($end_time)) {
      $error_message = "End time must be after start time.";
    }

    // Handle image upload
    if (empty($error_message) && !empty($_FILES['event_image']['name'])) {
      $image_file = $_FILES['event_image'];
      $image_ext = strtolower(pathinfo($image_file['name'], PATHINFO_EXTENSION));

      if (!in_array($image_ext, $allowed_image_types)) {
        $error_message = "Invalid image format. Allowed: " . implode(', ', $allowed_image_types);
      } elseif ($image_file['size'] > $max_file_size) {
        $error_message = "Image size exceeds 10MB limit.";
      } else {
        $image_name = uniqid('event_img_') . '.' . $image_ext;
        $target_path = $upload_dir . $image_name;

        if (move_uploaded_file($image_file['tmp_name'], $target_path)) {
          $image_url = $target_path;
        } else {
          $error_message = "Failed to upload image.";
        }
      }
    }

    // Handle video upload
    if (empty($error_message) && !empty($_FILES['event_video']['name'])) {
      $video_file = $_FILES['event_video'];
      $video_ext = strtolower(pathinfo($video_file['name'], PATHINFO_EXTENSION));

      if (!in_array($video_ext, $allowed_video_types)) {
        $error_message = "Invalid video format. Allowed: " . implode(', ', $allowed_video_types);
      } elseif ($video_file['size'] > $max_file_size) {
        $error_message = "Video size exceeds 10MB limit.";
      } else {
        $video_name = uniqid('event_vid_') . '.' . $video_ext;
        $target_path = $upload_dir . $video_name;

        if (move_uploaded_file($video_file['tmp_name'], $target_path)) {
          $video_url = $target_path;
        } else {
          $error_message = "Failed to upload video.";
        }
      }
    }

    // Insert event
    if (empty($error_message)) {
      try {
        $stmt = $conn->prepare("
                    INSERT INTO events (
                        storyteller_id, title, description, event_date, 
                        start_time, end_time, location, is_virtual, 
                        image_url, video_url, created_at
                    ) VALUES (
                        :storyteller_id, :title, :description, :event_date, 
                        :start_time, :end_time, :location, :is_virtual, 
                        :image_url, :video_url, NOW()
                    )
                ");

        $stmt->execute([
          'storyteller_id' => $storyteller['id'],
          'title' => $title,
          'description' => $description,
          'event_date' => $event_date,
          'start_time' => $start_time,
          'end_time' => $end_time,
          'location' => $location,
          'is_virtual' => $is_virtual,
          'image_url' => $image_url,
          'video_url' => $video_url
        ]);

        $success_message = "Event added successfully!";
      } catch (PDOException $e) {
        // Clean up uploaded files if database operation fails
        if ($image_url && file_exists($image_url)) {
          unlink($image_url);
        }
        if ($video_url && file_exists($video_url)) {
          unlink($video_url);
        }
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
    $current_image = $_POST['current_image'] ?? null;
    $current_video = $_POST['current_video'] ?? null;
    $image_url = $current_image;
    $video_url = $current_video;

    // Validate required fields
    if (empty($title) || empty($event_date) || empty($start_time) || empty($end_time) || empty($location)) {
      $error_message = "Please fill in all required fields.";
    } elseif (strtotime($start_time) >= strtotime($end_time)) {
      $error_message = "End time must be after start time.";
    }

    // Handle image upload/replacement
    if (empty($error_message) && !empty($_FILES['event_image']['name'])) {
      $image_file = $_FILES['event_image'];
      $image_ext = strtolower(pathinfo($image_file['name'], PATHINFO_EXTENSION));

      if (!in_array($image_ext, $allowed_image_types)) {
        $error_message = "Invalid image format. Allowed: " . implode(', ', $allowed_image_types);
      } elseif ($image_file['size'] > $max_file_size) {
        $error_message = "Image size exceeds 10MB limit.";
      } else {
        // Delete old image if exists
        if ($current_image && file_exists($current_image)) {
          unlink($current_image);
        }

        $image_name = uniqid('event_img_') . '.' . $image_ext;
        $target_path = $upload_dir . $image_name;

        if (move_uploaded_file($image_file['tmp_name'], $target_path)) {
          $image_url = $target_path;
        } else {
          $error_message = "Failed to upload image.";
        }
      }
    }

    // Handle video upload/replacement
    if (empty($error_message) && !empty($_FILES['event_video']['name'])) {
      $video_file = $_FILES['event_video'];
      $video_ext = strtolower(pathinfo($video_file['name'], PATHINFO_EXTENSION));

      if (!in_array($video_ext, $allowed_video_types)) {
        $error_message = "Invalid video format. Allowed: " . implode(', ', $allowed_video_types);
      } elseif ($video_file['size'] > $max_file_size) {
        $error_message = "Video size exceeds 10MB limit.";
      } else {
        // Delete old video if exists
        if ($current_video && file_exists($current_video)) {
          unlink($current_video);
        }

        $video_name = uniqid('event_vid_') . '.' . $video_ext;
        $target_path = $upload_dir . $video_name;

        if (move_uploaded_file($video_file['tmp_name'], $target_path)) {
          $video_url = $target_path;
        } else {
          $error_message = "Failed to upload video.";
        }
      }
    }

    // Update event
    if (empty($error_message)) {
      try {
        $stmt = $conn->prepare("
                    UPDATE events 
                    SET title = :title, description = :description, event_date = :event_date, 
                        start_time = :start_time, end_time = :end_time, location = :location, 
                        is_virtual = :is_virtual, image_url = :image_url, video_url = :video_url
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
          'image_url' => $image_url,
          'video_url' => $video_url,
          'event_id' => $event_id,
          'storyteller_id' => $storyteller['id']
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
      // First get media paths to delete files
      $stmt = $conn->prepare("SELECT image_url, video_url FROM events WHERE id = :event_id");
      $stmt->execute(['event_id' => $event_id]);
      $event = $stmt->fetch(PDO::FETCH_ASSOC);

      // Delete media files if they exist
      if ($event) {
        if ($event['image_url'] && file_exists($event['image_url'])) {
          unlink($event['image_url']);
        }
        if ($event['video_url'] && file_exists($event['video_url'])) {
          unlink($event['video_url']);
        }
      }

      // Then delete the event record
      $stmt = $conn->prepare("DELETE FROM events WHERE id = :event_id AND storyteller_id = :storyteller_id");
      $stmt->execute([
        'event_id' => $event_id,
        'storyteller_id' => $storyteller['id']
      ]);

      $success_message = "Event deleted successfully!";
    } catch (PDOException $e) {
      $error_message = "Failed to delete event: " . $e->getMessage();
    }
  }
}

// Fetch events data
try {
  // Upcoming events
  $stmt = $conn->prepare("
        SELECT *
        FROM events
        WHERE storyteller_id = :storyteller_id
        AND event_date >= CURDATE()
        ORDER BY event_date ASC
    ");
  $stmt->execute(['storyteller_id' => $storyteller['id']]);
  $upcoming_events = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Past events
  $stmt = $conn->prepare("
        SELECT *
        FROM events
        WHERE storyteller_id = :storyteller_id
        AND event_date < CURDATE()
        ORDER BY event_date DESC
    ");
  $stmt->execute(['storyteller_id' => $storyteller['id']]);
  $past_events = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Event for editing if requested
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
} catch (PDOException $e) {
  die("Database error: " . $e->getMessage());
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

    .media-preview {
      max-height: 150px;
      object-fit: contain;
    }

    .nav-link {
      position: relative;
    }

    .nav-link.active:after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      width: 100%;
      height: 2px;
      background-color: #2563eb;
    }

    /* .upcom-event {
      display: grid !important;
      grid-template-columns: repeat(2, 1fr) !important;
      background-color: goldenrod !important;

    } */
  </style>
</head>

<body class="bg-gray-50 font-sans antialiased">
  <!-- Dashboard Header -->
  <header class="storyteller-header text-white shadow-lg">
    <div class="container mx-auto px-4 py-6">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
        <div class="flex items-center space-x-6 mb-6 md:mb-0">
          <img src="<?php echo htmlspecialchars($storyteller['profile_image_url'] ?? 'uploads/profiles/default-profile.jpg'); ?>"
            alt="Storyteller"
            class="w-20 h-20 rounded-full border-4 border-white border-opacity-30 object-cover shadow-lg">
          <div>
            <h1 class="text-2xl md:text-3xl font-bold"><?php echo htmlspecialchars($storyteller['artistic_name'] ?? 'Unknown Storyteller'); ?></h1>
            <p class="text-white text-opacity-90 flex items-center mt-1">
              <i class="fas fa-map-marker-alt mr-2 text-blue-200"></i>
              <?php echo htmlspecialchars($storyteller['location'] ?? 'Unknown Location'); ?>
              <span class="ml-4 px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm flex items-center">
                <i class="fas fa-certificate mr-1 text-yellow-300"></i>
                <?php echo ucfirst($storyteller['verification_status'] ?? 'Pending') ?> Storykeeper
              </span>
            </p>
          </div>
        </div>
        <div class="flex space-x-3">
          <button class="px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg transition flex items-center">
            <i class="fas fa-cog mr-2"></i>
            <span class="hidden md:inline">Settings</span>
          </button>
          <a href="logout.php" class="px-4 py-2 bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg transition flex items-center">
            <i class="fas fa-sign-out-alt mr-2"></i>
            <span class="hidden md:inline">Logout</span>
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Dashboard Navigation -->
  <nav class="bg-white shadow-sm sticky top-0 z-10">
    <div class="container mx-auto px-4">
      <div class="flex overflow-x-auto">
        <a href="storytellers.php" class="nav-link px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-home mr-2"></i> Dashboard
        </a>
        <a href="mystory.php" class="nav-link px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-book-open mr-2"></i> My Stories
        </a>
        <a href="events.php" class="nav-link active px-6 py-4 font-medium text-blue-800">
          <i class="fas fa-calendar-alt mr-2"></i> Events
        </a>
        <a href="community.php" class="nav-link px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-comments mr-2"></i> Community
        </a>
        <a href="analytics.php" class="nav-link px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-chart-line mr-2"></i> Analytics
        </a>
        <a href="earning.php" class="nav-link px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-wallet mr-2"></i> Earnings
        </a>
      </div>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="container mx-auto px-4 py-8">
    <!-- Messages -->
    <?php if ($success_message): ?>
      <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg flex items-center shadow">
        <i class="fas fa-check-circle mr-2 text-green-600"></i>
        <?php echo htmlspecialchars($success_message); ?>
        <button class="ml-auto text-green-700 hover:text-green-900" onclick="this.parentElement.remove()">
          <i class="fas fa-times"></i>
        </button>
      </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
      <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg flex items-center shadow">
        <i class="fas fa-exclamation-circle mr-2 text-red-600"></i>
        <?php echo htmlspecialchars($error_message); ?>
        <button class="ml-auto text-red-700 hover:text-red-900" onclick="this.parentElement.remove()">
          <i class="fas fa-times"></i>
        </button>
      </div>
    <?php endif; ?>

    <!-- Add/Edit Event Form Modal -->
    <div id="eventModal" class="modal">
      <div class="modal-content p-6 shadow-xl">
        <div class="flex justify-between items-center mb-4 border-b pb-4">
          <h2 class="text-xl font-bold text-gray-800">
            <?php echo $edit_event ? 'Edit Event' : 'Add New Event'; ?>
          </h2>
          <button id="closeModal" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times text-xl"></i>
          </button>
        </div>

        <form action="events.php" method="POST" enctype="multipart/form-data" class="space-y-4">
          <input type="hidden" name="action" value="<?php echo $edit_event ? 'edit_event' : 'add_event'; ?>">
          <input type="hidden" name="storyteller_id" value="<?php echo $storyteller['id']; ?>">

          <?php if ($edit_event): ?>
            <input type="hidden" name="event_id" value="<?php echo $edit_event['id']; ?>">
            <input type="hidden" name="current_image" value="<?php echo $edit_event['image_url'] ?? ''; ?>">
            <input type="hidden" name="current_video" value="<?php echo $edit_event['video_url'] ?? ''; ?>">
          <?php endif; ?>

          <div class="space-y-6">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Event Title <span class="text-red-600">*</span>
                </label>
                <input type="text" name="title"
                  value="<?php echo htmlspecialchars($edit_event['title'] ?? ''); ?>"
                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                  required>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3"
                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"><?php echo htmlspecialchars($edit_event['description'] ?? ''); ?></textarea>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Event Date <span class="text-red-600">*</span>
                </label>
                <input type="date" name="event_date"
                  value="<?php echo htmlspecialchars($edit_event['event_date'] ?? ''); ?>"
                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                  required>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Start Time <span class="text-red-600">*</span>
                  </label>
                  <input type="time" name="start_time"
                    value="<?php echo htmlspecialchars($edit_event['start_time'] ?? ''); ?>"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    End Time <span class="text-red-600">*</span>
                  </label>
                  <input type="time" name="end_time"
                    value="<?php echo htmlspecialchars($edit_event['end_time'] ?? ''); ?>"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                    required>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Location <span class="text-red-600">*</span>
                </label>
                <input type="text" name="location"
                  value="<?php echo htmlspecialchars($edit_event['location'] ?? ''); ?>"
                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500"
                  required>
              </div>

              <div>
                <label class="flex items-center space-x-2">
                  <input type="checkbox" name="is_virtual"
                    <?php echo ($edit_event && $edit_event['is_virtual']) ? 'checked' : ''; ?>
                    class="rounded text-blue-600 focus:ring-blue-500">
                  <span class="text-sm font-medium text-gray-700">Virtual Event</span>
                </label>
              </div>
            </div>

            <!-- Media Uploads -->
            <div class="border-t border-gray-200 pt-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Media</h3>

              <!-- Image Upload -->
              <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Image</label>
                <div class="mt-1 flex items-center">
                  <input type="file" name="event_image" id="eventImageInput" accept="image/*"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <?php if ($edit_event && $edit_event['image_url']): ?>
                  <div class="mt-4">
                    <p class="text-sm text-gray-500 mb-2">Current Image:</p>
                    <img src="<?php echo htmlspecialchars($edit_event['image_url']); ?>"
                      class="media-preview rounded-lg border border-gray-200 shadow-sm">
                  </div>
                <?php endif; ?>

                <div id="imagePreview" class="mt-4 hidden">
                  <p class="text-sm text-gray-500 mb-2">New Image Preview:</p>
                  <img id="previewImage" src="#" class="media-preview rounded-lg border border-gray-200 shadow-sm">
                </div>
              </div>

              <!-- Video Upload -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Event Video</label>
                <div class="mt-1 flex items-center">
                  <input type="file" name="event_video" id="eventVideoInput" accept="video/*"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <?php if ($edit_event && $edit_event['video_url']): ?>
                  <div class="mt-4">
                    <p class="text-sm text-gray-500 mb-2">Current Video:</p>
                    <video controls class="media-preview rounded-lg border border-gray-200 shadow-sm">
                      <source src="<?php echo htmlspecialchars($edit_event['video_url']); ?>" type="video/mp4">
                      Your browser does not support the video tag.
                    </video>
                  </div>
                <?php endif; ?>

                <div id="videoPreview" class="mt-4 hidden">
                  <p class="text-sm text-gray-500 mb-2">New Video Preview:</p>
                  <video id="previewVideo" controls class="media-preview rounded-lg border border-gray-200 shadow-sm">
                    Your browser does not support the video tag.
                  </video>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
              <button type="button" id="cancelModal"
                class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 transition">
                Cancel
              </button>
              <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
                <i class="fas fa-save mr-2"></i>
                <?php echo $edit_event ? 'Update Event' : 'Add Event'; ?>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Events Section -->
    <div class="mb-12">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">My Events</h2>
        <button id="openAddModal"
          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition flex items-center">
          <i class="fas fa-plus-circle mr-2"></i> Add New Event
        </button>
      </div>

      <!-- Upcoming Events -->
      <div class="bg-white rounded-xl shadow overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Upcoming Events</h3>
            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
              <?php echo count($upcoming_events); ?> scheduled
            </span>
          </div>
        </div>

        <div class="divide-y divide-gray-200">
          <?php if (empty($upcoming_events)): ?>
            <div class="p-8 text-center">
              <i class="fas fa-calendar-alt text-gray-300 text-4xl mb-3"></i>
              <h4 class="text-lg font-medium text-gray-500">No upcoming events</h4>
              <p class="text-gray-400 mt-1">Schedule your first event to get started</p>
              <button id="openAddModalEmpty"
                class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i> Create Event
              </button>
            </div>
          <?php else: ?>
            <?php foreach ($upcoming_events as $event): ?>
              <div class="event-card p-6 hover:bg-gray-50 transition duration-150 ease-in-out upcom-event">
                <div class="flex flex-col md:flex-row">
                  <!-- Event Date Badge -->
                  <div class="flex-shrink-0 mb-4 md:mb-0 md:mr-6">
                    <div class="flex flex-col items-center justify-center bg-blue-50 rounded-lg p-3 w-20">
                      <span class="text-blue-800 font-bold text-xl">
                        <?php echo date('d', strtotime($event['event_date'])); ?>
                      </span>
                      <span class="text-blue-600 text-sm uppercase">
                        <?php echo date('M', strtotime($event['event_date'])); ?>
                      </span>
                    </div>
                  </div>

                  <!-- Event Details -->
                  <div class="flex-1">
                    <div class="flex items-start justify-between">
                      <div>
                        <h3 class="font-bold text-lg text-gray-800">
                          <?php echo htmlspecialchars($event['title']); ?>
                        </h3>
                        <div class="flex items-center mt-1 text-sm text-gray-500">
                          <span class="flex items-center mr-4">
                            <i class="fas fa-clock mr-1.5 text-blue-500"></i>
                            <?php echo date('h:i A', strtotime($event['start_time'])); ?> - <?php echo date('h:i A', strtotime($event['end_time'])); ?>
                          </span>
                          <span class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-1.5 text-blue-500"></i>
                            <?php echo htmlspecialchars($event['location']); ?>
                          </span>
                        </div>
                      </div>

                      <div class="event-actions flex space-x-2 ml-4">
                        <a href="events.php?edit_event_id=<?php echo $event['id']; ?>"
                          class="p-2 text-blue-600 hover:bg-blue-50 rounded-full transition"
                          title="Edit">
                          <i class="fas fa-edit"></i>
                        </a>
                        <form action="events.php" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this event?');">
                          <input type="hidden" name="action" value="delete_event">
                          <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                          <input type="hidden" name="storyteller_id" value="<?php echo $storyteller['id']; ?>">
                          <button type="submit"
                            class="p-2 text-red-600 hover:bg-red-50 rounded-full transition"
                            title="Delete">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </div>
                    </div>

                    <?php if (!empty($event['description'])): ?>
                      <p class="text-gray-600 mt-2">
                        <?php echo htmlspecialchars($event['description']); ?>
                      </p>
                    <?php endif; ?>

                    <!-- Event Media -->
                    <div class="mt-4 space-y-3">
                      <?php if ($event['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($event['image_url']); ?>"
                          class="max-w-full h-auto rounded-lg border border-gray-200 shadow-sm">
                      <?php endif; ?>

                      <?php if ($event['video_url']): ?>
                        <video controls class="max-w-full rounded-lg border border-gray-200 shadow-sm">
                          <source src="<?php echo htmlspecialchars($event['video_url']); ?>" type="video/mp4">
                          Your browser does not support the video tag.
                        </video>
                      <?php endif; ?>
                    </div>

                    <!-- Event Tags -->
                    <div class="mt-4 flex flex-wrap gap-2">
                      <span class="px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                <?php echo $event['is_virtual'] ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'; ?>">
                        <?php echo $event['is_virtual'] ? 'Virtual Event' : 'In-Person Event'; ?>
                      </span>
                      <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <?php echo date('l, F j, Y', strtotime($event['event_date'])); ?>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Past Events -->
      <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Past Events</h3>
            <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">
              <?php echo count($past_events); ?> events
            </span>
          </div>
        </div>

        <div class="divide-y divide-gray-200 ">
          <?php if (empty($past_events)): ?>
            <div class="p-8 text-center">
              <i class="fas fa-history text-gray-300 text-4xl mb-3"></i>
              <h4 class="text-lg font-medium text-gray-500">No past events found</h4>
              <p class="text-gray-400 mt-1">Your completed events will appear here</p>
            </div>
          <?php else: ?>
            <?php foreach ($past_events as $event): ?>
              <div class="event-card p-6 hover:bg-gray-50 transition duration-150 ease-in-out">
                <div class="flex flex-col md:flex-row">
                  <!-- Event Date Badge -->
                  <div class="flex-shrink-0 mb-4 md:mb-0 md:mr-6">
                    <div class="flex flex-col items-center justify-center bg-gray-100 rounded-lg p-3 w-20">
                      <span class="text-gray-800 font-bold text-xl">
                        <?php echo date('d', strtotime($event['event_date'])); ?>
                      </span>
                      <span class="text-gray-600 text-sm uppercase">
                        <?php echo date('M', strtotime($event['event_date'])); ?>
                      </span>
                    </div>
                  </div>

                  <!-- Event Details -->
                  <div class="flex-1">
                    <div class="flex items-start justify-between">
                      <div>
                        <h3 class="font-bold text-lg text-gray-800">
                          <?php echo htmlspecialchars($event['title']); ?>
                        </h3>
                        <div class="flex items-center mt-1 text-sm text-gray-500">
                          <span class="flex items-center mr-4">
                            <i class="fas fa-clock mr-1.5 text-blue-500"></i>
                            <?php echo date('h:i A', strtotime($event['start_time'])); ?> - <?php echo date('h:i A', strtotime($event['end_time'])); ?>
                          </span>
                          <span class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-1.5 text-blue-500"></i>
                            <?php echo htmlspecialchars($event['location']); ?>
                          </span>
                        </div>
                      </div>

                      <div class="event-actions flex space-x-2 ml-4">
                        <a href="events.php?edit_event_id=<?php echo $event['id']; ?>"
                          class="p-2 text-blue-600 hover:bg-blue-50 rounded-full transition"
                          title="Edit">
                          <i class="fas fa-edit"></i>
                        </a>
                        <form action="events.php" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this event?');">
                          <input type="hidden" name="action" value="delete_event">
                          <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                          <input type="hidden" name="storyteller_id" value="<?php echo $storyteller['id']; ?>">
                          <button type="submit"
                            class="p-2 text-red-600 hover:bg-red-50 rounded-full transition"
                            title="Delete">
                            <i class="fas fa-trash"></i>
                          </button>
                        </form>
                      </div>
                    </div>

                    <?php if (!empty($event['description'])): ?>
                      <p class="text-gray-600 mt-2">
                        <?php echo htmlspecialchars($event['description']); ?>
                      </p>
                    <?php endif; ?>

                    <!-- Event Media -->
                    <div class="mt-4 space-y-3">
                      <?php if ($event['image_url']): ?>
                        <img src="<?php echo htmlspecialchars($event['image_url']); ?>"
                          class="max-w-full h-auto rounded-lg border border-gray-200 shadow-sm">
                      <?php endif; ?>

                      <?php if ($event['video_url']): ?>
                        <video controls class="max-w-full rounded-lg border border-gray-200 shadow-sm">
                          <source src="<?php echo htmlspecialchars($event['video_url']); ?>" type="video/mp4">
                          Your browser does not support the video tag.
                        </video>
                      <?php endif; ?>
                    </div>

                    <!-- Event Tags -->
                    <div class="mt-4 flex flex-wrap gap-2">
                      <span class="px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                <?php echo $event['is_virtual'] ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'; ?>">
                        <?php echo $event['is_virtual'] ? 'Virtual Event' : 'In-Person Event'; ?>
                      </span>
                      <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        Completed on <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>


      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-gray-800 text-white py-8 mt-12">
    <div class="container mx-auto px-4">
      <div class="flex flex-col md:flex-row justify-between">
        <div class="mb-8 md:mb-0">
          <h3 class="text-xl font-bold mb-4">Oromo Storyteller Network</h3>
          <p class="text-gray-400 max-w-md">Preserving and celebrating the rich oral traditions of the Oromo people for future generations.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
          <div>
            <h4 class="font-semibold text-lg mb-3">Resources</h4>
            <ul class="space-y-2">
              <li><a href="#" class="text-gray-400 hover:text-white transition">Recording Guide</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white transition">Storytelling Tips</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white transition">Cultural Database</a></li>
            </ul>
          </div>

          <div>
            <h4 class="font-semibold text-lg mb-3">Support</h4>
            <ul class="space-y-2">
              <li><a href="#" class="text-gray-400 hover:text-white transition">Help Center</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white transition">Community</a></li>
              <li><a href="#" class="text-gray-400 hover:text-white transition">Contact Us</a></li>
            </ul>
          </div>

          <div>
            <h4 class="font-semibold text-lg mb-3">Connect</h4>
            <div class="flex space-x-4">
              <a href="#" class="text-gray-400 hover:text-white transition text-xl">
                <i class="fab fa-facebook-f"></i>
              </a>
              <a href="#" class="text-gray-400 hover:text-white transition text-xl">
                <i class="fab fa-instagram"></i>
              </a>
              <a href="#" class="text-gray-400 hover:text-white transition text-xl">
                <i class="fab fa-youtube"></i>
              </a>
              <a href="#" class="text-gray-400 hover:text-white transition text-xl">
                <i class="fab fa-twitter"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-400 text-sm">
        © <?php echo date('Y'); ?> Oromo Artisan & Storyteller Marketplace. All rights reserved.
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
      const openAddModalEmpty = document.getElementById('openAddModalEmpty');
      const closeModal = document.getElementById('closeModal');
      const cancelModal = document.getElementById('cancelModal');

      function openModal() {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
      }

      function closeModalFunc() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
        window.history.replaceState({}, document.title, window.location.pathname);
      }

      if (openAddModal) {
        openAddModal.addEventListener('click', openModal);
      }

      if (openAddModalEmpty) {
        openAddModalEmpty.addEventListener('click', openModal);
      }

      if (closeModal) {
        closeModal.addEventListener('click', closeModalFunc);
      }

      if (cancelModal) {
        cancelModal.addEventListener('click', closeModalFunc);
      }

      // Close modal when clicking outside
      modal.addEventListener('click', function(e) {
        if (e.target === modal) {
          closeModalFunc();
        }
      });

      // Image preview handler
      const imageInput = document.getElementById('eventImageInput');
      const imagePreview = document.getElementById('imagePreview');
      const previewImage = document.getElementById('previewImage');

      if (imageInput) {
        imageInput.addEventListener('change', function() {
          if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
              previewImage.src = e.target.result;
              imagePreview.classList.remove('hidden');
            }
            reader.readAsDataURL(this.files[0]);
          }
        });
      }

      // Video preview handler
      const videoInput = document.getElementById('eventVideoInput');
      const videoPreview = document.getElementById('videoPreview');
      const previewVideo = document.getElementById('previewVideo');

      if (videoInput) {
        videoInput.addEventListener('change', function() {
          if (this.files && this.files[0]) {
            const file = this.files[0];
            const videoURL = URL.createObjectURL(file);

            // Create video element to check duration
            const video = document.createElement('video');
            video.preload = 'metadata';

            video.onloadedmetadata = function() {
              window.URL.revokeObjectURL(video.src);
              previewVideo.src = videoURL;
              videoPreview.classList.remove('hidden');
            }

            video.src = videoURL;
          }
        });
      }

      // Auto-open modal for editing
      <?php if ($edit_event): ?>
        openModal();
      <?php endif; ?>
    });
  </script>
</body>

</html>