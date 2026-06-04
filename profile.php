<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/helpers.php';

// Determine whose profile to show
$profileId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($profileId <= 0) {
    requireLogin();
    $profileId = currentUser()['id'];
}

$profile = getUserProfile($profileId);
if (!$profile) {
    setFlash('error', 'User not found.');
    redirect('/index.php');
}

$reviews   = getUserReviews($profileId);
$favorites = getUserFavorites($profileId);

// Rating breakdown for this user
$pdo = getDb();
$stmt = $pdo->prepare("SELECT rating, COUNT(*) as cnt FROM reviews WHERE user_id = ? GROUP BY rating ORDER BY rating DESC");
$stmt->execute([$profileId]);
$ratingBreakdown = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$isOwn   = isLoggedIn() && currentUser()['id'] === $profileId;
$isAdmin = isAdmin();

include __DIR__ . '/includes/header.php';
?>

<main class="page-main">
  <!-- Profile Hero -->
  <section class="profile-hero">
    <div class="container">
      <div class="profile-header">
        <div class="profile-avatar-wrap">
          <div class="profile-avatar-large">
            <?= strtoupper(substr(e($profile['username']), 0, 1)) ?>
          </div>
          <?php if ($profile['role'] === 'admin'): ?>
            <span class="admin-badge">Admin</span>
          <?php endif; ?>
        </div>

        <div class="profile-info">
          <h1 class="profile-name"><?= e($profile['username']) ?></h1>
          <p class="profile-email"><?= ($isOwn || $isAdmin) ? e($profile['email']) : '' ?></p>
          <p class="profile-joined">Member since <?= date('F Y', strtotime($profile['created_at'])) ?></p>

          <div class="profile-stats-row">
            <div class="profile-stat">
              <span class="stat-num"><?= $profile['review_count'] ?></span>
              <span class="stat-label">Reviews</span>
            </div>
            <div class="profile-stat">
              <span class="stat-num"><?= $profile['fav_count'] ?></span>
              <span class="stat-label">Favorites</span>
            </div>
            <div class="profile-stat">
              <span class="stat-num"><?= $profile['avg_rating'] ? number_format($profile['avg_rating'], 1) : '—' ?></span>
              <span class="stat-label">Avg Rating</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="container profile-body">
    <div class="profile-grid">

      <!-- Left: Reviews -->
      <section class="profile-reviews">
        <h2 class="section-title">
          Reviews
          <span class="title-count"><?= count($reviews) ?></span>
        </h2>

        <?php if (empty($reviews)): ?>
          <div class="empty-state">
            <div class="empty-icon">🎬</div>
            <p><?= $isOwn ? "You haven't reviewed any films yet." : "This user hasn't reviewed any films yet." ?></p>
            <?php if ($isOwn): ?>
              <a href="/movies.php" class="btn btn-primary">Browse Movies</a>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div class="profile-review-list">
            <?php foreach ($reviews as $r): ?>
            <div class="profile-review-card">
              <a href="/movie.php?id=<?= $r['movie_id'] ?>" class="pr-poster-wrap">
                <?php if ($r['poster_url']): ?>
                  <img src="<?= e($r['poster_url']) ?>" alt="<?= e($r['title']) ?>" loading="lazy">
                <?php else: ?>
                  <div class="poster-fallback sm"><?= strtoupper(substr(e($r['title']), 0, 1)) ?></div>
                <?php endif; ?>
              </a>
              <div class="pr-body">
                <div class="pr-top">
                  <a href="/movie.php?id=<?= $r['movie_id'] ?>" class="pr-movie-title"><?= e($r['title']) ?></a>
                  <span class="rating-badge <?= ratingColor($r['rating']) ?>"><?= $r['rating'] ?>/10</span>
                </div>
                <?php if ($r['review_text']): ?>
                  <p class="pr-text"><?= e($r['review_text']) ?></p>
                <?php endif; ?>
                <div class="pr-meta">
                  <span><?= timeAgo($r['created_at']) ?></span>
                  <?php if ($isOwn || $isAdmin): ?>
                    <a href="/movie.php?id=<?= $r['movie_id'] ?>#reviews" class="pr-link">Edit</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>

      <!-- Right: Sidebar -->
      <aside class="profile-sidebar">

        <!-- Rating Distribution -->
        <?php if (!empty($ratingBreakdown)): ?>
        <div class="sidebar-card">
          <h3 class="sidebar-card-title">Rating Breakdown</h3>
          <div class="rating-dist mini">
            <?php for ($i = 10; $i >= 1; $i--): ?>
              <?php $cnt = $ratingBreakdown[$i] ?? 0; ?>
              <?php $max = max(array_values($ratingBreakdown) ?: [1]); ?>
              <?php $pct = $max > 0 ? ($cnt / $max) * 100 : 0; ?>
              <div class="dist-row">
                <span class="dist-label"><?= $i ?></span>
                <div class="dist-bar-wrap">
                  <div class="dist-bar <?= ratingColor($i) ?>" style="width: <?= $pct ?>%"></div>
                </div>
                <span class="dist-count"><?= $cnt ?></span>
              </div>
            <?php endfor; ?>
          </div>
        </div>
        <?php endif; ?>

        <!-- Favorites -->
        <div class="sidebar-card">
          <h3 class="sidebar-card-title">
            Favorites
            <span class="title-count"><?= count($favorites) ?></span>
          </h3>
          <?php if (empty($favorites)): ?>
            <p class="sidebar-empty">No favorites yet.</p>
          <?php else: ?>
            <div class="fav-grid-mini">
              <?php foreach (array_slice($favorites, 0, 8) as $fav): ?>
                <a href="/movie.php?id=<?= $fav['id'] ?>" class="fav-mini-item" title="<?= e($fav['title']) ?>">
                  <?= posterImg($fav['poster_url'], $fav['title'], 'fav-mini-poster') ?>
                </a>
              <?php endforeach; ?>
            </div>
            <?php if (count($favorites) > 8): ?>
              <a href="/favorites.php<?= $isOwn ? '' : '?user_id='.$profileId ?>" class="view-all-link">
                View all <?= count($favorites) ?> favorites →
              </a>
            <?php endif; ?>
          <?php endif; ?>
        </div>

      </aside>

    </div><!-- /.profile-grid -->
  </div><!-- /.container -->
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
