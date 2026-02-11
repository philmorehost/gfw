<?php
// migrate/index.php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/mail.php';
require_once __DIR__ . '/includes/api_cache.php';

// Check if config exists for DB connection
$pdo = null;
$settings = [
    'name' => 'The Global Football Watch',
    'tagline' => 'The World Watches Football Here.',
    'logo' => 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&q=80&w=200&h=60',
    'whatsapp_number' => '+1234567890',
    'admin_email' => 'admin@example.com',
    'smtp_sender' => 'editorial@theglobalfootballwatch.com'
];

if (file_exists(__DIR__ . '/config.php')) {
    try {
        $pdo = get_db_connection();
        if ($pdo) {
            $db_settings = get_site_settings($pdo);
            if ($db_settings) {
                $settings = $db_settings;
            }
        }
    } catch (Exception $e) {
        // DB connection might fail even if config exists
    }
}

// Simple routing logic
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = ''; // Adjust if site is in a subdirectory

// Remove query string
$path = parse_url($request_uri, PHP_URL_PATH);
$path = str_replace($base_path, '', $path);
$path = trim($path, '/');

$parts = explode('/', $path);

// Installer route
if ($parts[0] === 'install') {
    include __DIR__ . '/install/index.php';
    exit;
}

if (!$pdo && $parts[0] !== 'install') {
    if (file_exists(__DIR__ . '/config.php')) {
        die("Database connection failed. If you need to re-install, please delete migrate/config.php first.");
    }
    header("Location: /install");
    exit;
}

// Admin Routing
if ($parts[0] === 'admin') {
    $admin_page = isset($parts[1]) ? $parts[1] : 'index';

    // Admin login is accessible without auth
    if ($admin_page === 'login') {
        include __DIR__ . '/admin/login.php';
        exit;
    }

    if ($admin_page === 'logout') {
        include __DIR__ . '/admin/logout.php';
        exit;
    }

    require_admin_login();

    switch ($admin_page) {
        case 'index':
        case 'posts':
            include __DIR__ . '/admin/index.php';
            break;
        case 'comments':
            include __DIR__ . '/admin/comments.php';
            break;
        case 'subscribers':
            include __DIR__ . '/admin/subscribers.php';
            break;
        case 'settings':
            include __DIR__ . '/admin/settings.php';
            break;
        case 'profile':
            include __DIR__ . '/admin/profile.php';
            break;
        default:
            include __DIR__ . '/admin/index.php';
            break;
    }
    exit;
}

// Frontend Routing
if ($path === '' || $path === 'index') {
    $page_title = $settings['name'] . " | Elite Coverage";
    include __DIR__ . '/pages/home.php';
} elseif ($parts[0] === 'post' && isset($parts[1])) {
    $post_id = $parts[1];
    include __DIR__ . '/pages/post_detail.php';
} elseif ($parts[0] === 'category' && isset($parts[1])) {
    $category_name = $parts[1];
    include __DIR__ . '/pages/home.php';
} elseif ($path === 'fixtures') {
    include __DIR__ . '/pages/home.php';
} elseif ($path === 'tables') {
    include __DIR__ . '/pages/home.php';
} else {
    // 404
    http_response_code(404);
    echo "<h1>404 - Not Found</h1>";
}
