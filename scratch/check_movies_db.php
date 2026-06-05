<?php
require_once __DIR__ . '/../config/db.php';
$stmt = $pdo->query("SELECT id, title, year, director FROM movies");
print_r($stmt->fetchAll());
