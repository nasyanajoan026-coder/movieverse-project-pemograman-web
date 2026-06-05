<?php
// ================================================
// Application Helper Functions
// ================================================

/**
 * Get all movies with stats (rating, review count)
 * Supports search, genre filter, and sorting
 */
function getMovies(PDO $pdo, array $opts = []): array {
    $search   = $opts['search']   ?? '';
    $genre    = $opts['genre']    ?? '';
    $sort     = $opts['sort']     ?? 'latest';
    $limit    = $opts['limit']    ?? 0;
    $offset   = $opts['offset']   ?? 0;

    $where = ['1=1'];
    $params = [];

    if ($search) {
        $where[] = '(m.title LIKE ? OR m.director LIKE ?)';
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }

    if ($genre) {
        $where[] = 'EXISTS (
            SELECT 1 FROM movie_genres mg2
            JOIN genres g2 ON mg2.genre_id = g2.id
            WHERE mg2.movie_id = m.id AND g2.slug = ?
        )';
        $params[] = $genre;
    }

    $orderBy = match($sort) {
        'rating'  => 'avg_rating DESC, review_count DESC',
        'reviews' => 'review_count DESC, avg_rating DESC',
        'title'   => 'm.title ASC',
        default   => 'm.created_at DESC',
    };

    $limitClause = $limit > 0 ? "LIMIT {$limit} OFFSET {$offset}" : '';
    $whereStr = implode(' AND ', $where);

    $sql = "
        SELECT 
            m.*,
            COUNT(DISTINCT r.id) AS review_count,
            COALESCE(ROUND(AVG(r.rating), 1), 0) AS avg_rating,
            COUNT(DISTINCT f.user_id) AS favorite_count,
            GROUP_CONCAT(DISTINCT g.name ORDER BY g.name SEPARATOR ', ') AS genres
        FROM movies m
        LEFT JOIN reviews r ON m.id = r.movie_id
        LEFT JOIN favorites f ON m.id = f.movie_id
        LEFT JOIN movie_genres mg ON m.id = mg.movie_id
        LEFT JOIN genres g ON mg.genre_id = g.id
        WHERE {$whereStr}
        GROUP BY m.id
        ORDER BY {$orderBy}
        {$limitClause}
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Count movies (for pagination)
 */
