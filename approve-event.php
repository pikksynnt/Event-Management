<?php
/**
 * Action: Approve Event
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

if (!$eventId || $eventId <= 0) {
    $_SESSION['flash_error'] = 'ID event tidak valid.';
    header('Location: dashboard.php');
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

if ($event['status'] === 'approved') {
    $_SESSION['flash_success'] = 'Event ini sudah berstatus disetujui.';
    header("Location: event-detail.php?id={$eventId}");
    exit;
}

// Update status to approved
$updateStmt = $pdo->prepare("UPDATE events SET status = 'approved', updated_at = NOW() WHERE id = ?");
$updateStmt->execute([$eventId]);

$_SESSION['flash_success'] = 'Event berhasil disetujui.';
header("Location: event-detail.php?id={$eventId}");
exit;
