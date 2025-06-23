<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
<!-- Tailwind CSS CDN -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
  footer{
    margin-top:5px;
  }
  .footer-gradient { background: black }
  .footer-shadow { box-shadow: 0 8px 32px 0 rgba(0,0,0,0.25); }
  .footer-link:hover { color: #facc15 !important; }
  .footer-newsletter input:focus { outline: 2px solid #facc15; }
</style>
<!-- Footer -->
<footer class="footer-gradient text-white py-10 footer-shadow">
  <div class="container mx-auto px-4">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
      <div class="mb-8 md:mb-0 max-w-xs">
        <h3 class="text-2xl font-extrabold mb-3 tracking-wide">Vist Oromo Artisan & Storyteller Marketplace</h3>
        <p class="text-gray-300 mb-4">Preserving Oromia's beautiful cultural heritage through fair trade</p>
        <div class="flex space-x-4 mt-2">
          <a href="#" class="text-yellow-400 hover:text-white transition"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="text-yellow-400 hover:text-white transition"><i class="fab fa-instagram"></i></a>
          <a href="#" class="text-yellow-400 hover:text-white transition"><i class="fab fa-twitter"></i></a>
        </div>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-3 gap-10 w-full md:w-auto">
        <div>
          <h4 class="font-semibold mb-4 text-yellow-300">Support</h4>
          <ul class="space-y-2">
            <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition footer-link">Contact Artisan</a></li>
            <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition footer-link">Shipping Info</a></li>
            <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition footer-link">Returns</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold mb-4 text-yellow-300">About</h4>
          <ul class="space-y-2">
            <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition footer-link">Our Mission</a></li>
            <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition footer-link">Cultural Preservation</a></li>
            <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition footer-link">Fair Trade Promise</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold mb-4 text-yellow-300">Newsletter</h4>
          <form class="flex flex-col space-y-2 footer-newsletter">
            <input type="email" placeholder="Your email"
              class="px-3 py-2 rounded bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400" />
            <button type="submit"
              class="bg-yellow-400 text-gray-900 font-semibold rounded px-3 py-2 hover:bg-yellow-500 transition">Subscribe</button>
          </form>
        </div>
      </div>
    </div>
    <div class="border-t border-gray-700 mt-10 pt-6 text-center text-gray-400 text-sm">
      &copy; <?php echo date('Y'); ?> Vist Oromo Artisan & Storyteller Marketplace. All rights reserved.
    </div>
  </div>
</footer>   

<script>
  document.addEventListener('DOMContentLoaded', function() {
  const newsletterForm = document.querySelector('.footer-newsletter');
  
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      const emailInput = newsletterForm.querySelector('input[type="email"]');
      const email = emailInput.value.trim();
      
      // Simple email validation
      if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        alert('Please enter a valid email address');
        return;
      }
      
      // Show thank you popup
      showThankYouPopup();
      
      // Clear the input
      emailInput.value = '';
    });
  }
  
  function showThankYouPopup() {
    // Create popup overlay
    const overlay = document.createElement('div');
    overlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    
    // Create popup content
    const popup = document.createElement('div');
    popup.className = 'bg-gray-800 rounded-lg p-6 max-w-md w-full mx-4';
    
    popup.innerHTML = `
      <div class="text-center">
        <h3 class="text-xl font-semibold text-yellow-300 mb-3">Thank You for Subscribing!</h3>
        <p class="text-gray-300 mb-5">We appreciate your interest in our newsletter.</p>
        
        <p class="text-gray-400 mb-3">Share this website with others:</p>
        <button id="copyUrlBtn" class="bg-yellow-400 text-gray-900 font-semibold rounded px-4 py-2 hover:bg-yellow-500 transition flex items-center justify-center mx-auto">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
          </svg>
          Copy Link
        </button>
        
        <button id="closePopupBtn" class="mt-4 text-gray-400 hover:text-white transition">
          Close
        </button>
      </div>
    `;
    
    overlay.appendChild(popup);
    document.body.appendChild(overlay);
    
    // Add copy functionality
    const copyBtn = overlay.querySelector('#copyUrlBtn');
    copyBtn.addEventListener('click', function() {
      navigator.clipboard.writeText(window.location.href).then(() => {
        copyBtn.innerHTML = `
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Copied!
        `;
        setTimeout(() => {
          copyBtn.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
            </svg>
            Copy Link
          `;
        }, 2000);
      });
    });
    
    // Add close functionality
    const closeBtn = overlay.querySelector('#closePopupBtn');
    closeBtn.addEventListener('click', function() {
      document.body.removeChild(overlay);
    });
    
    // Close when clicking outside
    overlay.addEventListener('click', function(e) {
      if (e.target === overlay) {
        document.body.removeChild(overlay);
      }
    });
  }
});
  </script>