<?php
$currentAdminPage = basename($_SERVER['PHP_SELF']);
function adminNavClass($page) {
    global $currentAdminPage;
    return $currentAdminPage === $page ? 'class="active"' : '';
}
?>
<aside class="admin-sidebar">
  <div class="admin-sidebar-logo">
    <a href="<?= BASE_URL ?>/index.php">🎬 Movieverse</a>
  </div>
  <nav class="admin-nav">
    <span class="admin-nav-label">Overview</span>
    <a href="<?= BASE_URL ?>/panel/index.php" <?= adminNavClass('index.php') ?>>
      <span>📊</span> Dashboard
    </a>

    <span class="admin-nav-label">Content</span>
    <a href="<?= BASE_URL ?>/panel/movies.php" <?= adminNavClass('movies.php') ?>>
      <span>🎬</span> Films
    </a>
    <a href="<?= BASE_URL ?>/panel/movie-form.php" <?= adminNavClass('movie-form.php') ?>>
      <span>➕</span> Add Film
    </a>
    <a href="<?= BASE_URL ?>/panel/genres.php" <?= adminNavClass('genres.php') ?>>
      <span>🏷</span> Genres
    </a>

    <span class="admin-nav-label">Community</span>
    <a href="<?= BASE_URL ?>/panel/reviews.php" <?= adminNavClass('reviews.php') ?>>
      <span>📝</span> Reviews
    </a>

    <span class="admin-nav-label">Site</span>
    <a href="<?= BASE_URL ?>/stats.php" target="_blank">
      <span>📈</span> Statistics
    </a>
    <a href="<?= BASE_URL ?>/index.php">
      <span>🏠</span> Back to Site
    </a>
    <a href="<?= BASE_URL ?>/logout.php" class="admin-logout">
      <span>🚪</span> Logout
    </a>
  </nav>
</aside>

