<?php
require_once __DIR__ . '/../config/db.php';
$stmt = $pdo->query("SELECT id, username, email, role, SUBSTRING(password, 1, 10) as pass_start, LENGTH(password) as pass_len FROM users");
print_r($stmt->fetchAll());
