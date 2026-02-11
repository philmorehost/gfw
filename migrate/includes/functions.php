<?php
// migrate/includes/functions.php

/**
 * XSS Protection: Escape output
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * CSRF Protection: Generate token
 */
function generate_csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRF Protection: Verify token
 */
function verify_csrf_token($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get Site Settings from DB
 */
function get_site_settings($pdo) {
    $stmt = $pdo->query("SELECT * FROM settings LIMIT 1");
    return $stmt->fetch();
}

/**
 * Format Date
 */
function format_date($date) {
    return date('d M Y', strtotime($date));
}

/**
 * Redirect helper
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Social Media Autopost Placeholder
 */
function autopost_to_social($pdo, $title, $url) {
    // In a real implementation, you would use APIs for X/Twitter, Facebook, Instagram, YouTube
    // This requires OAuth tokens and registered apps.
    // For now, we log the attempt.
    $log_msg = "[" . date('Y-m-d H:i:s') . "] Autopost triggered for: $title ($url)\n";
    file_put_contents(__DIR__ . '/../cache/social_autopost.log', $log_msg, FILE_APPEND);
}

/**
 * Simple slugify for URLs
 */
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    if (empty($text)) {
        return 'n-a';
    }
    return $text;
}
