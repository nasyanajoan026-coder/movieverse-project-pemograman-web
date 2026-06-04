<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'Movies';

// Filter params
$search  = trim($_GET['q']     ?? '');
$genre   = trim($_GET['genre'] ?? '');
$sort    = in_array($_GET['sort'] ?? '', ['latest','rating','reviews','title']) ? $_GET['sort'] : 'latest';
$perPage = 24;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

$total    = countMovies($pdo, $search, $genre);
$movies   = getMovies($pdo, compact('search', 'genre', 'sort') + ['limit' => $perPage, 'offset' => $offset]);
$genres   = getGenres($pdo, true);
$pages    = (int)ceil($total / $perPage);

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<div style="background: var(--bg-surface); border-bottom: 1px solid var(--border); padding: var(--s-10) 0 var(--s-8);">
    <div class="container">
        <h1 style="font-family: var(--font-hero); font-size: clamp(2rem,5vw,3.5rem); color: var(--text-primary); margin-bottom: var(--s-2);">
            All Films
        </h1>
        <p style="color: var(--text-secondary);">
            <?= number_format($total) ?> film<?= $total !== 1 ? 's' : '' ?>
            <?php if ($search): ?> matching "<strong style="color:var(--accent)"><?= e($search) ?></strong>"<?php endif; ?>
            <?php if ($genre): ?> in genre <strong style="color:var(--accent)"><?= e(ucfirst($genre)) ?></strong><?php endif; ?>
        </p>
    </div>
</div>

<div class="container" style="padding-top: var(--s-8); padding-bottom: var(--s-16);">

    <!-- Filter Bar -->
    <form method="GET" id="filterForm">
        <div class="filter-bar">
            <!-- Search Input -->
            <div class="filter-search" style="flex:1; min-width: 220px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="q" id="searchInput" 
                       value="<?= e($search) ?>"
                       placeholder="Search films, directors…">
            </div>
            <!-- Hidden genre input -->
            <input type="hidden" name="genre" id="genreInput" value="<?= e($genre) ?>">
            <input type="hidden" name="page" value="1">
            <!-- Sort -->
            <div class="filter-sort">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:16px;height:16px;color:var(--text-secondary)"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                <select name="sort" id="sortSelect">
                    <option value="latest"  <?= $sort==='latest'  ? 'selected':'' ?>>Latest Added</option>
                    <option value="rating"  <?= $sort==='rating'  ? 'selected':'' ?>>Highest Rated</option>
                    <option value="reviews" <?= $sort==='reviews' ? 'selected':'' ?>>Most Reviewed</option>
                    <option value="title"   <?= $sort==='title'   ? 'selected':'' ?>>A–Z</option>
                </select>
            </div>
            <?php if ($search || $genre): ?>
            <a href="movies.php" class="btn btn-ghost btn-sm">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Clear
            </a>
            <?php endif; ?>
        </div>

        <!-- Genre Chips -->
        <div class="genre-chips" style="margin-bottom: var(--s-8);">
            <button type="button" class="genre-chip <?= !$genre ? 'active' : '' ?>" data-genre="" onclick="document.getElementById('genreInput').value=''; document.getElementById('filterForm').submit()">All</button>
            <?php foreach ($genres as $g): ?>
            <button type="button" class="genre-chip <?= $genre === $g['slug'] ? 'active' : '' ?>"
                    data-genre="<?= e($g['slug']) ?>"
                    onclick="document.getElementById('genreInput').value='<?= e($g['slug']) ?>'; document.getElementById('filterForm').submit()">
                <?= e($g['name']) ?>
            </button>
            <?php endforeach; ?>
        </div>
    </form>

    <!-- Movie Grid -->
    <?php if (empty($movies)): ?>
    <div class="empty-state">
        <div class="empty-icon">🎬</div>
        <h3>No films found</h3>
        <p>Try a different search or filter.</p>
        <a href="movies.php" class="btn btn-ghost" style="margin-top:var(--s-4)">Browse All Films</a>
    </div>
    <?php else: ?>
    <div class="movies-grid stagger">
        <?php foreach ($movies as $movie): ?>
        <?php include __DIR__ . '/includes/movie-card.php'; ?>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($pages > 1): ?>
    <nav class="pagination" aria-label="Page navigation">
        <?php
        $queryParams = http_build_query(array_filter(['q'=>$search,'genre'=>$genre,'sort'=>$sort]));
        ?>
        <a href="?<?= $queryParams ?>&page=<?= max(1,$page-1) ?>" 
           class="page-btn <?= $page<=1 ? 'disabled':'' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="15 18 9 12 15 6"/></svg>
        </a>
        <?php
        $start = max(1, $page-2);
        $end   = min($pages, $page+2);
        if ($start > 1): ?>
        <a href="?<?= $queryParams ?>&page=1" class="page-btn">1</a>
        <?php if ($start > 2): ?><span class="page-btn disabled">…</span><?php endif; ?>
        <?php endif; ?>

        <?php for ($i=$start; $i<=$end; $i++): ?>
        <a href="?<?= $queryParams ?>&page=<?= $i ?>" class="page-btn <?= $i===$page ? 'active':'' ?>"><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($end < $pages): ?>
        <?php if ($end < $pages-1): ?><span class="page-btn disabled">…</span><?php endif; ?>
        <a href="?<?= $queryParams ?>&page=<?= $pages ?>" class="page-btn"><?= $pages ?></a>
        <?php endif; ?>

        <a href="?<?= $queryParams ?>&page=<?= min($pages,$page+1) ?>" 
           class="page-btn <?= $page>=$pages ? 'disabled':'' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="9 18 15 12 9 6"/></svg>
        </a>
    </nav>
    <?php endif; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
