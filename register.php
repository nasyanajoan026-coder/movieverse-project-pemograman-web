<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/helpers.php';

if (isLoggedIn()) { redirect('/index.php'); }

$pageTitle = 'Sign Up';
$errors = [];
$data = ['username' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $data['username'] = trim($_POST['username'] ?? '');
    $data['email']    = trim($_POST['email']    ?? '');
    $password         = $_POST['password']  ?? '';
    $password2        = $_POST['password2'] ?? '';

    if (strlen($data['username']) < 3 || strlen($data['username']) > 50)
        $errors[] = 'Username must be 3–50 characters.';
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username']))
        $errors[] = 'Username may only contain letters, numbers, and underscores.';
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
        $errors[] = 'Please enter a valid email address.';
    if (strlen($password) < 8)
        $errors[] = 'Password must be at least 8 characters.';
    if ($password !== $password2)
        $errors[] = 'Passwords do not match.';

    if (empty($errors)) {
        // Check uniqueness
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$data['username'], $data['email']]);
        if ($stmt->fetch()) {
            $errors[] = 'That username or email is already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?,?,?)");
            $stmt->execute([$data['username'], $data['email'], $hash]);
            $userId = (int)$pdo->lastInsertId();
            $newUser = ['id' => $userId, 'username' => $data['username'], 'email' => $data['email'], 'role' => 'member'];
            loginUser($newUser);
            setFlash('success', 'Welcome to Movieverse, ' . $data['username'] . '!');
            redirect('/index.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign Up — Movieverse</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700&family=Bebas+Neue&family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-card animate-in">
        <div class="auth-logo">
            <a href="<?= BASE_URL ?>/index.php" class="nav-logo" style="justify-content:center;font-size:1.5rem;">
                <svg viewBox="0 0 32 32" fill="none" style="width:32px;height:32px;color:var(--accent)">
                    <circle cx="16" cy="16" r="14" stroke="currentColor" stroke-width="2"/>
                    <path d="M10 10L22 16L10 22V10Z" fill="currentColor"/>
                    <path d="M4 12h4M4 16h4M4 20h4M24 12h4M24 16h4M24 20h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                Movieverse
            </a>
        </div>

        <h1 class="auth-title">Join Movieverse</h1>
        <p class="auth-subtitle">Create your free account</p>

        <?php if (!empty($errors)): ?>
        <div style="background:var(--danger-bg);border:1px solid rgba(229,53,53,.3);border-radius:var(--r-md);padding:var(--s-4);margin-bottom:var(--s-5);">
            <?php foreach ($errors as $err): ?><p style="color:var(--danger);font-size:.875rem;"><?= e($err) ?></p><?php endforeach; ?>
        </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <div class="input-icon-wrap">
                    <span class="input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
                    <input type="text" id="username" name="username" class="form-control" value="<?= e($data['username']) ?>" placeholder="your_username" required autofocus>
                </div>
                <p class="form-hint">Letters, numbers, underscores only.</p>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email</label>
                <div class="input-icon-wrap">
                    <span class="input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
                    <input type="email" id="email" name="email" class="form-control" value="<?= e($data['email']) ?>" placeholder="you@example.com" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-icon-wrap">
                    <span class="input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                    <input type="password" id="password" name="password" class="form-control" placeholder="Min. 8 characters" required minlength="8">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password2">Confirm Password</label>
                <div class="input-icon-wrap">
                    <span class="input-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
                    <input type="password" id="password2" name="password2" class="form-control" placeholder="Repeat password" required minlength="8">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:.75rem;">
                Create Account
            </button>
        </form>

        <p class="auth-footer">Already have an account? <a href="<?= BASE_URL ?>/login.php">Log in</a></p>
    </div>
</div>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>
