<?php
/**
 * Authentication and Session Management Helper
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Initializes a secure session if not already active
 */
function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isSecure,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        session_start();
    }
}

/**
 * Check if current session belongs to an authenticated Event Manager
 */
function isLoggedIn(): bool {
    startSession();
    return isset($_SESSION['user']) && 
           isset($_SESSION['user']['role']) && 
           $_SESSION['user']['role'] === 'event_manager';
}

/**
 * Enforce Event Manager access on protected pages and actions
 */
function requireEventManager(): void {
    startSession();
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Get currently logged-in user array or null
 */
function getCurrentUser(): ?array {
    startSession();
    return $_SESSION['user'] ?? null;
}

/**
 * Authenticates user credentials against the database
 */
function authenticateUser(string $email, string $password): array {
    startSession();
    $email = trim($email);

    if (empty($email) || empty($password)) {
        return ['success' => false, 'error' => 'Email dan password wajib diisi.'];
    }

    $pdo = getDbConnection();
    $stmt = $pdo->prepare("SELECT id, name, email, password_hash, role FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        return ['success' => false, 'error' => 'Email atau password salah. Silakan coba kembali.'];
    }

    if ($user['role'] !== 'event_manager') {
        return ['success' => false, 'error' => 'Akses ditolak. Akun Anda bukan Event Manager.'];
    }

    // Regenerate session ID to prevent session fixation attacks
    session_regenerate_id(true);

    $_SESSION['user'] = [
        'id'    => (int)$user['id'],
        'name'  => $user['name'],
        'email' => $user['email'],
        'role'  => $user['role']
    ];

    return ['success' => true];
}

/**
 * Clears session and logs user out completely
 */
function logoutUser(): void {
    startSession();
    $_SESSION = [];

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    session_destroy();
}
