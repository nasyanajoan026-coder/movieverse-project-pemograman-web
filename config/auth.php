<?php
// ================================================
// Authentication & Session Helpers
// ================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Site base URL — auto-detect based on environment
if (!defined('BASE_URL')) {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    if ($host === 'localhost' || str_starts_with($host, 'localhost:') || $host === '127.0.0.1') {
        define('BASE_URL', $scheme . '://' . $host . '/movieverse');
    } else {
        define('BASE_URL', $scheme . '://' . $host);
    }
} 

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Check if current user is admin
 */
function isAdmin(): bool {
    return isLoggedIn() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Get current user data from session
 */
function currentUser(): array {
    if (!isLoggedIn()) return [];
    return [
        'id'       => $_SESSION['user_id'],
        'username' => $_SESSION['username'],
        'email'    => $_SESSION['email'],
        'role'     => $_SESSION['role'],
    ];
}

/**
 * Redirect to login page if not logged in
 */
function requireLogin(string $redirect = ''): void {
    if (!isLoggedIn()) {
        $back = $redirect ?: $_SERVER['REQUEST_URI'];
        header('Location: ' . BASE_URL . '/login.php?redirect=' . urlencode($back));
        exit;
    }
}

/**
 * Redirect to home if not admin
 */
function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) {
        header('Location: ' . BASE_URL . '/index.php?error=unauthorized');
        exit;
    }
}

/**
 * Generate CSRF token
 */
function csrfToken(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token from POST
 */
function verifyCsrf(): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Invalid request. Please go back and try again.');
    }
}

/**
 * Log a user in (set session)
 */
function loginUser(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email']    = $user['email'];
    $_SESSION['role']     = $user['role'];
}

/**
 * Log out current user
 */
function logoutUser(): void {
    session_unset();
    session_destroy();
}

/**
 * Sanitize output for HTML
 */
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * Flash message helpers
 */
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): array {
    $flash = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flash;
}

/**
 * Redirect helper
 */
function redirect(string $url): void {
    header('Location: ' . BASE_URL . $url);
    exit;
}