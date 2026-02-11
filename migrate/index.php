<?php
// migrate/index.php

require_once __DIR__ . '/includes/bootstrap.php';

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

// PDO check is now handled in bootstrap.php

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

    if ($admin_page === 'forgot-password') {
        include __DIR__ . '/admin/forgot-password.php';
        exit;
    }

    if ($admin_page === 'reset-password') {
        include __DIR__ . '/admin/reset-password.php';
        exit;
    }

    require_admin_login();

    switch ($admin_page) {
        case 'index':
        case 'posts':
            include __DIR__ . '/admin/index.php';
            break;
        case 'categories':
            include __DIR__ . '/admin/categories.php';
            break;
        case 'pages':
            include __DIR__ . '/admin/pages.php';
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
} elseif ($parts[0] === 'page' && isset($parts[1])) {
    $page_slug = $parts[1];
    include __DIR__ . '/pages/page.php';
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
    include __DIR__ . '/pages/404.php';
}
