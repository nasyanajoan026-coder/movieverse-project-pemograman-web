<?php
// ================================================
// Database Configuration
// Edit these values to match your server
// ================================================
if (!defined('BASE_URL')) {
    // Auto-detect: if running on hosting, use the domain; otherwise use localhost
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    if ($host === 'localhost' || str_starts_with($host, 'localhost:') || $host === '127.0.0.1') {
        define('BASE_URL', $scheme . '://' . $host . '/movieverse');
    } else {
        // On hosting (movieverse.page.gd), no subfolder needed
        define('BASE_URL', $scheme . '://' . $host);
    }
}
define('DB_HOST', 'sql212.infinityfree.com');
define('DB_USER', 'if0_42095667');
define('DB_PASS', 'MovieVerse123');
define('DB_NAME', 'if0_42095667_db_movieverse');
define('DB_CHARSET', 'utf8mb4');

// Establish PDO connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // In production, log this error instead of showing it
    die('Database connection failed. Please check your configuration.');
}