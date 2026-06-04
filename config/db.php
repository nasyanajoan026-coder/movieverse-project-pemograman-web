<?php
// ================================================
// Database Configuration
// Edit these values to match your server
// ================================================
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/movieverse');
}
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Change to your MySQL username
define('DB_PASS', '');            // Change to your MySQL password
define('DB_NAME', 'movieverse');
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
