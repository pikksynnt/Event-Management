<?php
/**
 * Shared Navbar Partial for Authenticated Pages
 */
$currentUser = getCurrentUser();
?>
<header class="navbar">
  <div class="container navbar-inner">
    <a href="dashboard.php" class="navbar-brand">
      <div class="navbar-logo-box">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
          <line x1="16" y1="2" x2="16" y2="6"></line>
          <line x1="8" y1="2" x2="8" y2="6"></line>
          <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
      </div>
      <div>
        <div class="navbar-brand-title">Sistem Event Management</div>
        <div class="navbar-brand-subtitle">Portal Khusus Event Manager</div>
      </div>
    </a>

    <div class="navbar-user-section">
      <div class="navbar-user-info">
        <span class="navbar-user-name"><?= htmlspecialchars($currentUser['name'] ?? 'Event Manager', ENT_QUOTES, 'UTF-8') ?></span>
        <span class="badge-role">Event Manager</span>
      </div>

      <form action="logout.php" method="POST" style="margin: 0;">
        <button type="submit" class="btn btn-outline btn-sm" title="Keluar dari sistem">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
            <polyline points="16 17 21 12 16 7"></polyline>
            <line x1="21" y1="12" x2="9" y2="12"></line>
          </svg>
          <span>Keluar</span>
        </button>
      </form>
    </div>
  </div>
</header>
