<?php
// includes/movie-card.php
// Expects: $movie array with id, title, year, poster_url, avg_rating, review_count, genres
// No standalone variables needed — just include this file inside a foreach
?>
<div class="movie-card">
    <a href="<?= BASE_URL ?>/movie.php?id=<?= (int)$movie['id'] ?>">
        <div class="movie-poster">
            <?= posterImg($movie['poster_url'] ?? '', $movie['title'], 'poster-img') ?>
            <div class="movie-overlay">
                <span class="btn btn-primary btn-sm">View Details</span>
            </div>
        </div>
        <div class="movie-info">
            <div class="movie-title" title="<?= e($movie['title']) ?>"><?= e($movie['title']) ?></div>
            <div class="movie-meta">
                <span><?= e((string)$movie['year']) ?></span>
                <?php if ($movie['avg_rating'] > 0): ?>
                <span class="rating-badge <?= ratingColor((float)$movie['avg_rating']) ?>">
                    ★ <?= number_format((float)$movie['avg_rating'], 1) ?>
                </span>
                <?php else: ?>
                <span style="font-size:.75rem;color:var(--text-muted)">No rating</span>
                <?php endif; ?>
            </div>
        </div>
    </a>
</div>
