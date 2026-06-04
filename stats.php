<?php
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/helpers.php';
 
$stats = getPlatformStats($pdo);
 
$genreStats = $pdo->query("
    SELECT g.name, COUNT(r.id) AS review_count, COUNT(DISTINCT r.movie_id) AS movie_count,
           ROUND(AVG(r.rating), 1) AS avg_rating
    FROM genres g
    JOIN movie_genres mg ON g.id = mg.genre_id
    JOIN movies m        ON mg.movie_id = m.id
    LEFT JOIN reviews r  ON m.id = r.movie_id
    GROUP BY g.id, g.name
    ORDER BY review_count DESC
    LIMIT 12
")->fetchAll();
 
$topRated = $pdo->query("
    SELECT m.id, m.title, m.year, m.poster_url,
           ROUND(AVG(r.rating), 2) AS avg_rating,
           COUNT(r.id) AS review_count
    FROM movies m
    JOIN reviews r ON m.id = r.movie_id
    GROUP BY m.id
    HAVING review_count >= 1
    ORDER BY avg_rating DESC, review_count DESC
    LIMIT 10
")->fetchAll();
 
$mostReviewed = $pdo->query("
    SELECT m.id, m.title, m.year, m.poster_url,
           COUNT(r.id) AS review_count,
           ROUND(AVG(r.rating), 1) AS avg_rating
    FROM movies m
    JOIN reviews r ON m.id = r.movie_id
    GROUP BY m.id
    ORDER BY review_count DESC
    LIMIT 10
")->fetchAll();
 
$topReviewers = $pdo->query("
    SELECT u.id, u.username, COUNT(r.id) AS review_count,
           ROUND(AVG(r.rating), 1) AS avg_rating
    FROM users u
    JOIN reviews r ON u.id = r.user_id
    GROUP BY u.id
    ORDER BY review_count DESC
    LIMIT 8
")->fetchAll();
 
$allDist = $pdo->query("
    SELECT rating, COUNT(*) AS cnt
    FROM reviews
    GROUP BY rating
    ORDER BY rating DESC
")->fetchAll(PDO::FETCH_KEY_PAIR);
 
$monthlyActivity = $pdo->query("
    SELECT DATE_FORMAT(created_at, '%b %Y') AS month,
           DATE_FORMAT(created_at, '%Y-%m') AS ym,
           COUNT(*) AS cnt
    FROM reviews
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY ym, month
    ORDER BY ym ASC
")->fetchAll();
 
$maxGenreReviews = !empty($genreStats) ? max(array_column($genreStats, 'review_count')) : 1;
$maxAllDist      = !empty($allDist)    ? max(array_values($allDist)) : 1;
$maxMonthly      = !empty($monthlyActivity) ? max(array_column($monthlyActivity, 'cnt')) : 1;
 
$pageTitle = 'Statistics';
require_once __DIR__ . '/includes/header.php';
?>
 
<main class="page-main stats-page">
  <div class="container">
 
    <div class="page-header">
      <h1 class="page-title">📊 Platform Statistics</h1>
      <p class="page-subtitle">A data-driven look at Movieverse</p>
    </div>
 
    <!-- Platform Numbers -->
    <section class="stats-numbers">
      <div class="stats-grid-4">
        <div class="stat-card">
          <span class="stat-icon">👤</span>
          <span class="stat-big count-up" data-target="<?= $stats['users'] ?>"><?= $stats['users'] ?></span>
          <span class="stat-label">Registered Users</span>
        </div>
        <div class="stat-card">
          <span class="stat-icon">🎬</span>
          <span class="stat-big count-up" data-target="<?= $stats['movies'] ?>"><?= $stats['movies'] ?></span>
          <span class="stat-label">Films Listed</span>
        </div>
        <div class="stat-card">
          <span class="stat-icon">📝</span>
          <span class="stat-big count-up" data-target="<?= $stats['reviews'] ?>"><?= $stats['reviews'] ?></span>
          <span class="stat-label">Reviews Written</span>
        </div>
        <div class="stat-card">
          <span class="stat-icon">🏷</span>
          <span class="stat-big count-up" data-target="<?= $stats['genres'] ?>"><?= $stats['genres'] ?></span>
          <span class="stat-label">Genres</span>
        </div>
      </div>
    </section>
 
    <div class="stats-two-col">
 
      <!-- LEFT column -->
      <div class="stats-col-left">
 
        <!-- Genre Popularity -->
        <section class="stats-section">
          <h2 class="stats-section-title">🏷 Genre Popularity</h2>
          <p class="stats-section-sub">By total number of reviews</p>
          <div class="stat-bars">
            <?php foreach ($genreStats as $g): ?>
            <div class="stat-bar-row">
              <span class="sbar-label"><?= e($g['name']) ?></span>
              <div class="sbar-track">
                <div class="sbar-fill gold"
                  style="width: 0%"
                  data-width="<?= $maxGenreReviews > 0 ? round($g['review_count'] / $maxGenreReviews * 100) : 0 ?>%">
                </div>
              </div>
              <span class="sbar-value"><?= $g['review_count'] ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </section>
 
        <!-- Overall Rating Distribution -->
        <section class="stats-section">
          <h2 class="stats-section-title">⭐ Rating Distribution</h2>
          <p class="stats-section-sub">How users rate films across the platform</p>
          <div class="stat-bars">
            <?php for ($i = 10; $i >= 1; $i--): ?>
              <?php $cnt = $allDist[$i] ?? 0; ?>
              <div class="stat-bar-row">
                <span class="sbar-label"><?= $i ?> ★</span>
                <div class="sbar-track">
                  <div class="sbar-fill <?= ratingColor($i) ?>"
                    style="width: 0%"
                    data-width="<?= $maxAllDist > 0 ? round($cnt / $maxAllDist * 100) : 0 ?>%">
                  </div>
                </div>
                <span class="sbar-value"><?= $cnt ?></span>
              </div>
            <?php endfor; ?>
          </div>
        </section>
 
        <!-- Monthly Activity -->
        <?php if (!empty($monthlyActivity)): ?>
        <section class="stats-section">
          <h2 class="stats-section-title">📅 Review Activity</h2>
          <p class="stats-section-sub">Reviews written per month (last 6 months)</p>
          <div class="bar-chart-wrap">
            <?php foreach ($monthlyActivity as $m): ?>
            <div class="bar-col">
              <span class="bar-val"><?= $m['cnt'] ?></span>
              <div class="bar-bar">
                <div class="bar-fill"
                  style="height: 0%"
                  data-height="<?= $maxMonthly > 0 ? round($m['cnt'] / $maxMonthly * 100) : 0 ?>%">
                </div>
              </div>
              <span class="bar-label"><?= $m['month'] ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </section>
        <?php endif; ?>
 
      </div><!-- /.stats-col-left -->
 
      <!-- RIGHT column -->
      <div class="stats-col-right">
 
        <!-- Top Rated Films -->
        <section class="stats-section">
          <h2 class="stats-section-title">🏆 Top Rated Films</h2>
          <div class="stats-movie-list">
            <?php foreach ($topRated as $i => $m): ?>
            <a href="<?= BASE_URL ?>/movie.php?id=<?= $m['id'] ?>" class="stats-movie-row">
              <span class="rank-num"><?= $i + 1 ?></span>
              <div class="sml-poster">
                <?= posterImg($m['poster_url'], $m['title'], 'sml-poster-img') ?>
              </div>
              <div class="sml-info">
                <span class="sml-title"><?= e($m['title']) ?></span>
                <span class="sml-meta"><?= $m['year'] ?> · <?= $m['review_count'] ?> review<?= $m['review_count'] != 1 ? 's' : '' ?></span>
              </div>
              <span class="rating-badge <?= ratingColor($m['avg_rating']) ?>"><?= $m['avg_rating'] ?></span>
            </a>
            <?php endforeach; ?>
          </div>
        </section>
 
        <!-- Most Reviewed Films -->
        <section class="stats-section">
          <h2 class="stats-section-title">📝 Most Reviewed Films</h2>
          <div class="stats-movie-list">
            <?php foreach ($mostReviewed as $i => $m): ?>
            <a href="<?= BASE_URL ?>/movie.php?id=<?= $m['id'] ?>" class="stats-movie-row">
              <span class="rank-num"><?= $i + 1 ?></span>
              <div class="sml-poster">
                <?= posterImg($m['poster_url'], $m['title'], 'sml-poster-img') ?>
              </div>
              <div class="sml-info">
                <span class="sml-title"><?= e($m['title']) ?></span>
                <span class="sml-meta"><?= $m['year'] ?><?php if ($m['avg_rating']): ?> · avg <?= $m['avg_rating'] ?><?php endif; ?></span>
              </div>
              <span class="review-count-badge"><?= $m['review_count'] ?> reviews</span>
            </a>
            <?php endforeach; ?>
          </div>
        </section>
 
        <!-- Top Reviewers -->
        <?php if (!empty($topReviewers)): ?>
        <section class="stats-section">
          <h2 class="stats-section-title">🌟 Most Active Users</h2>
          <div class="stats-user-list">
            <?php foreach ($topReviewers as $i => $u): ?>
            <a href="<?= BASE_URL ?>/profile.php?id=<?= $u['id'] ?>" class="stats-user-row">
              <span class="rank-num"><?= $i + 1 ?></span>
              <div class="user-avatar-sm"><?= strtoupper(substr(e($u['username']), 0, 1)) ?></div>
              <div class="sml-info">
                <span class="sml-title"><?= e($u['username']) ?></span>
                <span class="sml-meta">Avg rating: <?= $u['avg_rating'] ?? '—' ?></span>
              </div>
              <span class="review-count-badge"><?= $u['review_count'] ?> reviews</span>
            </a>
            <?php endforeach; ?>
          </div>
        </section>
        <?php endif; ?>
 
      </div><!-- /.stats-col-right -->
 
    </div><!-- /.stats-two-col -->
 
  </div><!-- /.container -->
</main>
 
<script>
document.addEventListener('DOMContentLoaded', () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (!entry.isIntersecting) return;
            entry.target.querySelectorAll('.sbar-fill').forEach(bar => {
                bar.style.transition = 'width 0.8s ease';
                bar.style.width = bar.dataset.width;
            });
            entry.target.querySelectorAll('.bar-fill').forEach(bar => {
                bar.style.transition = 'height 0.8s ease';
                bar.style.height = bar.dataset.height;
            });
            observer.unobserve(entry.target);
        });
    }, { threshold: 0.2 });
 
    document.querySelectorAll('.stats-section').forEach(s => observer.observe(s));
});
</script>
 
<?php include __DIR__ . '/includes/footer.php'; ?>