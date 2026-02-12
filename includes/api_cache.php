<?php
// includes/api_cache.php

/**
 * Enhanced Football API Service with Caching for API-Sports.io
 */
class FootballApiService {
    private $api_key;
    private $base_url;
    private $header_name;
    private $api_host;
    private $cache_dir;
    private $default_league;
    private $default_season;

    public function __construct($settings) {
        $this->api_key = $settings['api_key'] ?? '00lee8418970aa40edfd7a4b97cbbb65';
        $this->base_url = $settings['api_url'] ?? 'https://v3.football.api-sports.io';
        $this->header_name = $settings['api_header'] ?? 'x-apisports-key';
        $this->api_host = $settings['api_host'] ?? '';
        $this->default_league = $settings['api_league_id'] ?? 39;
        $this->default_season = $settings['api_season'] ?? get_current_season();

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

        $headers = $this->header_name . ": " . $this->api_key . "\r\n";
        if (!empty($this->api_host)) {
            $headers .= "x-rapidapi-host: " . $this->api_host . "\r\n";
        }

        $opts = [
            "http" => [
                "method" => "GET",
                "header" => $headers
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

    public function getFixtures($league = null, $season = null, $next = 10) {
        $l = $league ?? $this->default_league;
        $s = $season ?? $this->default_season;
        $data = $this->fetch('fixtures', ['league' => $l, 'season' => $s, 'next' => $next]);
        return $data['response'] ?? [];
    }

    public function getStandings($league = null, $season = null) {
        $l = $league ?? $this->default_league;
        $s = $season ?? $this->default_season;
        $data = $this->fetch('standings', ['league' => $l, 'season' => $s]);
        return $data['response'][0]['league']['standings'][0] ?? [];
    }

    public function getTopScorers($league = null, $season = null) {
        $l = $league ?? $this->default_league;
        $s = $season ?? $this->default_season;
        $data = $this->fetch('players/topscorers', ['league' => $l, 'season' => $s]);
        return $data['response'] ?? [];
    }
}

/**
 * Helper to get the API service
 */
function get_football_api($settings) {
    return new FootballApiService($settings);
}
