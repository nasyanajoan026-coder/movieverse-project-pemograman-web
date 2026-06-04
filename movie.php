<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/helpers.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: ' . BASE_URL . '/movies.php'); exit; }

$movie = getMovie($pdo, $id);
if (!$movie) { header('Location: ' . BASE_URL . '/movies.php?error=notfound'); exit; }

$pageTitle = $movie['title'];
$reviews   = getMovieReviews($pdo, $id);
$dist      = getRatingDistribution($pdo, $id);
$maxDist   = max(array_merge(array_values($dist), [1]));

$user = currentUser();
$userReview = $user ? getUserReview($pdo, $user['id'], $id) : false;
$isFav      = $user ? isFavorited($pdo, $user['id'], $id) : false;

// Handle POST (write/edit review)
$reviewErrors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'review_submit') {
        $rating = (float)($_POST['rating'] ?? 0);
        $text   = trim($_POST['review_text'] ?? '');
        if ($rating < 1 || $rating > 10) $reviewErrors[] = 'Rating must be between 1–10.';
        if (empty($reviewErrors)) {
            try {
                if ($userReview) {
                    $stmt = $pdo->prepare("UPDATE reviews SET rating=?, review_text=?, updated_at=NOW() WHERE id=? AND user_id=?");
                    $stmt->execute([$rating, $text ?: null, $userReview['id'], $user['id']]);
                    setFlash('success', 'Your review was updated!');
                } else {
                    $stmt = $pdo->prepare("INSERT INTO reviews (user_id, movie_id, rating, review_text) VALUES (?,?,?,?)");
                    $stmt->execute([$user['id'], $id, $rating, $text ?: null]);
                    setFlash('success', 'Review submitted! Thanks for sharing.');
                }
                header('Location: movie.php?id=' . $id . '#reviews');
                exit;
            } catch (PDOException $e) {
                $reviewErrors[] = 'Could not save review. Try again.';
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<input type="hidden" id="csrfToken" value="<?= e(csrfToken()) ?>">

<!-- MOVIE HERO -->
<div class="movie-hero">
    <div class="movie-hero-bg" style="background-image:url('<?= e($movie['poster_url'] ?? '') ?>')"></div>
    <div class="movie-hero-grad"></div>
    <div class="movie-hero-content container">
        <!-- Poster -->
        <div class="movie-poster-lg">
            <?= posterImg($movie['poster_url'] ?? '', $movie['title']) ?>
        </div>
        <!-- Info -->
        <div class="movie-detail-info">
            <h1 class="movie-detail-title"><?= e($movie['title']) ?></h1>
            <div class="movie-detail-meta">
                <span class="detail-chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <?= e((string)$movie['year']) ?>
                </span>
                <?php if ($movie['director']): ?>
                <span class="detail-chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <?= e($movie['director']) ?>
                </span>
                <?php endif; ?>
                <?php if ($movie['duration']): ?>
                <span class="detail-chip">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <?= floor($movie['duration']/60) ?>h <?= $movie['duration']%60 ?>m
                </span>
                <?php endif; ?>
            </div>
            <?php if ($movie['genres']): ?>
            <div style="display:flex;gap:var(--s-2);flex-wrap:wrap;margin-bottom:var(--s-5);">
                <?php foreach (explode(', ', $movie['genres']) as $g): ?>
                <a href="<?= BASE_URL ?>/movies.php?genre=<?= urlencode(strtolower(trim($g))) ?>" class="hero-genre-chip" style="transition:var(--t-fast);"><?= e(trim($g)) ?></a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <!-- Big Rating -->
            <div style="display:flex; align-items:baseline; gap:var(--s-4); margin-bottom:var(--s-6);">
                <div class="movie-big-rating">
                    <?php if ($movie['avg_rating'] > 0): ?>
                    <?= number_format((float)$movie['avg_rating'], 1) ?><small>/10</small>
                    <?php else: ?>
                    <span style="font-size:1.5rem;color:var(--text-muted);">No ratings yet</span>
                    <?php endif; ?>
                </div>
                <?php if ($movie['review_count'] > 0): ?>
                <span style="color:var(--text-secondary);font-size:.875rem;"><?= $movie['review_count'] ?> review<?= $movie['review_count']!=1?'s':'' ?></span>
                <?php endif; ?>
            </div>
            <!-- Actions -->
            <div style="display:flex;gap:var(--s-3);flex-wrap:wrap;align-items:center;">
                <a href="#write-review" class="btn btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    <?= $userReview ? 'Edit Review' : 'Write Review' ?>
                </a>
                <?php if (isLoggedIn()): ?>
                <button class="fav-btn <?= $isFav ? 'active' : '' ?>" id="favBtn" data-movie-id="<?= $id ?>">
                    <svg viewBox="0 0 24 24" fill="<?= $isFav ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    <span class="fav-label"><?= $isFav ? 'Favorited' : 'Add to Favorites' ?></span>
                </button>
                <?php endif; ?>
                <?php if ($movie['trailer_url']): ?>
                <a href="<?= e($movie['trailer_url']) ?>" target="_blank" rel="noopener" class="btn btn-ghost">
                    <svg viewBox="0 0 24 24" fill="currentColor" width="16" height="16"><path d="M8 5v14l11-7z"/></svg>
                    Watch Trailer
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="container" style="padding-top: var(--s-10); padding-bottom: var(--s-16);">
    <div style="display:grid; grid-template-columns: 1fr 320px; gap: var(--s-10); align-items: start;">

        <!-- LEFT: Synopsis + Reviews -->
        <div>
            <!-- Synopsis -->
            <?php if ($movie['synopsis']): ?>
            <div class="card" style="margin-bottom: var(--s-8);">
                <h2 class="card-title">Synopsis</h2>
                <p style="color:var(--text-secondary);line-height:1.8;"><?= e($movie['synopsis']) ?></p>
            </div>
            <?php endif; ?>

            <!-- Write / Edit Review -->
            <div id="write-review" class="card" style="margin-bottom: var(--s-8);">
                <?php if (isLoggedIn()): ?>
                <h2 class="card-title"><?= $userReview ? 'Edit Your Review' : 'Write a Review' ?></h2>
                <?php if (!empty($reviewErrors)): ?>
                <div style="background:var(--danger-bg);border:1px solid rgba(229,53,53,.3);border-radius:var(--r-md);padding:var(--s-4);margin-bottom:var(--s-5);">
                    <?php foreach ($reviewErrors as $err): ?>
                    <p style="color:var(--danger);font-size:.875rem;"><?= e($err) ?></p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <form method="POST" action="movie.php?id=<?= $id ?>#write-review">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="action" value="review_submit">
                    <div class="form-group">
                        <label class="form-label">Your Rating</label>
                        <div class="star-rating-input">
                            <?php for ($i=10; $i>=1; $i--): ?>
                            <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>"
                                   <?= ($userReview && (int)$userReview['rating'] === $i) || (!$userReview && $i === 0) ? 'checked' : '' ?>>
                            <label for="star<?= $i ?>" title="<?= $i ?>/10">★</label>
                            <?php endfor; ?>
                        </div>
                        <p class="form-hint">Rating: <span id="ratingValue"><?= $userReview ? (int)$userReview['rating'] : '—' ?></span> / 10</p>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="reviewText">Your Review <span style="color:var(--text-muted);font-weight:400">(optional)</span></label>
                        <textarea id="reviewText" name="review_text" class="form-control" rows="5" 
                                  placeholder="Share your thoughts about this film…"><?= e($userReview['review_text'] ?? '') ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="20 6 9 17 4 12"/></svg>
                        <?= $userReview ? 'Update Review' : 'Submit Review' ?>
                    </button>
                </form>
                <?php else: ?>
                <div style="text-align:center;padding:var(--s-8) 0;">
                    <p style="color:var(--text-secondary);margin-bottom:var(--s-4);">Sign in to rate and review this film.</p>
                    <a href="<?= BASE_URL ?>/login.php?redirect=<?= urlencode('movie.php?id='.$id.'#write-review') ?>" class="btn btn-primary">Log In to Review</a>
                </div>
                <?php endif; ?>
            </div>

            <!-- Reviews List -->
            <div id="reviews">
                <h2 style="font-size:1.125rem;margin-bottom:var(--s-5);font-family:var(--font-brand);">
                    Reviews <span id="reviewCount" style="color:var(--text-secondary);font-weight:400;font-family:var(--font-body)">(<?= count($reviews) ?>)</span>
                </h2>
                <?php if (empty($reviews)): ?>
                <div class="empty-state" style="padding: var(--s-10) 0;">
                    <div class="empty-icon">🎞️</div>
                    <h3>No reviews yet</h3>
                    <p>Be the first to share your thoughts!</p>
                </div>
                <?php else: ?>
                <div style="display:flex;flex-direction:column;gap:var(--s-4);">
                    <?php foreach ($reviews as $rev): ?>
                    <div class="review-card" id="review-<?= $rev['id'] ?>">
                        <div class="review-header">
                            <div class="review-avatar"><?= strtoupper(mb_substr($rev['username'],0,1)) ?></div>
                            <div>
                                <div class="review-username"><?= e($rev['username']) ?></div>
                                <div class="review-date"><?= timeAgo($rev['created_at']) ?></div>
                            </div>
                            <div class="review-rating" style="margin-left:auto">
                                <span class="rating-badge <?= ratingColor((float)$rev['rating']) ?>">
                                    ★ <?= number_format((float)$rev['rating'],1) ?>
                                </span>
                            </div>
                        </div>
                        <?php if ($rev['review_text']): ?>
                        <p class="review-text" id="reviewText-<?= $rev['id'] ?>"><?= e($rev['review_text']) ?></p>
                        <?php endif; ?>

                        <?php if ($user && ($user['id'] === (int)$rev['user_id'] || isAdmin())): ?>
                        <!-- Edit Form (hidden) -->
                        <form method="POST" id="editForm-<?= $rev['id'] ?>" class="hidden" style="margin-top:var(--s-4);">
                            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                            <input type="hidden" name="action" value="review_submit">
                            <?php
                            // Pre-select the star for editing
                            ?>
                            <div class="form-group">
                                <div class="star-rating-input">
                                    <?php for ($i=10; $i>=1; $i--): ?>
                                    <input type="radio" name="rating" id="editStar<?= $rev['id'] ?>_<?= $i ?>" value="<?= $i ?>"
                                           <?= (int)$rev['rating'] === $i ? 'checked' : '' ?>>
                                    <label for="editStar<?= $rev['id'] ?>_<?= $i ?>">★</label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <textarea name="review_text" class="form-control" rows="4"><?= e($rev['review_text'] ?? '') ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                        </form>
                        <div class="review-actions">
                            <?php if ($user['id'] === (int)$rev['user_id']): ?>
                            <button class="btn btn-ghost btn-sm" data-edit-review="<?= $rev['id'] ?>">Edit</button>
                            <?php endif; ?>
                            <button class="btn btn-danger btn-sm" data-delete-review="<?= $rev['id'] ?>">Delete</button>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- RIGHT SIDEBAR -->
        <div style="position:sticky;top:calc(var(--nav-h) + 24px);">
            <!-- Rating Distribution -->
            <?php if ($movie['review_count'] > 0): ?>
            <div class="card" style="margin-bottom:var(--s-5);">
                <h3 class="card-title">Rating Distribution</h3>
                <?php for ($score=10; $score>=1; $score--): ?>
                <?php $count = $dist[$score] ?? 0; $pct = $maxDist > 0 ? round($count/$maxDist*100) : 0; ?>
                <div class="dist-row">
                    <span class="dist-label"><?= $score ?></span>
                    <div class="dist-bar-wrap">
                        <div class="dist-bar" data-bar-width="<?= $pct ?>" style="width:0"></div>
                    </div>
                    <span class="dist-count"><?= $count ?></span>
                </div>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

            <!-- Film Details -->
            <div class="card">
                <h3 class="card-title">Film Details</h3>
                <?php $details = [
                    'Director'  => $movie['director'],
                    'Year'      => $movie['year'],
                    'Duration'  => $movie['duration'] ? floor($movie['duration']/60).'h '.$movie['duration']%60 .'m' : null,
                    'Genres'    => $movie['genres'],
                    'Favorites' => $movie['favorite_count'] . ' users',
                ]; ?>
                <dl style="display:flex;flex-direction:column;gap:var(--s-3);">
                <?php foreach ($details as $label => $value): ?>
                    <?php if ($value): ?>
                    <div style="display:flex;justify-content:space-between;gap:var(--s-3);font-size:.875rem;">
                        <dt style="color:var(--text-muted);flex-shrink:0"><?= $label ?></dt>
                        <dd style="color:var(--text-primary);text-align:right"><?= e((string)$value) ?></dd>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                </dl>
            </div>

            <?php if (isAdmin()): ?>
            <div style="margin-top:var(--s-4); display:flex; gap:var(--s-3);">
                <a href="<?= BASE_URL ?>/admin/movie-form.php?id=<?= $id ?>" class="btn btn-ghost btn-sm" style="flex:1;justify-content:center">Edit Film</a>
                <a href="<?= BASE_URL ?>/admin/movies.php?delete=<?= $id ?>" class="btn btn-danger btn-sm"
                   data-confirm="Delete this film and all its reviews?"
                   style="flex:1;justify-content:center">Delete</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
@media(max-width:768px){
    .container > div[style*="grid-template-columns"]{display:flex!important;flex-direction:column!important;}
    .movie-hero-content.container{padding:0 var(--s-5) var(--s-8)!important;}
}
</style>

<?php include __DIR__ . '/includes/footer.php'; ?>
