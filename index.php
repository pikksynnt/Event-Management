<?php
/**
 * Root Entry Point
 * Redirects to dashboard if logged in, otherwise to login.php
 */

require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;
