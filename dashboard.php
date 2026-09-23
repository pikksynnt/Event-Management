<?php
/**
 * Event Manager Dashboard
 */

require_once __DIR__ . '/includes/auth.php';
requireEventManager();

$pdo = getDbConnection();

// Dynamic statistics from MySQL
$statStmt = $pdo->query("
    SELECT 
        COUNT(*) AS total_count,
        SUM(CASE WHEN status = 'submitted' THEN 1 ELSE 0 END) AS submitted_count,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved_count,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected_count
    FROM events
");
$stats = $statStmt->fetch();

$totalCount     = (int)($stats['total_count'] ?? 0);
$submittedCount = (int)($stats['submitted_count'] ?? 0);
$approvedCount  = (int)($stats['approved_count'] ?? 0);
$rejectedCount  = (int)($stats['rejected_count'] ?? 0);

// Filter logic
$filter = $_GET['filter'] ?? 'all';
$validFilters = ['submitted', 'approved', 'rejected'];

if (in_array($filter, $validFilters, true)) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE status = ? ORDER BY created_at DESC");
    $stmt->execute([$filter]);
} else {
    $filter = 'all';
    $stmt = $pdo->query("SELECT * FROM events ORDER BY created_at DESC");
}

$events = $stmt->fetchAll();

// Date formatting helper
function formatEventDate(string $dateStr): string {
    $months = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
        7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];
    $ts = strtotime($dateStr);
    if (!$ts) return $dateStr;
    $d = date('j', $ts);
    $m = $months[(int)date('n', $ts)] ?? date('M', $ts);
    $y = date('Y', $ts);
    return "{$d} {$m} {$y}";
}

