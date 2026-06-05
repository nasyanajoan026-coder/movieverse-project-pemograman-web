<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';

requireAdmin();

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    verifyCsrf();
    $id = (int)($_POST['movie_id'] ?? 0);
    if ($id > 0) {
        $pdo->prepare("DELETE FROM movie_genres WHERE movie_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM reviews WHERE movie_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM favorites WHERE movie_id = ?")->execute([$id]);
        $pdo->prepare("DELETE FROM movies WHERE id = ?")->execute([$id]);
        setFlash('success', 'Film deleted successfully.');
    }
    redirect('/admin/movies.php');
}

// Search / filter
$q    = trim($_GET['q'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 20;
$offset  = ($page - 1) * $perPage;

$where  = $q ? "WHERE m.title LIKE :q" : "";
$params = $q ? [':q' => "%$q%"] : [];

$total = $pdo->prepare("SELECT COUNT(*) FROM movies m $where");
$total->execute($params);
$totalCount = (int)$total->fetchColumn();
$totalPages = max(1, (int)ceil($totalCount / $perPage));

$stmt = $pdo->prepare("
    SELECT m.id, m.title, m.year, m.director, m.poster_url,
           ROUND(AVG(r.rating), 1) AS avg_rating,
           COUNT(r.id) AS review_count
    FROM movies m
    LEFT JOIN reviews r ON m.id = r.movie_id
    $where
    GROUP BY m.id
    ORDER BY m.title ASC
    LIMIT $perPage OFFSET $offset
");
$stmt->execute($params);
$movies = $stmt->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<main class="page-main admin-page">
  <div class="admin-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="admin-content">
      <div class="admin-top">
        <h1 class="admin-page-title">Films</h1>
        <a href="<?= BASE_URL ?>/admin/movie-form.php" class="btn btn-primary">+ Add Film</a>
      </div>

      <!-- Search bar -->
      <form method="GET" class="admin-search-form">
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Search films…" class="admin-search-input">
        <button type="submit" class="btn btn-ghost">Search</button>
        <?php if ($q): ?><a href="<?= BASE_URL ?>/admin/movies.php" class="btn btn-ghost">Clear</a><?php endif; ?>
      </form>

      <p class="admin-count"><?= $totalCount ?> film<?= $totalCount !== 1 ? 's' : '' ?><?= $q ? " matching \"".e($q)."\"" : '' ?></p>

      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>Poster</th>
              <th>Title</th>
              <th>Year</th>
              <th>Director</th>
              <th>Rating</th>
              <th>Reviews</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($movies)): ?>
              <tr><td colspan="7" class="table-empty">No films found.</td></tr>
            <?php else: ?>
              <?php foreach ($movies as $m): ?>
              <tr>
                <td>
                  <div class="table-poster">
                    <?= posterImg($m['poster_url'], $m['title'], 'table-poster-img') ?>
                  </div>
                </td>
                <td>
                  <a href="<?= BASE_URL ?>/movie.php?id=<?= $m['id'] ?>" target="_blank" class="table-link">
                    <?= e($m['title']) ?>
                  </a>
                </td>
                <td><?= $m['year'] ?></td>
                <td><?= e($m['director'] ?? '—') ?></td>
                <td>
                  <?php if ($m['avg_rating']): ?>
                    <span class="rating-badge <?= ratingColor($m['avg_rating']) ?>"><?= $m['avg_rating'] ?></span>
                  <?php else: ?><em>—</em><?php endif; ?>
                </td>
                <td><?= $m['review_count'] ?></td>
                <td class="action-cell">
                   <a href="<?= BASE_URL ?>/admin/movie-form.php?id=<?= $m['id'] ?>" class="btn btn-ghost btn-sm">Edit</a>
                  <form method="POST" class="inline-form">
                    <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                    <input type="hidden" name="movie_id" value="<?= $m['id'] ?>">
                    <input type="hidden" name="action" value="delete">
                    <button type="submit" class="btn btn-danger btn-sm"
                      onclick="return confirm('Delete \'<?= addslashes(e($m['title'])) ?>\' and all its reviews? This cannot be undone.')">
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
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
          <a href="?page=<?= $p ?><?= $q ? '&q='.urlencode($q) : '' ?>"
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
