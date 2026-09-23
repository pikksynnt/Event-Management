<?php
/**
 * Event Detail Page
 */

require_once __DIR__ . '/includes/auth.php';
requireEventManager();

$pdo = getDbConnection();

$idParam = $_GET['id'] ?? null;
$eventId = filter_var($idParam, FILTER_VALIDATE_INT);

$event = null;
if ($eventId !== false && $eventId > 0) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? LIMIT 1");
    $stmt->execute([$eventId]);
    $event = $stmt->fetch();
}

// Flash messages from approve/reject actions
$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

// Date formatting helper
function formatEventDateTime(string $dateStr): string {
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    $ts = strtotime($dateStr);
    if (!$ts) return $dateStr;
    $d = date('j', $ts);
    $m = $months[(int)date('n', $ts)] ?? date('F', $ts);
    $y = date('Y', $ts);
    $time = date('H:i', $ts);
    return "{$d} {$m} {$y}, {$time} WIB";
}

$pageTitle = $event ? htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') . ' - Detail Event' : 'Event Tidak Ditemukan';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<main class="main-content">
  <div class="container">
    <?php if (!$event): ?>
      <!-- Not Found State -->
      <div style="max-width: 32rem; margin: 3rem auto;">
        <div class="card" style="padding: 3rem 2rem; text-align: center;">
          <div class="empty-state-icon" style="margin: 0 auto 1.25rem;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
          </div>
          <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-main);">Event tidak ditemukan</h2>
          <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.5rem;">
            ID event yang Anda cari tidak valid atau tidak tersedia di sistem.
          </p>
          <div style="margin-top: 1.75rem;">
            <a href="dashboard.php" class="btn btn-outline">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
              </svg>
              <span>Kembali ke Dashboard</span>
            </a>
          </div>
        </div>
      </div>
    <?php else: ?>
      <!-- Back Navigation -->
      <div style="margin-bottom: 1.5rem;">
        <a href="dashboard.php" class="btn btn-outline btn-sm">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
          <span>Kembali ke Dashboard</span>
        </a>
      </div>

      <!-- Flash Feedback Alerts -->
      <?php if (!empty($flashSuccess)): ?>
        <div class="alert alert-success">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 0.125rem;">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
          </svg>
          <div><?= htmlspecialchars($flashSuccess, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
      <?php endif; ?>

      <?php if (!empty($flashError)): ?>
        <div class="alert alert-error">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 0.125rem;">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
          </svg>
          <div><?= htmlspecialchars($flashError, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
      <?php endif; ?>

      <!-- Event Header Card -->
      <div class="card" style="margin-bottom: 2rem;">
        <div style="padding: 2rem; border-bottom: 1px solid var(--border-subtle);">
          <div style="display: flex; flex-direction: column; gap: 1.5rem;">
            <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
              <div>
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                  <span style="font-family: monospace; font-size: 0.75rem; font-weight: 700; color: var(--text-light);">
                    EVENT #<?= (int)$event['id'] ?>
                  </span>
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
                </div>
                <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-top: 0.5rem; letter-spacing: -0.025em;">
                  <?= htmlspecialchars($event['title'], ENT_QUOTES, 'UTF-8') ?>
                </h1>
              </div>

              <!-- Action Controls -->
              <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <?php if ($event['status'] === 'submitted'): ?>
                  <!-- Approve Form -->
                  <form action="approve-event.php" method="POST" style="margin: 0;" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui event ini?');">
                    <input type="hidden" name="event_id" value="<?= (int)$event['id'] ?>">
                    <button type="submit" class="btn btn-success">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                      </svg>
                      <span>Setujui Event</span>
                    </button>
                  </form>

                  <!-- Reject Trigger -->
                  <button type="button" class="btn btn-danger" id="btn-open-reject">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="12" cy="12" r="10"></circle>
                      <line x1="15" y1="9" x2="9" y2="15"></line>
                      <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                    <span>Tolak Event</span>
                  </button>
                <?php elseif ($event['status'] === 'approved'): ?>
                  <button type="button" class="btn btn-outline" disabled style="opacity: 0.75; cursor: default;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--emerald-btn);">
                      <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                      <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span>Sudah Disetujui</span>
                  </button>
                <?php elseif ($event['status'] === 'rejected'): ?>
                  <button type="button" class="btn btn-outline" disabled style="opacity: 0.75; cursor: default;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--rose-btn);">
                      <circle cx="12" cy="12" r="10"></circle>
                      <line x1="15" y1="9" x2="9" y2="15"></line>
                      <line x1="9" y1="9" x2="15" y2="15"></line>
                    </svg>
                    <span>Sudah Ditolak</span>
                  </button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>

        <!-- Rejection Notice Banner -->
        <?php if ($event['status'] === 'rejected' && !empty($event['rejection_reason'])): ?>
          <div class="rejection-banner">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--rose-btn); flex-shrink: 0; margin-top: 0.125rem;">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="8" x2="12" y2="12"></line>
              <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <div>
              <div class="rejection-banner-title">Alasan Penolakan Event:</div>
              <div class="rejection-banner-text">
                <?= nl2br(htmlspecialchars($event['rejection_reason'], ENT_QUOTES, 'UTF-8')) ?>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <!-- Key Meta Grid -->
        <div class="detail-grid-meta">
          <div class="detail-meta-item">
            <div class="detail-meta-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                <line x1="7" y1="7" x2="7.01" y2="7"></line>
              </svg>
            </div>
            <div>
              <div class="detail-meta-label">Jenis Event</div>
              <div class="detail-meta-value"><?= htmlspecialchars($event['event_type'], ENT_QUOTES, 'UTF-8') ?></div>
            </div>
          </div>

          <div class="detail-meta-item">
            <div class="detail-meta-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="16" y1="2" x2="16" y2="6"></line>
                <line x1="8" y1="2" x2="8" y2="6"></line>
                <line x1="3" y1="10" x2="21" y2="10"></line>
              </svg>
            </div>
            <div>
              <div class="detail-meta-label">Jadwal Pelaksanaan</div>
              <div class="detail-meta-value" style="font-size: 0.875rem;">
                Mulai: <?= formatEventDateTime($event['start_date']) ?><br>
                Selesai: <?= formatEventDateTime($event['end_date']) ?>
              </div>
            </div>
          </div>

          <div class="detail-meta-item">
            <div class="detail-meta-icon">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
            </div>
            <div>
              <div class="detail-meta-label">Estimasi Jumlah Tamu</div>
              <div class="detail-meta-value">
                <?= number_format((int)$event['estimated_guests'], 0, ',', '.') ?> Orang
              </div>
            </div>
          </div>
        </div>

        <!-- Event Description Section -->
        <div style="padding: 2rem;">
          <h3 style="font-size: 1rem; font-weight: 700; color: var(--text-main); margin-bottom: 0.75rem;">
            Deskripsi Event
          </h3>
          <p style="font-size: 0.9375rem; color: #334155; line-height: 1.7; white-space: pre-line;">
            <?= htmlspecialchars($event['description'], ENT_QUOTES, 'UTF-8') ?>
          </p>
        </div>
      </div>

      <!-- Reject Modal Dialog -->
      <div id="reject-modal" class="modal-backdrop">
        <div class="modal-dialog">
          <div style="display: flex; align-items: flex-start; gap: 0.875rem; margin-bottom: 1.25rem;">
            <div style="padding: 0.5rem; background-color: var(--rose-bg); border-radius: var(--radius-md); color: var(--rose-btn);">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
              </svg>
            </div>
            <div>
              <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--text-main);">Konfirmasi Penolakan Event</h3>
              <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.125rem;">
                Tindakan ini memerlukan alasan penolakan yang jelas.
              </p>
            </div>
          </div>

          <form id="reject-form" action="reject-event.php" method="POST">
            <input type="hidden" name="event_id" value="<?= (int)$event['id'] ?>">

            <div class="form-group">
              <label for="rejection_reason" class="form-label">
                Alasan Penolakan <span style="color: var(--rose-btn);">*</span>
              </label>
              <textarea 
                id="rejection_reason" 
                name="rejection_reason" 
                class="form-control" 
                rows="4" 
                required 
                placeholder="Tuliskan alasan penolakan event secara spesifik (misal: kapasitas venue tidak memadai, jadwal bertabrakan, dsb)..."
              ></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
              <button type="button" class="btn btn-outline" id="btn-close-reject">
                Batal
              </button>
              <button type="submit" class="btn btn-danger">
                Konfirmasi Tolak Event
              </button>
            </div>
          </form>
        </div>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
