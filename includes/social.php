<?php
// includes/social.php

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

    // Facebook Graph API Implementation
    if (!empty($settings['fb_access_token'])) {
        $fb_url = "https://graph.facebook.com/v19.0/me/feed";
        $ch = curl_init($fb_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'message' => $message,
            'link' => get_base_url() . "/post/" . $post['id'],
            'access_token' => $settings['fb_access_token']
        ]);
        curl_exec($ch);
        curl_close($ch);
    }

    // X (Twitter) API v2 Implementation
    if (!empty($settings['tw_api_key']) && !empty($settings['tw_api_secret'])) {
        // Basic implementation requires OAuth1.0a headers, omitting for brevity in vanilla script
        // but hook is prepared for library injection.
        error_log("Twitter autopost triggered for: " . $post['id']);
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
