<?php
require_once __DIR__ . '/../config/db.php';
$stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->execute([13]);
print_r($stmt->fetch());
