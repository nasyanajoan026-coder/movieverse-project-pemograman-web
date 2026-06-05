<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';

requireAdmin();

$errors = [];
$movie  = null;
$isEdit = false;

// Load existing movie for editing
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ?");
    $stmt->execute([$id]);
    $movie = $stmt->fetch();
    if (!$movie) { setFlash('error', 'Film not found.'); redirect('/admin/movies.php'); }
    $isEdit = true;
    // Load current genres
    $gs = $pdo->prepare("SELECT genre_id FROM movie_genres WHERE movie_id = ?");
    $gs->execute([$id]);
    $movie['genre_ids'] = $gs->fetchAll(PDO::FETCH_COLUMN);
}

// All genres for checkbox list
$allGenres = getGenres($pdo);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();

    $title       = trim($_POST['title'] ?? '');
    $year        = (int)($_POST['year'] ?? 0);
    $director    = trim($_POST['director'] ?? '');
    $duration    = (int)($_POST['duration_min'] ?? $_POST['duration'] ?? 0);
    $synopsis    = trim($_POST['synopsis'] ?? '');
    $poster_url  = trim($_POST['poster_url'] ?? '');
    $trailer_url = trim($_POST['trailer_url'] ?? '');
    $genreIds    = array_map('intval', $_POST['genre_ids'] ?? []);

    // Validation
    if (strlen($title) < 1)                    $errors[] = 'Title is required.';
    if ($year < 1888 || $year > date('Y') + 2) $errors[] = 'Please enter a valid year.';
    if ($synopsis === '')                        $errors[] = 'Synopsis is required.';
    if (empty($genreIds))                        $errors[] = 'Please select at least one genre.';

    if (empty($errors)) {
        if ($isEdit) {
            $stmt = $pdo->prepare("
                UPDATE movies SET title=?, year=?, director=?, duration=?, synopsis=?,
                                  poster_url=?, trailer_url=?
                WHERE id=?
            ");
            $stmt->execute([$title, $year, $director ?: null, $duration ?: null, $synopsis,
                            $poster_url ?: null, $trailer_url ?: null, $id]);
            $movieId = $id;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO movies (title, year, director, duration, synopsis, poster_url, trailer_url)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$title, $year, $director ?: null, $duration ?: null, $synopsis,
                            $poster_url ?: null, $trailer_url ?: null]);
            $movieId = (int)$pdo->lastInsertId();
        }

        // Update genres
        $pdo->prepare("DELETE FROM movie_genres WHERE movie_id = ?")->execute([$movieId]);
        $mgStmt = $pdo->prepare("INSERT INTO movie_genres (movie_id, genre_id) VALUES (?, ?)");
        foreach ($genreIds as $gid) { $mgStmt->execute([$movieId, $gid]); }

        setFlash('success', $isEdit ? 'Film updated successfully.' : 'Film added successfully.');
        redirect('/admin/movies.php');
    }

    // Re-populate for re-display
    $movie = compact('title','year','director','duration','synopsis','poster_url','trailer_url');
    $movie['genre_ids'] = $genreIds;
}

include __DIR__ . '/../includes/header.php';
?>