function countMovies(PDO $pdo, string $search = '', string $genre = ''): int {
    $where = ['1=1'];
    $params = [];
    if ($search) {
        $where[] = '(m.title LIKE ? OR m.director LIKE ?)';
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }
    if ($genre) {
        $where[] = 'EXISTS (SELECT 1 FROM movie_genres mg2 JOIN genres g2 ON mg2.genre_id = g2.id WHERE mg2.movie_id = m.id AND g2.slug = ?)';
        $params[] = $genre;
    }
    $whereStr = implode(' AND ', $where);
    $stmt = $pdo->prepare("SELECT COUNT(DISTINCT m.id) FROM movies m LEFT JOIN movie_genres mg ON m.id = mg.movie_id LEFT JOIN genres g ON mg.genre_id = g.id WHERE {$whereStr}");
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

/**
 * Get single movie with full details
 */
function getMovie(PDO $pdo, int $id): array|false {
    $stmt = $pdo->prepare("
        SELECT 
            m.*,
            COUNT(DISTINCT r.id) AS review_count,
            COALESCE(ROUND(AVG(r.rating), 1), 0) AS avg_rating,
            COUNT(DISTINCT f.user_id) AS favorite_count,
            GROUP_CONCAT(DISTINCT g.id ORDER BY g.id SEPARATOR ',') AS genre_ids,
            GROUP_CONCAT(DISTINCT g.name ORDER BY g.name SEPARATOR ', ') AS genres
        FROM movies m
        LEFT JOIN reviews r ON m.id = r.movie_id
        LEFT JOIN favorites f ON m.id = f.movie_id
        LEFT JOIN movie_genres mg ON m.id = mg.movie_id
        LEFT JOIN genres g ON mg.genre_id = g.id
        WHERE m.id = ?
        GROUP BY m.id
    ");
    $stmt->execute([$id]);
    return $stmt->fetch();
}

/**
 * Get reviews for a movie
 */
function getMovieReviews(PDO $pdo, int $movieId): array {
    $stmt = $pdo->prepare("
        SELECT r.*, u.username, u.avatar_url
        FROM reviews r
        JOIN users u ON r.user_id = u.id
        WHERE r.movie_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$movieId]);
    return $stmt->fetchAll();
}

/**
 * Get rating distribution for a movie
 */
function getRatingDistribution(PDO $pdo, int $movieId): array {
    $stmt = $pdo->prepare("
        SELECT FLOOR(rating) AS score, COUNT(*) AS count
        FROM reviews
        WHERE movie_id = ?
        GROUP BY FLOOR(rating)
        ORDER BY score DESC
    ");
    $stmt->execute([$movieId]);
    $rows = $stmt->fetchAll();
    $dist = array_fill(1, 10, 0);
    foreach ($rows as $row) {
        $dist[(int)$row['score']] = (int)$row['count'];
    }
    return $dist;
}

/**
 * Get all genres with movie count
 */
function getGenres(PDO $pdo, bool $withCount = false): array {
    if ($withCount) {
        $stmt = $pdo->query("
            SELECT g.*, COUNT(DISTINCT mg.movie_id) AS movie_count
            FROM genres g
            LEFT JOIN movie_genres mg ON g.id = mg.genre_id
            GROUP BY g.id
            ORDER BY movie_count DESC, g.name ASC
        ");
    } else {
        $stmt = $pdo->query("SELECT * FROM genres ORDER BY name ASC");
    }
    return $stmt->fetchAll();
}

/**
 * Get "Film of the Month" — highest avg rating this month (min 2 reviews)
 */
function getFilmOfMonth(PDO $pdo): array|false {
    $stmt = $pdo->prepare("
        SELECT 
            m.*,
            ROUND(AVG(r.rating), 1) AS avg_rating,
            COUNT(r.id) AS review_count,
            GROUP_CONCAT(DISTINCT g.name ORDER BY g.name SEPARATOR ', ') AS genres
        FROM movies m
        JOIN reviews r ON m.id = r.movie_id
        LEFT JOIN movie_genres mg ON m.id = mg.movie_id
        LEFT JOIN genres g ON mg.genre_id = g.id
        WHERE MONTH(r.created_at) = MONTH(CURDATE()) 
          AND YEAR(r.created_at) = YEAR(CURDATE())
        GROUP BY m.id
        HAVING review_count >= 1
        ORDER BY avg_rating DESC, review_count DESC
        LIMIT 1
    ");
    $stmt->execute();
    $result = $stmt->fetch();
    if (!$result) {
        // Fallback: all-time highest rated
        $stmt = $pdo->query("
            SELECT m.*, ROUND(AVG(r.rating), 1) AS avg_rating, COUNT(r.id) AS review_count,
            GROUP_CONCAT(DISTINCT g.name ORDER BY g.name SEPARATOR ', ') AS genres
            FROM movies m
            JOIN reviews r ON m.id = r.movie_id
            LEFT JOIN movie_genres mg ON m.id = mg.movie_id
            LEFT JOIN genres g ON mg.genre_id = g.id
            GROUP BY m.id ORDER BY avg_rating DESC LIMIT 1
        ");
        $result = $stmt->fetch();
    }
    return $result;
}

/**
 * Check if user has favorited a movie
 */
function isFavorited(PDO $pdo, int $userId, int $movieId): bool {
    $stmt = $pdo->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND movie_id = ?");
    $stmt->execute([$userId, $movieId]);
    return (bool)$stmt->fetchColumn();
}

/**
 * Get user's review for a movie
 */
function getUserReview(PDO $pdo, int $userId, int $movieId): array|false {
    $stmt = $pdo->prepare("SELECT * FROM reviews WHERE user_id = ? AND movie_id = ?");
    $stmt->execute([$userId, $movieId]);
    return $stmt->fetch();
}

/**
 * Get user's profile data
 */
function getUserProfile(PDO $pdo, int $userId): array|false {
    $stmt = $pdo->prepare("
        SELECT u.*,
            COUNT(DISTINCT r.id) AS review_count,
            COALESCE(ROUND(AVG(r.rating), 1), 0) AS avg_rating_given,
            COUNT(DISTINCT f.movie_id) AS favorite_count
        FROM users u
        LEFT JOIN reviews r ON u.id = r.user_id
        LEFT JOIN favorites f ON u.id = f.user_id
        WHERE u.id = ?
        GROUP BY u.id
    ");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

/**
 * Get user's reviews with movie data
 */
function getUserReviews(PDO $pdo, int $userId): array {
    $stmt = $pdo->prepare("
        SELECT r.*, m.title, m.poster_url, m.year
        FROM reviews r
        JOIN movies m ON r.movie_id = m.id
        WHERE r.user_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Get user's favorite movies
 */
function getUserFavorites(PDO $pdo, int $userId): array {
    $stmt = $pdo->prepare("
        SELECT m.*, f.created_at AS favorited_at,
            COUNT(DISTINCT r.id) AS review_count,
            COALESCE(ROUND(AVG(r.rating), 1), 0) AS avg_rating
        FROM favorites f
        JOIN movies m ON f.movie_id = m.id
        LEFT JOIN reviews r ON m.id = r.movie_id
        WHERE f.user_id = ?
        GROUP BY m.id, f.created_at
        ORDER BY f.created_at DESC
    ");
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

/**
 * Get platform-wide statistics
 */
function getPlatformStats(PDO $pdo): array {
    $stats = [];
    $stats['users']   = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='member'")->fetchColumn();
    $stats['movies']  = (int)$pdo->query("SELECT COUNT(*) FROM movies")->fetchColumn();
    $stats['reviews'] = (int)$pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
    $stats['genres']  = (int)$pdo->query("SELECT COUNT(*) FROM genres")->fetchColumn();
    return $stats;
}

/**
 * Render star display (out of 10)
 */
function renderStars(float $rating): string {
    $full = floor($rating / 2);
    $half = ($rating / 2 - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;
    $html = '';
    for ($i = 0; $i < $full; $i++)  $html .= '<span class="star full">★</span>';
    if ($half)                        $html .= '<span class="star half">★</span>';
    for ($i = 0; $i < $empty; $i++) $html .= '<span class="star empty">★</span>';
    return $html;
}

/**
 * Get rating color class based on score
 */


function ratingColor(?float $rating): string {
    if ($rating === null) return 'rating-low';
    if ($rating >= 8) return 'rating-gold';
    if ($rating >= 6) return 'rating-good';
    if ($rating >= 4) return 'rating-mid';
    return 'rating-low';
}

/**
 * Truncate text
 */
function truncate(?string $text, int $length = 150): string {
    $text = $text ?? '';
    if (strlen($text) <= $length) return $text;
    return rtrim(substr($text, 0, $length)) . '…';
}

/**
 * Time ago helper
 */
function timeAgo(string $datetime): string {
    $time = time() - strtotime($datetime);
    if ($time < 60)     return 'just now';
    if ($time < 3600)   return floor($time/60) . 'm ago';
    if ($time < 86400)  return floor($time/3600) . 'h ago';
    if ($time < 604800) return floor($time/86400) . 'd ago';
    return date('M j, Y', strtotime($datetime));
}

/**
 * Poster fallback: returns image tag or placeholder div
 */
function posterImg(?string $url, string $title, string $classes = ''): string {
    if ($url) {
        return '<img src="' . e($url) . '" alt="' . e($title) . '" class="' . $classes . '" loading="lazy" onerror="this.parentElement.innerHTML=\'<div class=poster-ph>' . e(mb_substr($title, 0, 1)) . '</div>\'">';
    }
    return '<div class="poster-ph">' . e(mb_substr($title, 0, 1)) . '</div>';
}