$pageTitle = 'Dashboard Event Manager - Sistem Event Management';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="main-content">
  <div class="container">
    <!-- Header -->
    <div style="margin-bottom: 2rem;">
      <h1 style="font-size: 1.75rem; font-weight: 800; letter-spacing: -0.025em; color: var(--text-main);">
        Dashboard Event Manager
      </h1>
      <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.25rem;">
        Tinjau, evaluasi, dan kelola seluruh pengajuan event dengan terorganisir.
      </p>
    </div>

    <!-- Stat Cards -->
    <div class="stats-grid">
      <!-- Total Event -->
      <div class="stat-card">
        <div class="stat-card-header">
          <span class="stat-card-title">Total Event</span>
          <div class="stat-icon-wrapper stat-icon-default">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
          </div>
        </div>
        <div class="stat-card-value"><?= $totalCount ?></div>
        <div class="stat-card-desc">Seluruh pengajuan event tercatat</div>
      </div>

      <!-- Menunggu Review -->
      <div class="stat-card">
        <div class="stat-card-header">
          <span class="stat-card-title">Menunggu Review</span>
          <div class="stat-icon-wrapper stat-icon-amber">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <polyline points="12 6 12 12 16 14"></polyline>
            </svg>
          </div>
        </div>
        <div class="stat-card-value"><?= $submittedCount ?></div>
        <div class="stat-card-desc">Perlu segera ditinjau manager</div>
      </div>

      <!-- Event Disetujui -->
      <div class="stat-card">
        <div class="stat-card-header">
          <span class="stat-card-title">Event Disetujui</span>
          <div class="stat-icon-wrapper stat-icon-emerald">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
          </div>
        </div>
        <div class="stat-card-value"><?= $approvedCount ?></div>
        <div class="stat-card-desc">Telah disetujui & siap jalan</div>
      </div>

      <!-- Event Ditolak -->
      <div class="stat-card">
        <div class="stat-card-header">
          <span class="stat-card-title">Event Ditolak</span>
          <div class="stat-icon-wrapper stat-icon-rose">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="15" y1="9" x2="9" y2="15"></line>
              <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
          </div>
        </div>
        <div class="stat-card-value"><?= $rejectedCount ?></div>
        <div class="stat-card-desc">Pengajuan ditolak dengan alasan</div>
      </div>
    </div>

    <!-- Event List Card -->
    <div class="card">
      <div class="card-header">
        <div>
          <h2 class="card-title">Daftar Event</h2>
          <p class="card-subtitle">
            Menampilkan <?= count($events) ?> event berdasarkan status yang dipilih.
          </p>
        </div>

        <!-- Filter Tabs -->
        <div class="filter-tabs">
          <span class="filter-label">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
            </svg>
            Filter:
          </span>
          <a href="dashboard.php?filter=all" class="filter-tab <?= $filter === 'all' ? 'active' : '' ?>">
            Semua
            <span class="filter-tab-count"><?= $totalCount ?></span>
          </a>
          <a href="dashboard.php?filter=submitted" class="filter-tab <?= $filter === 'submitted' ? 'active' : '' ?>">
            Menunggu Review
            <span class="filter-tab-count"><?= $submittedCount ?></span>
          </a>
          <a href="dashboard.php?filter=approved" class="filter-tab <?= $filter === 'approved' ? 'active' : '' ?>">
            Disetujui
            <span class="filter-tab-count"><?= $approvedCount ?></span>
          </a>
          <a href="dashboard.php?filter=rejected" class="filter-tab <?= $filter === 'rejected' ? 'active' : '' ?>">
            Ditolak
            <span class="filter-tab-count"><?= $rejectedCount ?></span>
          </a>
        </div>
      </div>

      <!-- Table or Empty State -->
      <?php if (empty($events)): ?>
        <div class="empty-state">
          <div class="empty-state-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="16" y1="2" x2="16" y2="6"></line>
              <line x1="8" y1="2" x2="8" y2="6"></line>
              <line x1="3" y1="10" x2="21" y2="10"></line>
            </svg>
          </div>
          <h3 class="empty-state-title">Tidak ada event pada kategori ini</h3>
          <p class="empty-state-desc">Tidak ada data event dengan status yang Anda pilih saat ini.</p>
        </div>
      <?php else: ?>
        <div class="table-responsive">
          <table class="data-table">
            <thead>
              <tr>
                <th>Judul Event</th>
                <th>Jenis Event</th>
                <th>Tanggal Pelaksanaan</th>
                <th>Estimasi Tamu</th>
                <th>Status</th>
                <th style="text-align: right;">Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($events as $event): ?>
                <tr>
                  <td>
                    <div class="table-event-title">
                      <?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="table-event-desc">
                      <?= htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                  </td>
                  <td>
                    <span class="tag-badge">
                      <?= htmlspecialchars($event['event_type'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                  </td>
                  <td style="color: var(--text-muted); white-space: nowrap;">
                    <?= formatEventDate($event['start_date']) ?> - <?= formatEventDate($event['end_date']) ?>
                  </td>
                  <td style="color: var(--text-muted); white-space: nowrap;">
                    <div style="display: inline-flex; align-items: center; gap: 0.375rem;">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--text-light);">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                      </svg>
                      <span><?= number_format((int)$event['estimated_guests'], 0, ',', '.') ?> orang</span>
                    </div>
                  </td>
                  <td>
                    <?php if ($event['status'] === 'submitted'): ?>
                      <span class="badge-status badge-submitted">
                        <span class="badge-dot"></span> Menunggu Review
                      </span>
                    <?php elseif ($event['status'] === 'approved'): ?>
                      <span class="badge-status badge-approved">
                        <span class="badge-dot"></span> Disetujui
                      </span>
                    <?php elseif ($event['status'] === 'rejected'): ?>
                      <span class="badge-status badge-rejected">
                        <span class="badge-dot"></span> Ditolak
                      </span>
                    <?php endif; ?>
                  </td>
                  <td style="text-align: right; white-space: nowrap;">
                    <a href="event-detail.php?id=<?= (int)$event['id'] ?>" class="btn btn-outline btn-sm">
                      <span>Detail</span>
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                      </svg>
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
