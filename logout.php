<?php
/**
 * Logout Endpoint
 * Clears session and redirects to login.php
 */

require_once __DIR__ . '/includes/auth.php';

logoutUser();

header('Location: login.php');
exit;
