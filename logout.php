<?php
require_once __DIR__ . '/config/auth.php';
logoutUser();
setFlash('success', 'You have been logged out.');
redirect('/index.php');
