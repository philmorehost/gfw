<?php

if (!isset($pdo)) { require_once __DIR__ . '/../includes/bootstrap.php'; }
require_once __DIR__ . '/../includes/admin_layout.php';

// admin/api_manager.php
$GLOBALS['admin_page'] = 'api-manager';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'])) {
        if (isset($_POST['save_api_settings'])) {
            $api_key = $_POST['api_key'];
            $api_url = $_POST['api_url'];
            $api_header = $_POST['api_header'];
            $api_host = $_POST['api_host'];
            $api_league = $_POST['api_league_id'];
            $api_season = !empty($_POST['api_season']) ? $_POST['api_season'] : null;

            $stmt = $pdo->prepare("UPDATE settings SET api_key=?, api_url=?, api_header=?, api_host=?, api_league_id=?, api_season=? WHERE id=1");
            $stmt->execute([$api_key, $api_url, $api_header, $api_host, $api_league, $api_season]);

            $success = "API settings updated successfully.";
            $settings = get_site_settings($pdo);
        } elseif (isset($_POST['clear_cache'])) {
            $cache_dir = __DIR__ . '/../cache/api/';
            $files = glob($cache_dir . '*.json');
            foreach ($files as $file) {
                if (is_file($file)) unlink($file);
            }
            $success = "API cache cleared successfully. Fresh data will be fetched on next request.";
        }
    }
}

$content_file = __DIR__ . '/api_manager_content.php';
render_admin_layout($content_file, $pdo, $settings, [
    'success' => $success,
    'error' => $error
]);
