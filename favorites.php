<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/helpers.php';

requireLogin();

$userId    = currentUser()['id'];
$favorites = getUserFavorites($pdo, $userId);

$pageTitle = 'My Favorites';
require_once __DIR__ . '/includes/header.php';
?>

<main class="page-main">
  <div class="container">

    <div class="page-header">
      <h1 class="page-title">
        <span class="accent">❤</span> My Favorites
      </h1>
      <p class="page-subtitle">
        <?= count($favorites) ?> film<?= count($favorites) !== 1 ? 's' : '' ?> saved
      </p>
    </div>

    <?php if (empty($favorites)): ?>
      <div class="empty-state large">
        <div class="empty-icon">🎬</div>
        <h2>No favorites yet</h2>
        <p>Start exploring films and hit the heart button to save them here.</p>
        <a href="<?= BASE_URL ?>/movies.php" class="btn btn-primary">Browse Movies</a>
      </div>
    <?php else: ?>
      <div class="movies-grid stagger">
        <?php foreach ($favorites as $movie): ?>
          <div class="movie-card">
            <a href="<?= BASE_URL ?>/movie.php?id=<?= $movie['id'] ?>">
              <div class="movie-poster">
                <?= posterImg($movie['poster_url'], $movie['title'], 'poster-img') ?>
                <div class="movie-overlay">
                  <span class="btn btn-primary btn-sm">View Details</span>
                </div>
              </div>
            </a>
            <div class="movie-info">
              <a href="<?= BASE_URL ?>/movie.php?id=<?= $movie['id'] ?>" class="movie-title" title="<?= e($movie['title']) ?>"><?= e($movie['title']) ?></a>
              <div class="movie-meta">
                <span><?= e((string)$movie['year']) ?></span>
                <?php if ($movie['avg_rating']): ?>
                  <span class="rating-badge <?= ratingColor($movie['avg_rating']) ?>">
                    ★ <?= number_format($movie['avg_rating'], 1) ?>
                  </span>
                <?php endif; ?>
              </div>
              <div class="card-actions-row">
                <button class="btn btn-icon fav-btn active"
                  data-movie-id="<?= $movie['id'] ?>"
                  title="Remove from favorites">
                  ❤
                </button>
                <a href="<?= BASE_URL ?>/movie.php?id=<?= $movie['id'] ?>" class="btn btn-ghost btn-sm">Details</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

