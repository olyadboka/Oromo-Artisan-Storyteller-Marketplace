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
  <!-- Dashboard Header -->
  <header class="storyteller-header text-white">
    <div class="container mx-auto px-4 py-8">
      <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
        <div class="flex items-center space-x-6 mb-6 md:mb-0">
          <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQcz5kv79l6XgmpU4Z3n3_OwCm-kf8M01D0qg&s"
            alt="Storyteller"
            class="w-20 h-20 rounded-full border-4 border-white border-opacity-30 object-cover shadow-lg">
          <div>
            <h1 class="text-3xl font-bold">Jirenya Dhugaa</h1>
            <p class="text-white text-opacity-80 flex items-center">
              <i class="fas fa-map-marker-alt mr-2"></i> Bale, Oromia
              <span class="ml-4 px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm">
                <i class="fas fa-certificate mr-1"></i> Certified Storykeeper
              </span>
            </p>
            <div class="flex mt-2 space-x-2">
              <span class="px-2 py-1 bg-white bg-opacity-10 rounded text-xs">Folklore</span>
              <span class="px-2 py-1 bg-white bg-opacity-10 rounded text-xs">History</span>
              <span class="px-2 py-1 bg-white bg-opacity-10 rounded text-xs">Music</span>
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

  <!-- Dashboard Navigation -->
  <nav class="bg-white shadow-sm sticky top-0 z-10">
    <div class="container mx-auto px-4">
      <div class="flex overflow-x-auto">
        <a href="#" class="px-6 py-4 font-medium text-blue-800 border-b-2 border-blue-800">
          <i class="fas fa-home mr-2"></i> Dashboard
        </a>
        <a href="" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-book-open mr-2"></i> My Stories
        </a>
        <a href="#" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-calendar-alt mr-2"></i> Events
        </a>
        <a href="#" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
          <i class="fas fa-comments mr-2"></i> Community
        </a>
        <a href="#" class="px-6 py-4 font-medium text-gray-600 hover:text-blue-800">
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
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500">Total Stories</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1">47</h3>
          </div>
          <div class="p-3 bg-blue-100 rounded-lg text-blue-800">
            <i class="fas fa-book-open text-2xl"></i>
          </div>
        </div>
        <a href="#" class="mt-4 inline-block text-blue-600 hover:text-blue-800 text-sm font-medium">
          View all stories <i class="fas fa-arrow-right ml-1"></i>
        </a>
      </div>

      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500">Monthly Listeners</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1">1,248</h3>
          </div>
          <div class="p-3 bg-red-100 rounded-lg text-red-800">
            <i class="fas fa-headphones text-2xl"></i>
          </div>
        </div>
        <a href="#" class="mt-4 inline-block text-blue-600 hover:text-blue-800 text-sm font-medium">
          See analytics <i class="fas fa-arrow-right ml-1"></i>
        </a>
      </div>

      <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-gray-500">Earnings (Last 30d)</p>
            <h3 class="text-3xl font-bold text-gray-800 mt-1">ETB 8,950</h3>
          </div>
          <div class="p-3 bg-green-100 rounded-lg text-green-800">
            <i class="fas fa-coins text-2xl"></i>
          </div>
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
        <a href="#"
          class="border-2 border-dashed border-gray-200 hover:border-blue-300 rounded-lg p-5 text-center transition-colors">
          <div class="text-blue-600 mb-3"><i class="fas fa-microphone text-3xl"></i></div>
          <h3 class="font-medium text-gray-800">Record New Story</h3>
          <p class="text-sm text-gray-500 mt-1">Audio or video</p>
        </a>

        <a href="#"
          class="border-2 border-dashed border-gray-200 hover:border-blue-300 rounded-lg p-5 text-center transition-colors">
          <div class="text-red-600 mb-3"><i class="fas fa-calendar-plus text-3xl"></i></div>
          <h3 class="font-medium text-gray-800">Schedule Performance</h3>
          <p class="text-sm text-gray-500 mt-1">Live or virtual</p>
        </a>

        <a href="#"
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
            <!-- Story 1 -->
            <div class="story-card p-6 hover:bg-gray-50 transition">
              <div class="flex items-start">
                <div class="media-icon audio-icon mr-4">
                  <i class="fas fa-music"></i>
                </div>
                <div class="flex-1">
                  <h3 class="font-bold text-lg text-gray-800">The Wise Goat and the Hyena</h3>
                  <p class="text-gray-600 mt-1">Traditional Oromo fable about wisdom overcoming brute force</p>
                  <div class="flex items-center mt-3 text-sm text-gray-500">
                    <span class="mr-4"><i class="fas fa-tag mr-1"></i> Folklore</span>
                    <span class="mr-4"><i class="fas fa-language mr-1"></i> Afaan Oromo</span>
                    <span><i class="fas fa-clock mr-1"></i> 12 min</span>
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
                  <span class="text-sm text-gray-500">128 listens</span>
                </div>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View details →</a>
              </div>
            </div>

            <!-- Story 2 -->
            <div class="story-card p-6 hover:bg-gray-50 transition">
              <div class="flex items-start">
                <div class="media-icon video-icon mr-4">
                  <i class="fas fa-video"></i>
                </div>
                <div class="flex-1">
                  <h3 class="font-bold text-lg text-gray-800">The Gadaa System Explained</h3>
                  <p class="text-gray-600 mt-1">Documentary-style explanation of Oromo democratic tradition</p>
                  <div class="flex items-center mt-3 text-sm text-gray-500">
                    <span class="mr-4"><i class="fas fa-tag mr-1"></i> History</span>
                    <span class="mr-4"><i class="fas fa-language mr-1"></i> English</span>
                    <span><i class="fas fa-clock mr-1"></i> 22 min</span>
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
                    <i class="fas fa-star"></i>
                  </div>
                  <span class="text-sm text-gray-500">342 views</span>
                </div>
                <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View details →</a>
              </div>
            </div>
          </div>

          <div class="px-6 py-4 border-t border-gray-200 text-center">
            <a href="#" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
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
              <!-- Event 1 -->
              <div class="flex items-start">
                <div class="bg-blue-100 text-blue-800 rounded-lg p-3 text-center mr-4">
                  <div class="font-bold">15</div>
                  <div class="text-xs uppercase">Jun</div>
                </div>
                <div>
                  <h3 class="font-medium text-gray-800">Irreecha Festival</h3>
                  <p class="text-sm text-gray-600 mt-1">Traditional storytelling at Finfinne Cultural Center</p>
                  <div class="mt-2 flex items-center text-sm text-gray-500">
                    <i class="fas fa-clock mr-1"></i> 3:00 PM - 5:00 PM
                  </div>
                </div>
              </div>

              <!-- Event 2 -->
              <div class="flex items-start">
                <div class="bg-red-100 text-red-800 rounded-lg p-3 text-center mr-4">
                  <div class="font-bold">22</div>
                  <div class="text-xs uppercase">Jun</div>
                </div>
                <div>
                  <h3 class="font-medium text-gray-800">Virtual Story Circle</h3>
                  <p class="text-sm text-gray-600 mt-1">Online session for diaspora community</p>
                  <div class="mt-2 flex items-center text-sm text-gray-500">
                    <i class="fas fa-clock mr-1"></i> 8:00 PM - 9:30 PM
                  </div>
                </div>
              </div>
            </div>

            <button class="mt-6 w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-medium">
              <i class="fas fa-plus mr-2"></i> Schedule New Performance
            </button>
          </div>
        </div>

        <!-- Quick Translation Tool -->
        <div class="bg-white rounded-xl shadow p-6">
          <h2 class="text-xl font-bold text-gray-800 mb-4">Translate Story</h2>
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Select Story</label>
              <select
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                <option>The Wise Goat and the Hyena</option>
                <option>The Gadaa System Explained</option>
                <option>Song of the Oromo Warriors</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Translate to</label>
              <select
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
                <option>English</option>
                <option>Amharic</option>
                <option>Arabic</option>
                <option>French</option>
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
                <h3 class="font-medium">5 New Story Requests</h3>
                <p class="text-sm text-gray-600">From listeners worldwide</p>
              </div>
            </div>

            <div class="flex items-center p-3 bg-green-50 rounded-lg">
              <div class="bg-green-100 text-green-800 p-2 rounded-full mr-3">
                <i class="fas fa-question-circle"></i>
              </div>
              <div>
                <h3 class="font-medium">12 Questions</h3>
                <p class="text-sm text-gray-600">About your stories</p>
              </div>
            </div>

            <a href="#" class="block text-center text-blue-600 hover:text-blue-800 font-medium mt-4">
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
        &copy; 2023 Oromo Artisan & Storyteller Marketplace. All rights reserved.
      </div>
    </div>
  </footer>

  <script>
  // Interactive elements
  document.addEventListener('DOMContentLoaded', function() {
    // Story card hover actions
    const storyCards = document.querySelectorAll('.story-card');
    storyCards.forEach(card => {
      card.addEventListener('mouseenter', function() {
        this.querySelector('.story-actions').style.opacity = '1';
      });
      card.addEventListener('mouseleave', function() {
        this.querySelector('.story-actions').style.opacity = '0';
      });
    });

    // Performance time formatting
    const timeElements = document.querySelectorAll('[data-time]');
    timeElements.forEach(el => {
      const time = el.getAttribute('data-time');
      el.textContent = new Date(time).toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit'
      });
    });
  });
  </script>
</body>

</html>