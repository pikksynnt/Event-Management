<?php
/**
 * Login Page for Event Manager
 */

require_once __DIR__ . '/includes/auth.php';

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $result = authenticateUser($email, $password);

    if ($result['success']) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = $result['error'];
    }
}

$pageTitle = 'Masuk - Sistem Event Management';
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-wrapper">
  <div class="auth-card-box">
    <div class="auth-header">
      <div class="auth-header-icon">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
          <line x1="16" y1="2" x2="16" y2="6"></line>
          <line x1="8" y1="2" x2="8" y2="6"></line>
          <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
      </div>
      <h1 class="auth-header-title">Sistem Event Management</h1>
      <p class="auth-header-subtitle">Portal Masuk Khusus Event Manager</p>
    </div>

    <div class="card" style="padding: 2rem;">
      <?php if (!empty($error)): ?>
        <div class="alert alert-error">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 0.125rem;">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <div><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
      <?php endif; ?>

      <form action="login.php" method="POST">
        <div class="form-group">
          <label for="email" class="form-label">Email</label>
          <div class="input-icon-group">
            <span class="input-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
              </svg>
            </span>
            <input 
              type="email" 
              id="email" 
              name="email" 
              class="form-control form-control-icon" 
              placeholder="email@domain.com" 
              required 
              value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
              autocomplete="email"
            >
          </div>
        </div>

        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <div class="input-icon-group">
            <span class="input-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"></rect>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
              </svg>
            </span>
            <input 
              type="password" 
              id="password" 
              name="password" 
              class="form-control form-control-icon" 
              placeholder="••••••••" 
              required 
              autocomplete="current-password"
            >
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-w-full" style="margin-top: 0.5rem;">
          Masuk ke Dashboard
        </button>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
