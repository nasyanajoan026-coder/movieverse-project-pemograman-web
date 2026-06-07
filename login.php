<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/helpers.php';

if (isLoggedIn()) { redirect('/index.php'); }

$pageTitle = 'Log In';
$errors = [];
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username) $errors[] = 'Username is required.';
    if (!$password) $errors[] = 'Password is required.';

    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            loginUser($user);
            setFlash('success', 'Welcome back, ' . $user['username'] . '!');
            $redirect = $_GET['redirect'] ?? 'index.php';
            header('Location: ' . BASE_URL . '/' . $redirect);
            exit;
        } else {
            $errors[] = 'Invalid username/email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Log In — Movieverse</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Bebas+Neue&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-card animate-in">
        <!-- Logo -->
        <div class="auth-logo">
            <a href="<?= BASE_URL ?>/index.php" class="nav-logo" style="justify-content:center;font-size:1.5rem;">
                <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" style="width:32px;height:32px;color:var(--accent)">
                    <circle cx="16" cy="16" r="14" stroke="currentColor" stroke-width="2"/>
                    <path d="M10 10L22 16L10 22V10Z" fill="currentColor"/>
                    <path d="M4 12h4M4 16h4M4 20h4M24 12h4M24 16h4M24 20h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Movieverse
            </a>
        </div>

        <h1 class="auth-title">Welcome back</h1>
        <p class="auth-subtitle">Sign in to your account</p>

        <?php if (!empty($errors)): ?>
        <div style="background:var(--danger-bg);border:1px solid rgba(229,53,53,.3);border-radius:var(--r-md);padding:var(--s-4);margin-bottom:var(--s-5);">
            <?php foreach ($errors as $e): ?><p style="color:var(--danger);font-size:.875rem;"><?= e($e) ?></p><?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

            <div class="form-group">
                <label class="form-label" for="username">Username or Email</label>
                <div class="input-icon-wrap">
                    <span class="input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                    <input type="text" id="username" name="username" class="form-control" value="<?= e($username) ?>" placeholder="your_username" required autofocus>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-icon-wrap">
                    <span class="input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.75rem;">
                Log In
            </button>
        </form>

        <p class="auth-footer">Don't have an account? <a href="<?= BASE_URL ?>/register.php">Sign up free</a></p>
    </div>
</div>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>
