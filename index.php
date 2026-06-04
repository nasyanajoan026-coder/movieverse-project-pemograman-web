<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'Home';

// Data for the page
$filmOfMonth = getFilmOfMonth($pdo);
$latestMovies = getMovies($pdo, ['sort' => 'latest', 'limit' => 12]);
$topRated     = getMovies($pdo, ['sort' => 'rating',  'limit' => 12]);
$mostReviewed = getMovies($pdo, ['sort' => 'reviews', 'limit' => 12]);
$genres       = getGenres($pdo, true);
$platformStats = getPlatformStats($pdo);

include __DIR__ . '/includes/header.php';
?>

<!-- HERO: FILM OF THE MONTH -->
<?php if ($filmOfMonth): ?>
<section class="hero">
    <div class="hero-backdrop" style="background-image:url('<?= e($filmOfMonth['poster_url']) ?>')"></div>
    <div class="hero-gradient"></div>
    <div class="hero-content">
        <div class="hero-badge">
            <svg viewBox="0 0 24 24" fill="currentColor" width="12" height="12"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            FILM OF THE MONTH
        </div>
        <h1 class="hero-title"><?= e($filmOfMonth['title']) ?></h1>
        <div class="hero-meta">
            <span class="rating-badge rating-gold">
                ★ <?= $filmOfMonth['avg_rating'] ?>
            </span>
            <span class="detail-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <?= e($filmOfMonth['year']) ?>
            </span>
            <span class="detail-chip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                <?= $filmOfMonth['review_count'] ?> reviews
            </span>
        </div>
        <?php if ($filmOfMonth['genres']): ?>
        <div class="hero-genres">
            <?php foreach (explode(', ', $filmOfMonth['genres']) as $g): ?>
            <span class="hero-genre-chip"><?= e(trim($g)) ?></span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php if ($filmOfMonth['synopsis']): ?>
        <p class="hero-synopsis"><?= e(truncate($filmOfMonth['synopsis'], 160)) ?></p>
        <?php endif; ?>
        <div class="hero-actions">
            <a href="<?= BASE_URL ?>/movie.php?id=<?= $filmOfMonth['id'] ?>" class="btn btn-primary btn-lg">
                <svg viewBox="0 0 24 24" fill="currentColor" width="18" height="18"><path d="M8 5v14l11-7z"/></svg>
                View Film
            </a>
            <a href="<?= BASE_URL ?>/movies.php" class="btn btn-ghost btn-lg">Browse All</a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- STATS STRIP -->
<section style="padding: 2.5rem 0;">
    <div class="container">
        <div class="stats-strip">
            <div class="stat-item">
                <span class="stat-number" data-count-to="<?= $platformStats['users'] ?>"><?= $platformStats['users'] ?></span>
                <div class="stat-label">👤 Members</div>
            </div>
            <div class="stat-item">
                <span class="stat-number" data-count-to="<?= $platformStats['movies'] ?>"><?= $platformStats['movies'] ?></span>
                <div class="stat-label">🎬 Films</div>
            </div>
            <div class="stat-item">
                <span class="stat-number" data-count-to="<?= $platformStats['reviews'] ?>"><?= $platformStats['reviews'] ?></span>
                <div class="stat-label">📝 Reviews</div>
            </div>
            <div class="stat-item">
                <span class="stat-number" data-count-to="<?= $platformStats['genres'] ?>"><?= $platformStats['genres'] ?></span>
                <div class="stat-label">🏷️ Genres</div>
            </div>
        </div>
    </div>
</section>

<!-- POPULAR GENRES -->
<section class="page-section" style="padding-top:0;">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><span class="title-line"></span> Browse by Genre</h2>
            <a href="<?= BASE_URL ?>/movies.php" class="btn btn-ghost btn-sm">All Films →</a>
        </div>
        <div class="genre-chips">
            <?php foreach (array_slice($genres, 0, 10) as $genre): ?>
            <a href="<?= BASE_URL ?>/movies.php?genre=<?= e($genre['slug']) ?>" class="genre-chip">
                <?= e($genre['name']) ?>
                <?php if (!empty($genre['movie_count'])): ?>
                <span style="opacity:.6;font-size:.7rem;margin-left:2px"><?= $genre['movie_count'] ?></span>
                <?php endif; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- LATEST ADDED -->
<section class="page-section" style="padding-top:0;">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><span class="title-line"></span> Latest Added</h2>
            <a href="<?= BASE_URL ?>/movies.php?sort=latest" class="btn btn-ghost btn-sm">See All →</a>
        </div>
        <div class="scroll-row-wrap">
            <button class="scroll-btn scroll-btn-left" aria-label="Scroll left">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <div class="scroll-row stagger">
                <?php foreach ($latestMovies as $movie): ?>
                <?php include __DIR__ . '/includes/movie-card.php'; ?>
                <?php endforeach; ?>
            </div>
            <button class="scroll-btn scroll-btn-right" aria-label="Scroll right">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </div>
</section>

<!-- TOP RATED -->
<section class="page-section" style="padding-top:0;">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><span class="title-line"></span> ⭐ Top Rated</h2>
            <a href="<?= BASE_URL ?>/movies.php?sort=rating" class="btn btn-ghost btn-sm">See All →</a>
        </div>
        <div class="scroll-row-wrap">
            <button class="scroll-btn scroll-btn-left" aria-label="Scroll left">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <div class="scroll-row stagger">
                <?php foreach ($topRated as $movie): ?>
                <?php include __DIR__ . '/includes/movie-card.php'; ?>
                <?php endforeach; ?>
            </div>
            <button class="scroll-btn scroll-btn-right" aria-label="Scroll right">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </div>
</section>

<!-- MOST REVIEWED -->
<section class="page-section" style="padding-top:0; padding-bottom: var(--s-16);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><span class="title-line"></span> 💬 Most Reviewed</h2>
            <a href="<?= BASE_URL ?>/movies.php?sort=reviews" class="btn btn-ghost btn-sm">See All →</a>
        </div>
        <div class="scroll-row-wrap">
            <button class="scroll-btn scroll-btn-left" aria-label="Scroll left">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <div class="scroll-row stagger">
                <?php foreach ($mostReviewed as $movie): ?>
                <?php include __DIR__ . '/includes/movie-card.php'; ?>
                <?php endforeach; ?>
            </div>
            <button class="scroll-btn scroll-btn-right" aria-label="Scroll right">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
