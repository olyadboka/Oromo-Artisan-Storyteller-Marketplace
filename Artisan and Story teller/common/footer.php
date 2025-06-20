<!-- Footer -->
<footer class="bg-gray-800 text-white py-8 mt-12">
  <div class="container mx-auto px-4">
    <div class="flex flex-col md:flex-row justify-between">
      <div class="mb-6 md:mb-0">
        <h3 class="text-xl font-bold mb-4">Oromo Artisan Dashboard</h3>
        <p class="text-gray-400">Empowering artisans through technology</p>
      </div>
      <div class="grid grid-cols-2 md:grid-cols-3 gap-8">
        <div>
          <h4 class="font-semibold mb-3">Resources</h4>
          <ul class="space-y-2">
            <li><a href="#" class="text-gray-400 hover:text-white">Help Center</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white">Artisan Guides</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white">Pricing</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold mb-3">Legal</h4>
          <ul class="space-y-2">
            <li><a href="#" class="text-gray-400 hover:text-white">Terms</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white">Privacy</a></li>
            <li><a href="#" class="text-gray-400 hover:text-white">Cookies</a></li>
          </ul>
        </div>
        <div>
          <h4 class="font-semibold mb-3">Contact</h4>
          <div class="flex space-x-4">
            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-instagram"></i></a>
            <a href="#" class="text-gray-400 hover:text-white"><i class="fab fa-twitter"></i></a>
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
// Simple interactivity
document.addEventListener('DOMContentLoaded', function() {
  // Language toggle functionality
  const langButtons = document.querySelectorAll('.language-toggle button');
  if (langButtons) {
    langButtons.forEach(button => {
      button.addEventListener('click', function() {
        langButtons.forEach(btn => {
          btn.classList.remove('bg-red-600', 'text-white');
          btn.classList.add('bg-white', 'text-gray-700');
        });
        this.classList.add('bg-red-600', 'text-white');
        this.classList.remove('bg-white', 'text-gray-700');
      });
    });
  }

  // Product card hover actions
  const productCards = document.querySelectorAll('.product-card');
  productCards.forEach(card => {
    card.addEventListener('mouseenter', function() {
      this.querySelector('.product-actions').style.opacity = '1';
    });
    card.addEventListener('mouseleave', function() {
      this.querySelector('.product-actions').style.opacity = '0';
    });
  });
});
</script>
</body>

</html>