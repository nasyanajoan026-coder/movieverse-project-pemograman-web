<?php
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/helpers.php';

requireAdmin();

$errors = [];
$editGenre = null;

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $action = $_POST['action'] ?? '';

    // Add
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        if ($name === '') {
            $errors[] = 'Genre name is required.';
        } else {
            $exists = $pdo->prepare("SELECT id FROM genres WHERE name = ?");
            $exists->execute([$name]);
            if ($exists->fetch()) {
                $errors[] = 'A genre with that name already exists.';
            } else {
                $pdo->prepare("INSERT INTO genres (name) VALUES (?)")->execute([$name]);
                setFlash('success', "Genre \"$name\" added.");
                redirect('/admin/genres.php');
            }
        }
    }

    // Update
    if ($action === 'update') {
        $gid  = (int)($_POST['genre_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        if ($name === '' || $gid <= 0) {
            $errors[] = 'Name is required.';
        } else {
            $pdo->prepare("UPDATE genres SET name = ? WHERE id = ?")->execute([$name, $gid]);
            setFlash('success', 'Genre updated.');
            redirect('/admin/genres.php');
        }
    }

    // Delete
    if ($action === 'delete') {
        $gid = (int)($_POST['genre_id'] ?? 0);
        if ($gid > 0) {
            // Remove genre associations
            $pdo->prepare("DELETE FROM movie_genres WHERE genre_id = ?")->execute([$gid]);
            $pdo->prepare("DELETE FROM genres WHERE id = ?")->execute([$gid]);
            setFlash('success', 'Genre deleted.');
            redirect('/admin/genres.php');
        }
    }
}

// Load genre for editing
$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
if ($editId > 0) {
    $s = $pdo->prepare("SELECT * FROM genres WHERE id = ?");
    $s->execute([$editId]);
    $editGenre = $s->fetch();
}

// All genres with movie count
$genres = $pdo->query("
    SELECT g.id, g.name, COUNT(DISTINCT mg.movie_id) AS movie_count
    FROM genres g
    LEFT JOIN movie_genres mg ON g.id = mg.genre_id
    GROUP BY g.id
    ORDER BY g.name ASC
")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<main class="page-main admin-page">
  <div class="admin-layout">
    <?php include __DIR__ . '/sidebar.php'; ?>

    <div class="admin-content">
      <div class="admin-top">
        <h1 class="admin-page-title">Genres</h1>
      </div>

      <?php if ($errors): ?>
        <div class="flash error">
          <?php foreach ($errors as $err): ?><p><?= e($err) ?></p><?php endforeach; ?>
        </div>
      <?php endif; ?>

      <div class="genres-layout">

        <!-- Genre list -->
        <section class="admin-section genres-list-section">
          <h2><?= count($genres) ?> Genre<?= count($genres) !== 1 ? 's' : '' ?></h2>
          <div class="admin-table-wrap">
            <table class="admin-table">
              <thead>
                <tr><th>Name</th><th>Films</th><th>Actions</th></tr>
              </thead>
              <tbody>
                <?php if (empty($genres)): ?>
                  <tr><td colspan="3" class="table-empty">No genres yet.</td></tr>
                <?php else: ?>
                  <?php foreach ($genres as $g): ?>
                  <tr class="<?= $editGenre && $editGenre['id'] === $g['id'] ? 'row-editing' : '' ?>">
                    <td><?= e($g['name']) ?></td>
                    <td><?= $g['movie_count'] ?></td>
                    <td class="action-cell">
                      <a href="<?= BASE_URL ?>/admin/genres.php?edit=<?= $g['id'] ?>" class="btn btn-ghost btn-sm">Edit</a>
                      <?php if ($g['movie_count'] == 0): ?>
                      <form method="POST" class="inline-form">
                        <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="genre_id" value="<?= $g['id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm"
                          onclick="return confirm('Delete genre \'<?= addslashes(e($g['name'])) ?>\'?')">
                          Delete
                        </button>
                      </form>
                      <?php else: ?>
                        <span class="text-muted btn-sm">In use</span>
                      <?php endif; ?>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Add / Edit form -->
        <aside class="genres-form-aside">
          <?php if ($editGenre): ?>
          <section class="admin-card">
            <h2>Edit Genre</h2>
            <form method="POST" class="admin-form compact">
              <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="genre_id" value="<?= $editGenre['id'] ?>">
              <div class="form-group">
                <label for="edit_name">Name</label>
                <input type="text" id="edit_name" name="name" required
                  value="<?= e($editGenre['name']) ?>">
              </div>
              <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save</button>
                 <a href="<?= BASE_URL ?>/admin/genres.php" class="btn btn-ghost">Cancel</a>
              </div>
            </form>
          </section>
          <?php endif; ?>

          <section class="admin-card">
            <h2>Add Genre</h2>
            <form method="POST" class="admin-form compact">
              <input type="hidden" name="csrf_token" value="<?= csrfToken() ?>">
              <input type="hidden" name="action" value="add">
              <div class="form-group">
                <label for="genre_name">Name</label>
                <input type="text" id="genre_name" name="name" required
                  placeholder="e.g. Thriller">
              </div>
              <button type="submit" class="btn btn-primary">Add Genre</button>
            </form>
          </section>
        </aside>

      </div><!-- /.genres-layout -->
    </div>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
