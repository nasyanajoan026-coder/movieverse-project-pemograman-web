<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/auth.php';
require_once __DIR__ . '/includes/helpers.php';

// --- Team data (edit to match your actual group) ---
$team = [
    [
        'name'    => 'Claudia J. Bella',
        'role'    => 'Frontend Developer & UI/UX Designer',
        'nim'     => '240211060083',
        'photo'   => BASE_URL . '/assets/img/clau.jpeg',   // leave blank for avatar fallback
        'bio'     => 'Responsible for frontend architecture, CSS components, and responsive layouts.',
        'initials'=> 'CB',
    ],
    [
        'name'    => 'Nasya G. Najoan',
        'role'    => 'Backend Developer & UI/UX Designer',
        'nim'     => '240211060099',
        'photo'   => BASE_URL . '/assets/img/nasya.jpeg',
        'bio'     => 'Crafted the UI/UX design system, Database Handler, and server configuration. ',
        'initials'=> 'NN',
    ],
    
    [
        'name'    => 'Isabel S. C. D. Indo',
        'role'    => 'Backend Developer & Data Entry',
        'nim'     => '240211060005',
        'photo'   => BASE_URL . '/assets/img/isabel.jpeg',
        'bio'     => 'Built the statistics module, handled seed data, and assisted with deployment.',
        'initials'=> 'II',
    ],
];

$tech = [
    ['icon' => '🐘', 'name' => 'PHP 8',       'desc' => 'Server-side scripting'],
    ['icon' => '🗄',  'name' => 'MySQL',        'desc' => 'Relational database'],
    ['icon' => '🎨', 'name' => 'CSS3',          'desc' => 'Custom design system'],
    ['icon' => '⚡', 'name' => 'Vanilla JS',    'desc' => 'Interactive UI'],
    ['icon' => '🔒', 'name' => 'PDO & bcrypt',  'desc' => 'Secure data access'],
    ['icon' => '☁', 'name' => 'Live Deploy',    'desc' => 'Hosted on a public server'],
];

$pageTitle = 'About';
require_once __DIR__ . '/includes/header.php';
?>

<main class="page-main">

  <!-- Hero -->
  <section class="about-hero">
    <div class="container about-hero-inner">
      <div class="about-hero-text">
        <span class="about-eyebrow">About Us</span>
        <h1 class="about-title">Built by film lovers,<br>for film lovers.</h1>
        <p class="about-lead">
          Movieverse is a student project built for the Web Programming course. 
          It is a full-stack film review platform where users can discover movies, 
          share their opinions, and track their favorites — all without a single 
          framework or external library.
        </p>
      </div>
      <div class="about-hero-badge">
        <div class="badge-circle">
          <span class="badge-icon">🎬</span>
          <span class="badge-text">Movieverse</span>
          <span class="badge-sub">Web Programming Project</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Project Description -->
  <section class="about-section">
    <div class="container">
      <div class="about-two-col">
        <div>
          <h2 class="section-title">The Project</h2>
          <p>
            Movieverse was created as the final project for the <strong>Web Programming</strong> course. 
            The goal was to build a multi-user, dynamic web application using only core web technologies — 
            HTML, CSS, JavaScript, PHP, and MySQL — without relying on any external libraries or frameworks.
          </p>
          <p>
            Inspired by platforms like <em>Letterboxd</em> and <em>Netflix</em>, we wanted to build 
            something that felt polished and functional: a real product, not just an assignment.
          </p>
          <p>
            The platform supports user registration and login, movie browsing with search/filter, 
            writing and managing reviews, a personal favorites list, platform-wide statistics, 
            and a full admin dashboard for content management.
          </p>
        </div>
        <div class="about-highlights">
          <div class="highlight-item">
            <span class="hi-icon">🚀</span>
            <div>
              <strong>Zero Frameworks</strong>
              <p>No jQuery, Bootstrap, CodeIgniter, or any library. 100% hand-written code.</p>
            </div>
          </div>
          <div class="highlight-item">
            <span class="hi-icon">🔐</span>
            <div>
              <strong>Secure by Design</strong>
              <p>PDO prepared statements, bcrypt passwords, CSRF tokens on every form.</p>
            </div>
          </div>
          <div class="highlight-item">
            <span class="hi-icon">🌐</span>
            <div>
              <strong>Live Deployment</strong>
              <p>Deployed on a public hosting server, accessible from anywhere.</p>
            </div>
          </div>
          <div class="highlight-item">
            <span class="hi-icon">📱</span>
            <div>
              <strong>Fully Responsive</strong>
              <p>Works on desktop, tablet, and mobile without any CSS framework.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Tech Stack -->
  <section class="about-section about-section--dark">
    <div class="container">
      <h2 class="section-title centered">Technologies Used</h2>
      <div class="tech-grid">
        <?php foreach ($tech as $t): ?>
        <div class="tech-card">
          <span class="tech-icon"><?= $t['icon'] ?></span>
          <strong><?= e($t['name']) ?></strong>
          <span><?= e($t['desc']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Team -->
  <section class="about-section">
    <div class="container">
      <h2 class="section-title centered">Meet the Team</h2>
      <p class="section-subtitle centered">The people who made Movieverse happen.</p>

      <div class="team-grid">
        <?php foreach ($team as $member): ?>
        <div class="team-card">
          <div class="team-photo-wrap">
            <?php if ($member['photo']): ?>
              <img src="<?= e($member['photo']) ?>" alt="<?= e($member['name']) ?>" class="team-photo">
            <?php else: ?>
              <div class="team-avatar"><?= e($member['initials']) ?></div>
            <?php endif; ?>
          </div>
          <div class="team-info">
            <h3 class="team-name"><?= e($member['name']) ?></h3>
            <span class="team-role"><?= e($member['role']) ?></span>
            <span class="team-nim">NIM: <?= e($member['nim']) ?></span>
            <p class="team-bio"><?= e($member['bio']) ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- Closing CTA -->
  <section class="about-cta">
    <div class="container about-cta-inner">
      <h2>Start Exploring</h2>
      <p>Discover films, share your reviews, and build your watchlist.</p>
      <div class="cta-buttons">
        <a href="/movies.php" class="btn btn-primary">Browse Films</a>
        <a href="/register.php" class="btn btn-outline-gold">Join Movieverse</a>
      </div>
    </div>
  </section>

</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
