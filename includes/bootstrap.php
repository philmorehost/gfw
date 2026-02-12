<?php
// includes/bootstrap.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/mail.php';
require_once __DIR__ . '/api_cache.php';
require_once __DIR__ . '/social.php';

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

if (file_exists(__DIR__ . '/../config.php')) {
    try {
        $pdo = get_db_connection();
        if ($pdo) {
            // Ensure schema is up to date
            run_migrations($pdo);

            $db_settings = get_site_settings($pdo);
            if ($db_settings) {
                $settings = $db_settings;
            }
        }
    } catch (Exception $e) {
        // DB connection failed
    }
}

$api = get_football_api($settings);

// Global check for PDO if we are not in the installer
if (!$pdo && strpos($_SERVER['REQUEST_URI'], '/install') === false) {
    if (file_exists(__DIR__ . '/../config.php')) {
        die("Database connection failed. Please check your database credentials in config.php.");
    } else {
        header("Location: /install");
        exit;
    }
}
