<?php // includes/footer.php ?>
</main><!-- end .main-content -->

<footer class="footer">
    <div class="footer-container">
        <div class="footer-brand">
            <a href="<?= BASE_URL ?>/index.php" class="nav-logo">
                <svg viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" class="logo-icon">
                    <circle cx="16" cy="16" r="14" stroke="currentColor" stroke-width="2"/>
                    <path d="M10 10L22 16L10 22V10Z" fill="currentColor"/>
                    <path d="M4 12h4M4 16h4M4 20h4M24 12h4M24 16h4M24 20h4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
                <span>Movieverse</span>
            </a>
            <p class="footer-tagline">Your community for honest film reviews.</p>
        </div>

        <div class="footer-links">
            <div class="footer-col">
                <h4>Explore</h4>
                <a href="<?= BASE_URL ?>/movies.php">All Movies</a>
                <a href="<?= BASE_URL ?>/movies.php?sort=rating">Top Rated</a>
                <a href="<?= BASE_URL ?>/movies.php?sort=reviews">Most Reviewed</a>
                <a href="<?= BASE_URL ?>/stats.php">Statistics</a>
            </div>
            <div class="footer-col">
                <h4>Account</h4>
                <?php if (isLoggedIn()): ?>
                <a href="<?= BASE_URL ?>/profile.php">My Profile</a>
                <a href="<?= BASE_URL ?>/favorites.php">Favorites</a>
                <a href="<?= BASE_URL ?>/logout.php">Logout</a>
                <?php else: ?>
                <a href="<?= BASE_URL ?>/login.php">Log In</a>
                <a href="<?= BASE_URL ?>/register.php">Sign Up</a>
                <?php endif; ?>
            </div>
            <div class="footer-col">
                <h4>Info</h4>
                <a href="<?= BASE_URL ?>/about.php">About Us</a>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© <?= date('Y') ?> Movieverse — Built with PHP &amp; MySQL. No frameworks, pure craft.</p>
    </div>
</footer>

<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>
