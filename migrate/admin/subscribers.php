<?php
// migrate/admin/subscribers.php

$GLOBALS['admin_page'] = 'subscribers';

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'])) {
        if (isset($_POST['delete_subscriber'])) {
            $email = $_POST['email'];
            $stmt = $pdo->prepare("DELETE FROM subscribers WHERE email=?");
            $stmt->execute([$email]);
            redirect('/admin/subscribers');
        }
    }
}

// Fetch Subscribers
$stmt = $pdo->query("SELECT * FROM subscribers ORDER BY date_joined DESC");
$subscribers = $stmt->fetchAll();

require_once __DIR__ . '/../includes/admin_layout.php';
$content_file = __DIR__ . '/subscribers_content.php';
render_admin_layout($content_file, $pdo, $settings, [
    'subscribers' => $subscribers
]);
