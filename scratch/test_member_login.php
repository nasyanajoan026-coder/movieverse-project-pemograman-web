<?php
require_once __DIR__ . '/../config/db.php';
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute(['cinephile99']);
$user = $stmt->fetch();

echo "User: " . $user['username'] . "\n";
echo "Hash: " . $user['password'] . "\n";
echo "Verify 'password': " . (password_verify('password', $user['password']) ? 'SUCCESS' : 'FAILED') . "\n";
