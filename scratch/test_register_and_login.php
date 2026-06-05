<?php
require_once __DIR__ . '/../config/db.php';

// Try registering member123
$username = 'member123';
$email = 'member123@example.com';
$password = 'password123';
$hash = password_hash($password, PASSWORD_DEFAULT);

// Delete if already exists
$pdo->prepare("DELETE FROM users WHERE username = ?")->execute([$username]);

// Insert
$stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?,?,?)");
$stmt->execute([$username, $email, $hash]);

// Fetch and verify
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

echo "User registered: " . $user['username'] . "\n";
echo "Verify password123: " . (password_verify($password, $user['password']) ? 'SUCCESS' : 'FAILED') . "\n";
