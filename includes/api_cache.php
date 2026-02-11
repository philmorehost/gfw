<?php
// includes/api_cache.php

/**
 * Enhanced Football API Service with Caching for API-Sports.io
 */
class FootballApiService {
    private $api_key;
    private $base_url;
    private $header_name;
    private $cache_dir;

    public function __construct($settings) {
        $this->api_key = $settings['api_key'] ?? '00lee8418970aa40edfd7a4b97cbbb65';
        $this->base_url = $settings['api_url'] ?? 'https://v3.football.api-sports.io';
        $this->header_name = $settings['api_header'] ?? 'x-apisports-key';
        $this->cache_dir = __DIR__ . '/../cache/api/';

        if (!is_dir($this->cache_dir)) {
            mkdir($this->cache_dir, 0755, true);
        }
    }

    private function fetch($endpoint, $params = [], $cache_time = 3600) {
        $cache_file = $this->cache_dir . md5($endpoint . serialize($params)) . '.json';

        if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
            return json_decode(file_get_contents($cache_file), true);
        }

        $url = rtrim($this->base_url, '/') . '/' . ltrim($endpoint, '/');
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $opts = [
            "http" => [
                "method" => "GET",
                "header" => $this->header_name . ": " . $this->api_key . "\r\n"
            ]
        ];

        $context = stream_context_create($opts);
        $response = @file_get_contents($url, false, $context);

        if ($response) {
            file_put_contents($cache_file, $response);
            return json_decode($response, true);
        }

        return null;
    }

    public function getFixtures($league = 39, $season = 2024, $next = 10) {
        $data = $this->fetch('fixtures', ['league' => $league, 'season' => $season, 'next' => $next]);
        return $data['response'] ?? [];
    }

    public function getStandings($league = 39, $season = 2024) {
        $data = $this->fetch('standings', ['league' => $league, 'season' => $season]);
        return $data['response'][0]['league']['standings'][0] ?? [];
    }

    public function getTopScorers($league = 39, $season = 2024) {
        $data = $this->fetch('players/topscorers', ['league' => $league, 'season' => $season]);
        return $data['response'] ?? [];
    }
}

/**
 * Helper to get the API service
 */
function get_football_api($settings) {
    return new FootballApiService($settings);
}
