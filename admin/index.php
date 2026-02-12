<?php

if (!isset($pdo)) { require_once __DIR__ . '/../includes/bootstrap.php'; }
require_once __DIR__ . '/../includes/admin_layout.php';

// admin/index.php (Dashboard)
$GLOBALS['admin_page'] = 'dashboard';

// Fetch Statistics
$stmt = $pdo->query("SELECT COUNT(*) as count FROM posts");
$total_posts = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM comments");
$total_comments = $stmt->fetch()['count'];

$stmt = $pdo->query("SELECT COUNT(*) as count FROM comments WHERE status = 'pending'");
$pending_comments = $stmt->fetch()['count'];

$content_file = __DIR__ . '/dashboard_content.php';
render_admin_layout($content_file, $pdo, $settings, [
    'total_posts' => $total_posts,
    'total_comments' => $total_comments,
    'pending_comments' => $pending_comments
]);
