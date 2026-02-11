<?php
// migrate/includes/social.php

/**
 * Autopost to social media handles
 */
function autopost_to_social($pdo, $post_id) {
    $settings = get_site_settings($pdo);

    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if (!$post) return;

    $message = $post['title'] . "\n\n" . $post['excerpt'] . "\n\n" . "Read more: " . get_base_url() . "/post/" . $post['id'];

    // Log for simulation
    error_log("Autoposting to social media: " . $post['title']);

    // Mock implementations for requested handles
    // In a real scenario, you would use official APIs (e.g. facebook-php-sdk, etc.)

    // Facebook
    if (!empty($settings['fb_access_token'])) {
        // post_to_facebook($settings['fb_access_token'], $message, $post['image']);
    }

    // X (Twitter)
    if (!empty($settings['tw_api_key'])) {
        // post_to_x($settings['tw_api_key'], $settings['tw_api_secret'], $message);
    }

    // LinkedIn
    if (!empty($settings['li_access_token'])) {
        // post_to_linkedin($settings['li_access_token'], $message);
    }

    // Instagram & Threads (Meta Graph API)
    if (!empty($settings['meta_access_token'])) {
        // post_to_meta($settings['meta_access_token'], $message);
    }
}

/**
 * Helper to get base URL
 */
function get_base_url() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    return $protocol . "://" . $host;
}
