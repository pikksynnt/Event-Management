<?php
/**
 * Database Configuration
 * Target: MySQL on Localhost (XAMPP) & Hostinger Shared Hosting
 */

// Production or Environment Variable overrides
$dbHost = getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'localhost';
$dbPort = getenv('DB_PORT') !== false ? getenv('DB_PORT') : '3306';
$dbName = getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'event_management';
$dbUser = getenv('DB_USER') !== false ? getenv('DB_USER') : 'root';
$dbPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

/**
 * Returns a shared PDO instance
 * @return PDO
 */
function getDbConnection(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        global $dbHost, $dbPort, $dbName, $dbUser, $dbPass;

        $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
        } catch (PDOException $e) {
            // Log error safely without leaking sensitive credentials to client
            error_log("Database Connection Error: " . $e->getMessage());
            die("Koneksi ke database gagal. Pastikan konfigurasi database di config/database.php sudah benar.");
        }
    }

    return $pdo;
}
