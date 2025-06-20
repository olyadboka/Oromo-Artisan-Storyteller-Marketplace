<!-- Footer -->
<footer class="bg-gradient-to-r from-green-900 via-yellow-900 to-red-900 text-white py-10 shadow-2xl"></footer>
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
          <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition">Contact Artisan</a></li>
          <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition">Shipping Info</a></li>
          <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition">Returns</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-semibold mb-4 text-yellow-300">About</h4>
        <ul class="space-y-2">
          <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition">Our Mission</a></li>
          <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition">Cultural Preservation</a></li>
          <li><a href="#" class="text-gray-300 hover:text-yellow-400 transition">Fair Trade Promise</a></li>
        </ul>
      </div>
      <div>
        <h4 class="font-semibold mb-4 text-yellow-300">Newsletter</h4>
        <form class="flex flex-col space-y-2">
          <input type="email" placeholder="Your email"
            class="px-3 py-2 rounded bg-gray-800 text-white focus:outline-none focus:ring-2 focus:ring-yellow-400" />
          <button type="submit"
            class="bg-yellow-400 text-gray-900 font-semibold rounded px-3 py-2 hover:bg-yellow-500 transition">Subscribe</button>
        </form>
      </div>
    </div>
  </div>
  <div class="border-t border-gray-700 mt-10 pt-6 text-center text-gray-400 text-sm">
    &copy; <?= date('Y') ?> Vist Oromo Artisan & Storyteller Marketplace. All rights reserved.
  </div>
</div>
</footer>