<main class="page-main admin-page">
  <div class="admin-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="admin-content">
      <div class="admin-top">
        <h1 class="admin-page-title"><?= $isEdit ? 'Edit Film' : 'Add New Film' ?></h1>
        <div style="display:flex;gap:var(--s-3);">
          <?php if (!$isEdit): ?>
            <button type="button" class="btn btn-outline-gold btn-sm" onclick="autofillDemo()">⚡ Demo Autofill</button>
          <?php endif; ?>
          <a href="<?= BASE_URL ?>/admin/movies.php" class="btn btn-ghost">← Back to Films</a>
        </div>
      </div>

      <?php if ($errors): ?>
        <div class="flash error">
          <ul><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
        </div>
      <?php endif; ?>

      <div class="admin-form-layout">

        <!-- Form -->
        <form method="POST" class="admin-form">
          <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">

          <div class="form-group">
            <label for="title">Title <span class="req">*</span></label>
            <input type="text" id="title" name="title" required
              value="<?= e($movie['title'] ?? '') ?>" placeholder="e.g. Inception">
          </div>

          <div class="form-row-2">
            <div class="form-group">
              <label for="year">Year <span class="req">*</span></label>
              <input type="number" id="year" name="year" required
                min="1888" max="<?= date('Y') + 2 ?>"
                value="<?= e($movie['year'] ?? date('Y')) ?>">
            </div>
            <div class="form-group">
              <label for="duration">Duration (min)</label>
              <input type="number" id="duration" name="duration_min" min="1"
                value="<?= e($movie['duration_min'] ?? $movie['duration'] ?? '') ?>"
                placeholder="e.g. 148">
            </div>
          </div>

          <div class="form-group">
            <label for="director">Director</label>
            <input type="text" id="director" name="director"
              value="<?= e($movie['director'] ?? '') ?>" placeholder="e.g. Christopher Nolan">
          </div>

          <div class="form-group">
            <label for="synopsis">Synopsis <span class="req">*</span></label>
            <textarea id="synopsis" name="synopsis" rows="5" required
              placeholder="Brief description of the film…"><?= e($movie['synopsis'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label for="poster_url">Poster Image URL</label>
            <input type="url" id="poster_url" name="poster_url"
              value="<?= e($movie['poster_url'] ?? '') ?>"
              placeholder="https://image.tmdb.org/…"
              oninput="previewPoster(this.value)">
            <small>Paste a TMDB or direct image URL</small>
          </div>

          <div class="form-group">
            <label for="trailer_url">Trailer URL</label>
            <input type="url" id="trailer_url" name="trailer_url"
              value="<?= e($movie['trailer_url'] ?? '') ?>"
              placeholder="https://www.youtube.com/watch?v=…">
          </div>

          <!-- Genres -->
          <div class="form-group">
            <label>Genres <span class="req">*</span></label>
            <div class="genre-checkbox-grid">
              <?php foreach ($allGenres as $g): ?>
                <?php $checked = in_array($g['id'], $movie['genre_ids'] ?? []); ?>
                <label class="genre-check-label <?= $checked ? 'checked' : '' ?>">
                  <input type="checkbox" name="genre_ids[]" value="<?= $g['id'] ?>"
                    <?= $checked ? 'checked' : '' ?>
                    onchange="this.closest('label').classList.toggle('checked', this.checked)">
                  <?= e($g['name']) ?>
                </label>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary">
              <?= $isEdit ? 'Save Changes' : 'Add Film' ?>
            </button>
            <a href="<?= BASE_URL ?>/admin/movies.php" class="btn btn-ghost">Cancel</a>
          </div>
        </form>

        <!-- Poster Preview -->
        <div class="form-sidebar">
          <div class="poster-preview-wrap">
            <p class="form-sidebar-label">Poster Preview</p>
            <div id="poster-preview-box" class="poster-preview-box">
              <?php if (!empty($movie['poster_url'])): ?>
                <img id="poster-preview-img" src="<?= e($movie['poster_url']) ?>" alt="Poster preview">
              <?php else: ?>
                <div id="poster-preview-placeholder" class="poster-placeholder">
                  <span>No Image</span>
                </div>
                <img id="poster-preview-img" src="" alt="Poster preview" style="display:none">
              <?php endif; ?>
            </div>
          </div>

          <?php if ($isEdit): ?>
          <div class="form-sidebar-links">
            <a href="<?= BASE_URL ?>/movie.php?id=<?= $id ?>" target="_blank" class="btn btn-ghost btn-sm">View Film Page</a>
          </div>
          <?php endif; ?>
        </div>

      </div><!-- /.admin-form-layout -->
    </div><!-- /.admin-content -->
  </div>
</main>

<script>
function previewPoster(url) {
    const img   = document.getElementById('poster-preview-img');
    const ph    = document.getElementById('poster-preview-placeholder');
    if (url) {
        img.src = url;
        img.style.display = 'block';
        if (ph) ph.style.display = 'none';
        img.onerror = () => { img.style.display = 'none'; if (ph) ph.style.display = 'flex'; };
    } else {
        img.style.display = 'none';
        if (ph) ph.style.display = 'flex';
    }
}

function autofillDemo() {
    document.getElementById('title').value = 'Avatar: The Way of Water';
    document.getElementById('year').value = '2022';
    document.getElementById('duration').value = '192';
    document.getElementById('director').value = 'James Cameron';
    document.getElementById('synopsis').value = 'Jake Sully lives with his newfound family formed on the extrasolar moon Pandora. Once a familiar threat returns to finish what was previously started, Jake must work with Neytiri and the army of the Na\'vi race to protect their home.';
    document.getElementById('poster_url').value = 'https://image.tmdb.org/t/p/w500/t6HI54nsjTX5WagEhTMXW7qyA8B.jpg';
    document.getElementById('trailer_url').value = 'https://www.youtube.com/watch?v=d9MyW72ELq0';
    
    // Trigger preview
    previewPoster('https://image.tmdb.org/t/p/w500/t6HI54nsjTX5WagEhTMXW7qyA8B.jpg');
    
    // Select Action, Sci-Fi, Adventure genres
    const checkboxes = document.querySelectorAll('.genre-checkbox-grid input[type="checkbox"]');
    checkboxes.forEach(cb => {
        if (cb.value == '1' || cb.value == '5' || cb.value == '12') {
            cb.checked = true;
            cb.closest('label').classList.add('checked');
        }
    });
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
