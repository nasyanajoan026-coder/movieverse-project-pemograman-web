<?php
require_once __DIR__ . '/../config/db.php';

$newHash = password_hash('password', PASSWORD_DEFAULT);
echo "New hash: " . $newHash . "\n";

$stmt = $pdo->prepare("UPDATE users SET password = ?");
$stmt->execute([$newHash]);

echo "All user passwords have been updated to the hash of 'password'.\n";
