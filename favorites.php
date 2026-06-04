<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/helpers.php';

requireLogin();

$userId    = currentUser()['id'];
$favorites = getUserFavorites($userId);

include __DIR__ . '/includes/header.php';
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
        <a href="/movies.php" class="btn btn-primary">Browse Movies</a>
      </div>
    <?php else: ?>
      <div class="movie-grid">
        <?php foreach ($favorites as $movie): ?>
          <div class="movie-card">
            <a href="/movie.php?id=<?= $movie['id'] ?>" class="card-poster-link">
              <?= posterImg($movie['poster_url'], $movie['title'], 'card-poster') ?>
              <div class="card-overlay">
                <span class="card-cta">View Details</span>
              </div>
            </a>
            <div class="card-body">
              <a href="/movie.php?id=<?= $movie['id'] ?>" class="card-title"><?= e($movie['title']) ?></a>
              <div class="card-meta">
                <span class="card-year"><?= $movie['year'] ?></span>
                <?php if ($movie['avg_rating']): ?>
                  <span class="rating-badge <?= ratingColor($movie['avg_rating']) ?>">
                    <?= number_format($movie['avg_rating'], 1) ?>
                  </span>
                <?php endif; ?>
              </div>
              <div class="card-actions-row">
                <button class="btn btn-icon fav-btn active"
                  data-movie-id="<?= $movie['id'] ?>"
                  title="Remove from favorites">
                  ❤
                </button>
                <a href="/movie.php?id=<?= $movie['id'] ?>" class="btn btn-ghost btn-sm">Details</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

  </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
