<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';

requireAdmin();

// $pdo is loaded globally from config/db.php
$rawStats = getPlatformStats($pdo);
$stats = [
    'total_movies'  => $rawStats['movies'] ?? 0,
    'total_genres'  => $rawStats['genres'] ?? 0,
    'total_reviews' => $rawStats['reviews'] ?? 0,
    'total_users'   => $rawStats['users'] ?? 0,
];

// Recent reviews
$recentReviews = $pdo->query("
    SELECT r.id, r.rating, r.review_text, r.created_at,
           u.username, u.id AS user_id,
           m.title AS movie_title, m.id AS movie_id
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN movies m ON r.movie_id = m.id
    ORDER BY r.created_at DESC
    LIMIT 8
")->fetchAll();

// Recent registrations
$recentUsers = $pdo->query("
    SELECT id, username, email, role, created_at FROM users ORDER BY created_at DESC LIMIT 6
")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<main class="page-main admin-page">
  <div class="admin-layout">

    <!-- Sidebar -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Content -->
    <div class="admin-content">
      <div class="admin-top">
        <h1 class="admin-page-title">Dashboard</h1>
        <span class="admin-badge-label">Admin Panel</span>
      </div>

      <!-- Stat Cards -->
      <div class="admin-stats-grid">
        <div class="admin-stat-card">
          <span class="asc-icon">🎬</span>
          <div class="asc-body">
            <span class="asc-num count-up" data-target="<?= $stats['total_movies'] ?>"><?= $stats['total_movies'] ?></span>
            <span class="asc-label">Total Films</span>
          </div>
          <a href="<?= BASE_URL ?>/admin/movies.php" class="asc-link">Manage →</a>
        </div>
        <div class="admin-stat-card">
          <span class="asc-icon">🏷</span>
          <div class="asc-body">
            <span class="asc-num count-up" data-target="<?= $stats['total_genres'] ?>"><?= $stats['total_genres'] ?></span>
            <span class="asc-label">Genres</span>
          </div>
          <a href="<?= BASE_URL ?>/admin/genres.php" class="asc-link">Manage →</a>
        </div>
        <div class="admin-stat-card">
          <span class="asc-icon">📝</span>
          <div class="asc-body">
            <span class="asc-num count-up" data-target="<?= $stats['total_reviews'] ?>"><?= $stats['total_reviews'] ?></span>
            <span class="asc-label">Reviews</span>
          </div>
          <a href="<?= BASE_URL ?>/admin/reviews.php" class="asc-link">Moderate →</a>
        </div>
        <div class="admin-stat-card">
          <span class="asc-icon">👤</span>
          <div class="asc-body">
            <span class="asc-num count-up" data-target="<?= $stats['total_users'] ?>"><?= $stats['total_users'] ?></span>
            <span class="asc-label">Users</span>
          </div>
          <span class="asc-link">—</span>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="admin-quick-actions">
        <a href="<?= BASE_URL ?>/admin/movie-form.php" class="btn btn-primary">+ Add New Film</a>
        <a href="<?= BASE_URL ?>/admin/genres.php?action=add" class="btn btn-outline-gold">+ Add Genre</a>
        <a href="<?= BASE_URL ?>/stats.php" class="btn btn-ghost" target="_blank">View Stats Page</a>
      </div>

      <!-- Recent Reviews -->
      <section class="admin-section">
        <div class="admin-section-header">
          <h2>Recent Reviews</h2>
          <a href="<?= BASE_URL ?>/admin/reviews.php" class="view-all-link">View all →</a>
        </div>
        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr>
                <th>User</th>
                <th>Film</th>
                <th>Rating</th>
                <th>Review</th>
                <th>Date</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($recentReviews)): ?>
                <tr><td colspan="6" class="table-empty">No reviews yet.</td></tr>
              <?php else: ?>
                <?php foreach ($recentReviews as $r): ?>
                <tr>
                  <td><a href="<?= BASE_URL ?>/profile.php?id=<?= $r['user_id'] ?>"><?= e($r['username']) ?></a></td>
                  <td><a href="<?= BASE_URL ?>/movie.php?id=<?= $r['movie_id'] ?>"><?= e($r['movie_title']) ?></a></td>
                  <td><span class="rating-badge <?= ratingColor($r['rating']) ?>"><?= $r['rating'] ?></span></td>
                  <td class="review-cell"><?= $r['review_text'] ? e(truncate($r['review_text'], 60)) : '<em>—</em>' ?></td>
                  <td><?= date('d M Y', strtotime($r['created_at'])) ?></td>
                  <td>
                    <form method="POST" action="<?= BASE_URL ?>/admin/reviews.php" class="inline-form">
                      <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                      <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                      <input type="hidden" name="action" value="delete">
                      <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this review?')">Delete</button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </section>

      <!-- Recent Users -->
      <section class="admin-section">
        <div class="admin-section-header">
          <h2>Recent Registrations</h2>
        </div>
        <div class="admin-table-wrap">
          <table class="admin-table">
            <thead>
              <tr><th>#</th><th>Username</th><th>Email</th><th>Role</th><th>Joined</th></tr>
            </thead>
            <tbody>
              <?php foreach ($recentUsers as $u): ?>
              <tr>
                <td><?= $u['id'] ?></td>
                <td><a href="<?= BASE_URL ?>/profile.php?id=<?= $u['id'] ?>"><?= e($u['username']) ?></a></td>
                <td><?= e($u['email']) ?></td>
                <td><span class="role-badge <?= $u['role'] ?>"><?= $u['role'] ?></span></td>
                <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>

    </div><!-- /.admin-content -->
  </div><!-- /.admin-layout -->
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
