<?php
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/movies.php';
$_SERVER['HTTP_HOST'] = 'localhost';
ob_start();
include __DIR__ . '/../movies.php';
$html = ob_get_clean();

if (strpos($html, 'Fatal') !== false) {
    echo "Fatal error found in HTML output!\n";
    echo substr($html, strpos($html, 'Fatal') - 100, 500);
} else {
    echo "NO Fatal error found in HTML output!\n";
}
