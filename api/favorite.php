<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Login required', 'redirect' => '/login.php']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

$movieId = isset($_POST['movie_id']) ? (int)$_POST['movie_id'] : 0;
if ($movieId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid movie_id']);
    exit;
}

$userId = currentUser()['id'];
$pdo    = getDb();

// Check if movie exists
$m = $pdo->prepare("SELECT id FROM movies WHERE id = ?");
$m->execute([$movieId]);
if (!$m->fetch()) {
    http_response_code(404);
    echo json_encode(['error' => 'Movie not found']);
    exit;
}

// Toggle
$check = $pdo->prepare("SELECT id FROM favorites WHERE user_id = ? AND movie_id = ?");
$check->execute([$userId, $movieId]);
$existing = $check->fetch();

if ($existing) {
    $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND movie_id = ?")->execute([$userId, $movieId]);
    echo json_encode(['status' => 'removed', 'message' => 'Removed from favorites']);
} else {
    $pdo->prepare("INSERT INTO favorites (user_id, movie_id) VALUES (?, ?)")->execute([$userId, $movieId]);
    echo json_encode(['status' => 'added', 'message' => 'Added to favorites']);
}
