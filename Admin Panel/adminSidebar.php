<div class="d-flex flex-column flex-shrink-0 p-3 bg-light" style="width: 230px; min-height:100vh;">
  <a href="adminDashboard.php" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-dark text-decoration-none">
    <span class="fs-4"><i class="fa fa-cogs me-2"></i>Admin Panel</span>
  </a>
  <hr>
  <div class="d-md-block d-none">
    <ul class="nav nav-pills flex-column mb-auto">
      <li class="nav-item"><a href="adminDashboard.php" class="nav-link">Analytics Dashboard</a></li>
      <li><a href="adminUsers.php" class="nav-link">User Verification & Moderation</a></li>
      <li><a href="adminContent.php" class="nav-link">Content Monitoring</a></li>
      <li><a href="adminCommission.php" class="nav-link">Commission Settings</a></li>
      <li><a href="adminCuration.php" class="nav-link">Homepage Curation</a></li>
    </ul>
  </div>
  <!-- Mobile accordion -->
  <div class="accordion d-md-none" id="adminSidebarAccordion">
    <div class="accordion-item">
      <h2 class="accordion-header" id="headingOne">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
          Admin Navigation
        </button>
      </h2>
      <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#adminSidebarAccordion">
        <div class="accordion-body p-0">
          <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item"><a href="adminDashboard.php" class="nav-link">Analytics Dashboard</a></li>
            <li><a href="adminUsers.php" class="nav-link">User Verification & Moderation</a></li>
            <li><a href="adminContent.php" class="nav-link">Content Monitoring</a></li>
            <li><a href="adminCommission.php" class="nav-link">Commission Settings</a></li>
            <li><a href="adminCuration.php" class="nav-link">Homepage Curation</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>
<style>
  @media (max-width: 900px) { .d-flex.flex-column { position:static !important; width:100% !important; } }
</style>
