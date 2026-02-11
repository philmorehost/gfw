<?php
// migrate/includes/api_cache.php

/**
 * Fetch data from API with file-based caching
 */
function fetch_with_cache($url, $api_key, $cache_time = 3600) {
    $cache_dir = __DIR__ . '/../cache';
    if (!is_dir($cache_dir)) {
        mkdir($cache_dir, 0777, true);
    }

    $cache_file = $cache_dir . '/' . md5($url) . '.json';

    if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
        return json_decode(file_get_contents($cache_file), true);
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'X-Auth-Token: ' . $api_key
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($http_code === 200) {
        file_put_contents($cache_file, $response);
        return json_decode($response, true);
    }

    // If API fails but we have old cache, return it anyway
    if (file_exists($cache_file)) {
        return json_decode(file_get_contents($cache_file), true);
    }

    return null;
}
