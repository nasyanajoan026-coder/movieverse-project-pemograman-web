<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false]);
    exit;
}

$movieId = (int)($_POST['movie_id'] ?? 0);
if ($movieId <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid movie_id']);
    exit;
}

$userId = currentUser()['id'];

// Toggle favorite
$check = $pdo->prepare("SELECT user_id FROM favorites WHERE user_id = ? AND movie_id = ?");
$check->execute([$userId, $movieId]);
$existing = $check->fetch();

if ($existing) {
    $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND movie_id = ?")
        ->execute([$userId, $movieId]);
    echo json_encode(['success' => true, 'favorited' => false]);
} else {
    $pdo->prepare("INSERT INTO favorites (user_id, movie_id, created_at) VALUES (?, ?, NOW())")
        ->execute([$userId, $movieId]);
    echo json_encode(['success' => true, 'favorited' => true]);
}