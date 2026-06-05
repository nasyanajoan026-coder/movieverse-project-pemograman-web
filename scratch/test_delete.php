<?php
require_once __DIR__ . '/../config/db.php';

$id = 13;

$stmt1 = $pdo->prepare("DELETE FROM movie_genres WHERE movie_id = ?");
$stmt1->execute([$id]);
echo "Deleted genres: " . $stmt1->rowCount() . "\n";

$stmt2 = $pdo->prepare("DELETE FROM reviews WHERE movie_id = ?");
$stmt2->execute([$id]);
echo "Deleted reviews: " . $stmt2->rowCount() . "\n";

$stmt3 = $pdo->prepare("DELETE FROM favorites WHERE movie_id = ?");
$stmt3->execute([$id]);
echo "Deleted favorites: " . $stmt3->rowCount() . "\n";

$stmt4 = $pdo->prepare("DELETE FROM movies WHERE id = ?");
$stmt4->execute([$id]);
echo "Deleted movies: " . $stmt4->rowCount() . "\n";
