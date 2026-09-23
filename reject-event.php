<?php
/**
 * Action: Reject Event
 * Role Check: event_manager
 * Method: POST only
 */

require_once __DIR__ . '/includes/auth.php';
requireEventManager();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Metode tidak diizinkan.');
}

$idParam = $_POST['event_id'] ?? null;
$eventId = filter_var($idParam, FILTER_VALIDATE_INT);
$rejectionReason = trim($_POST['rejection_reason'] ?? '');

if (!$eventId || $eventId <= 0) {
    $_SESSION['flash_error'] = 'ID event tidak valid.';
    header('Location: dashboard.php');
    exit;
}

if ($rejectionReason === '') {
    $_SESSION['flash_error'] = 'Alasan penolakan wajib diisi.';
    header("Location: event-detail.php?id={$eventId}");
    exit;
}

$pdo = getDbConnection();

// Check if event exists
$stmt = $pdo->prepare("SELECT id, status FROM events WHERE id = ? LIMIT 1");
$stmt->execute([$eventId]);
$event = $stmt->fetch();

if (!$event) {
    $_SESSION['flash_error'] = 'Event tidak ditemukan.';
    header('Location: dashboard.php');
    exit;
}

// Update status and rejection reason
$updateStmt = $pdo->prepare("UPDATE events SET status = 'rejected', rejection_reason = ?, updated_at = NOW() WHERE id = ?");
$updateStmt->execute([$rejectionReason, $eventId]);

$_SESSION['flash_success'] = 'Event telah ditolak beserta alasan penolakan yang disimpan.';
header("Location: event-detail.php?id={$eventId}");
exit;
