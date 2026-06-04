<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';

requireAdmin();

$pdo = getDb();

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid CSRF token.');
    } else {
        $rid = (int)($_POST['review_id'] ?? 0);
        if ($rid > 0) {
            $pdo->prepare("DELETE FROM reviews WHERE id = ?")->execute([$rid]);
            setFlash('success', 'Review deleted.');
        }
    }
    redirect('/admin/reviews.php' . ($_GET['page'] ? '?page='.(int)$_GET['page'] : ''));
}

// Filters
$q      = trim($_GET['q'] ?? '');
$rating = isset($_GET['rating']) ? (int)$_GET['rating'] : 0;
$page   = max(1, (int)($_GET['page'] ?? 1));
$perPage = 25;
$offset  = ($page - 1) * $perPage;

$where  = [];
$params = [];
if ($q) {
    $where[]    = "(u.username LIKE :q OR m.title LIKE :q2 OR r.review_text LIKE :q3)";
    $params[':q']  = "%$q%";
    $params[':q2'] = "%$q%";
    $params[':q3'] = "%$q%";
}
if ($rating > 0 && $rating <= 10) {
    $where[]          = "r.rating = :rating";
    $params[':rating'] = $rating;
}
$whereStr = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$total = $pdo->prepare("
    SELECT COUNT(*) FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN movies m ON r.movie_id = m.id
    $whereStr
");
$total->execute($params);
$totalCount = (int)$total->fetchColumn();
$totalPages = max(1, (int)ceil($totalCount / $perPage));

$stmt = $pdo->prepare("
    SELECT r.id, r.rating, r.review_text, r.created_at,
           u.id AS user_id, u.username,
           m.id AS movie_id, m.title AS movie_title
    FROM reviews r
    JOIN users u ON r.user_id = u.id
    JOIN movies m ON r.movie_id = m.id
    $whereStr
    ORDER BY r.created_at DESC
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$reviews = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<main class="page-main admin-page">
  <div class="admin-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="admin-content">
      <div class="admin-top">
        <h1 class="admin-page-title">Reviews</h1>
        <span class="admin-badge-label"><?= $totalCount ?> total</span>
      </div>

      <!-- Filter bar -->
      <form method="GET" class="admin-filter-bar">
        <input type="text" name="q" value="<?= e($q) ?>"
          placeholder="Search user, film, or text…" class="admin-search-input">
        <select name="rating" class="admin-select">
          <option value="">All ratings</option>
          <?php for ($i = 10; $i >= 1; $i--): ?>
            <option value="<?= $i ?>" <?= $rating === $i ? 'selected' : '' ?>><?= $i ?>/10</option>
          <?php endfor; ?>
        </select>
        <button type="submit" class="btn btn-ghost">Filter</button>
        <?php if ($q || $rating): ?>
          <a href="/admin/reviews.php" class="btn btn-ghost">Clear</a>
        <?php endif; ?>
      </form>

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
            <?php if (empty($reviews)): ?>
              <tr><td colspan="6" class="table-empty">No reviews found.</td></tr>
            <?php else: ?>
              <?php foreach ($reviews as $r): ?>
              <tr>
                <td><a href="/profile.php?id=<?= $r['user_id'] ?>"><?= e($r['username']) ?></a></td>
                <td><a href="/movie.php?id=<?= $r['movie_id'] ?>" target="_blank"><?= e($r['movie_title']) ?></a></td>
                <td><span class="rating-badge <?= ratingColor($r['rating']) ?>"><?= $r['rating'] ?></span></td>
                <td class="review-cell">
                  <?php if ($r['review_text']): ?>
                    <span title="<?= e($r['review_text']) ?>"><?= e(truncate($r['review_text'], 80)) ?></span>
                  <?php else: ?>
                    <em class="text-muted">—</em>
                  <?php endif; ?>
                </td>
                <td class="nowrap"><?= date('d M Y', strtotime($r['created_at'])) ?></td>
                <td>
                  <form method="POST" class="inline-form">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="review_id" value="<?= $r['id'] ?>">
                    <button type="submit" class="btn btn-danger btn-sm"
                      onclick="return confirm('Delete this review by <?= addslashes(e($r['username'])) ?>?')">
                      Delete
                    </button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if ($totalPages > 1): ?>
      <div class="pagination">
        <?php
        $qs = http_build_query(array_filter(['q'=>$q,'rating'=>$rating?:null]));
        for ($p = 1; $p <= $totalPages; $p++):
        ?>
          <a href="?page=<?= $p ?><?= $qs ? '&'.$qs : '' ?>"
             class="page-btn <?= $p === $page ? 'active' : '' ?>">
            <?= $p ?>
          </a>
        <?php endfor; ?>
      </div>
      <?php endif; ?>

    </div>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
