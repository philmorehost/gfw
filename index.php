<?php
// index.php (Root Entry Point)

$config_file = __DIR__ . '/migrate/config.php';

// Check if we are already requesting /install to avoid infinite loop
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = trim($path, '/');

if (!file_exists($config_file)) {
    if ($path !== 'install') {
        header("Location: /install");
        exit;
    }
}

// If installed, route to the front controller
require_once __DIR__ . '/migrate/index.php';
