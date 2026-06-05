<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Login required']);
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

$action   = $_POST['action'] ?? '';
$reviewId = isset($_POST['review_id']) ? (int)$_POST['review_id'] : 0;

if ($reviewId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid review_id']);
    exit;
}
$user = currentUser();

// Fetch review
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE id = ?");
$stmt->execute([$reviewId]);
$review = $stmt->fetch();

if (!$review) {
    http_response_code(404);
    echo json_encode(['error' => 'Review not found']);
    exit;
}

// Authorization: owner or admin
if ($review['user_id'] !== $user['id'] && $user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

if ($action === 'delete') {
    $pdo->prepare("DELETE FROM reviews WHERE id = ?")->execute([$reviewId]);
    echo json_encode(['status' => 'deleted', 'message' => 'Review deleted']);
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Unknown action']);
}
