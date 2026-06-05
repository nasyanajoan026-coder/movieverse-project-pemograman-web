<?php
// includes/header.php — include at top of every page
// Expects $pageTitle to be set before including
if (session_status() === PHP_SESSION_NONE) session_start();
$flash = getFlash();
$__user = currentUser();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? e($pageTitle) . ' — ' : '' ?>Movieverse</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Bebas+Neue&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300;1,9..40,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <!-- Logo -->
        <a href="<?= BASE_URL ?>/index.php" class="nav-logo">
            <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="logo-icon">
                <circle cx="16" cy="16" r="14" stroke="currentColor" stroke-width="2"/>
                <path d="M10 10L22 16L10 22V10Z" fill="currentColor"/>
                <path d="M4 12h4M4 16h4M4 20h4M24 12h4M24 16h4M24 20h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            </svg>
            <span>Movieverse</span>
        </a>

        <!-- Desktop Nav Links -->
        <ul class="nav-links">
            <li><a href="<?= BASE_URL ?>/index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">Home</a></li>
            <li><a href="<?= BASE_URL ?>/movies.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'movies.php' ? 'active' : '' ?>">Movies</a></li>
            <li><a href="<?= BASE_URL ?>/stats.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'stats.php' ? 'active' : '' ?>">Stats</a></li>
            <li><a href="<?= BASE_URL ?>/about.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'about.php' ? 'active' : '' ?>">About</a></li>
        </ul>

        <!-- Nav Right -->
        <div class="nav-right">
            <!-- Search Button -->
            <button class="nav-search-btn" id="searchToggle" aria-label="Search">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>

            <?php if (isLoggedIn()): ?>
            <!-- User Dropdown -->
            <div class="nav-user" id="userDropdown">
                <button class="user-btn" id="userToggle">
                    <div class="user-avatar-sm">
                        <?= strtoupper(mb_substr($__user['username'], 0, 1)) ?>
                    </div>
                    <span class="user-name-sm"><?= e($__user['username']) ?></span>
                    <svg class="chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="user-dropdown" id="userMenu">
                    <a href="<?= BASE_URL ?>/profile.php" class="dropdown-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        My Profile
                    </a>
                    <a href="<?= BASE_URL ?>/favorites.php" class="dropdown-item">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                        Favorites
                    </a>
                    <?php if (isAdmin()): ?>
                    <div class="dropdown-divider"></div>
                    <a href="<?= BASE_URL ?>/admin/index.php" class="dropdown-item admin-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        Admin Panel
                    </a>
                    <?php endif; ?>
                    <div class="dropdown-divider"></div>
                    <a href="<?= BASE_URL ?>/logout.php" class="dropdown-item logout-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Logout
                    </a>
                </div>
            </div>
            <?php else: ?>
            <a href="<?= BASE_URL ?>/login.php" class="btn btn-ghost btn-sm">Log In</a>
            <a href="<?= BASE_URL ?>/register.php" class="btn btn-primary btn-sm">Sign Up</a>
            <?php endif; ?>

            <!-- Hamburger (mobile) -->
            <button class="hamburger" id="hamburger" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>

    <!-- Mobile Search Bar -->
    <div class="nav-search-bar" id="searchBar">
        <form action="<?= BASE_URL ?>/movies.php" method="GET" class="search-form">
            <input type="search" name="q" placeholder="Search films, directors…" 
                   value="<?= isset($_GET['q']) ? e($_GET['q']) : '' ?>" autofocus>
            <button type="submit" class="search-submit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>
        </form>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <a href="<?= BASE_URL ?>/index.php" class="mobile-link">Home</a>
        <a href="<?= BASE_URL ?>/movies.php" class="mobile-link">Movies</a>
        <a href="<?= BASE_URL ?>/stats.php" class="mobile-link">Stats</a>
        <a href="<?= BASE_URL ?>/about.php" class="mobile-link">About</a>
        <?php if (isLoggedIn()): ?>
        <div class="mobile-divider"></div>
        <a href="<?= BASE_URL ?>/profile.php" class="mobile-link">My Profile</a>
        <a href="<?= BASE_URL ?>/favorites.php" class="mobile-link">Favorites</a>
        <?php if (isAdmin()): ?>
        <a href="<?= BASE_URL ?>/admin/index.php" class="mobile-link">Admin Panel</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/logout.php" class="mobile-link">Logout</a>
        <?php else: ?>
        <div class="mobile-divider"></div>
        <a href="<?= BASE_URL ?>/login.php" class="mobile-link">Log In</a>
        <a href="<?= BASE_URL ?>/register.php" class="mobile-link mobile-signup">Sign Up</a>
        <?php endif; ?>
    </div>
</nav>

<!-- FLASH MESSAGES -->
<?php if ($flash): ?>
<div class="flash flash-<?= e($flash['type']) ?>" id="flashMsg">
    <span><?= e($flash['message']) ?></span>
    <button onclick="this.parentElement.remove()" class="flash-close">✕</button>
</div>
<?php endif; ?>

<main class="main-content">
