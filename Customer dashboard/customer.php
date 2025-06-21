<?php include '../common/header.php'; ?>
<main style="background:linear-gradient(120deg,#f8f8f8 60%,#e0c3a3 100%);min-height:100vh;" class="py-10">
  <div class="bg-white rounded-2xl shadow-lg p-8 max-w-2xl mx-auto border border-yellow-200">
    <div class="flex flex-col md:flex-row items-center md:items-start gap-6 mb-8">
      <img src="../assets/profile-default.png" alt="Profile" class="w-20 h-20 rounded-full border-4 border-yellow-300 shadow-md bg-gray-100 object-cover">
      <div class="flex-1 text-center md:text-left">
        <h1 class="text-3xl font-extrabold text-yellow-900 mb-1 tracking-tight">Your Profile</h1>
        <p class="text-gray-600 text-base">Manage your profile, view your orders, and update your information.</p>
      </div>
    </div>
    <!-- Add customer profile details and actions here -->
    <div class="mt-10 text-center">
      <a href="index.php" class="inline-block bg-yellow-400 text-yellow-900 font-bold px-6 py-2 rounded-full shadow hover:bg-yellow-500 transition">Back to Dashboard</a>
    </div>
  </div>
</main>
<?php include '../common/footer.php'; ?>
