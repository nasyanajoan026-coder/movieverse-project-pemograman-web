<?php
require_once __DIR__ . '/../config/db.php';
$stmt = $pdo->prepare("SELECT id, username, email, role, password, LENGTH(password) as pass_len FROM users WHERE username = ?");
$stmt->execute(['marklee02']);
$user = $stmt->fetch();

if ($user) {
    echo "FOUND!\n";
    print_r($user);
} else {
    echo "NOT FOUND!\n";
    // Also list all users
    $all = $pdo->query("SELECT id, username FROM users")->fetchAll();
    print_r($all);
}
