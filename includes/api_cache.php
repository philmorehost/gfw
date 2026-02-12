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

        if ($cache_time > 0 && file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_time)) {
            return json_decode(file_get_contents($cache_file), true);
        }

        $url = rtrim($this->base_url, '/') . '/' . ltrim($endpoint, '/');
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);

        $headers = [
            $this->header_name . ": " . $this->api_key
        ];
        if (!empty($this->api_host)) {
            $headers[] = "x-rapidapi-host: " . $this->api_host;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($response && $http_code === 200) {
            if ($cache_time > 0) {
                file_put_contents($cache_file, $response);
            }
            return json_decode($response, true);
        }

        // Return error details if it's a test or if needed
        if ($cache_time === 0) {
            return [
                'error' => true,
                'http_code' => $http_code,
                'curl_error' => $curl_error,
                'response' => $response ? json_decode($response, true) : null
            ];
        }

        return null;
    }

    public function getFixtures($league = null, $season = null, $next = 10, $date = null) {
        $l = $league ?? $this->default_league;
        $s = $season ?? $this->default_season;
        $params = ['league' => $l, 'season' => $s];
        if ($date) $params['date'] = $date;
        elseif ($next) $params['next'] = $next;

        $data = $this->fetch('fixtures', $params);
        return $data['response'] ?? [];
    }

    public function getLiveFixtures($league = null) {
        $l = $league ?? $this->default_league;
        // Live matches don't use cache usually or very short cache
        $data = $this->fetch('fixtures', ['league' => $l, 'live' => 'all'], 300);
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

    public function getTeams($league = null, $season = null) {
        $l = $league ?? $this->default_league;
        $s = $season ?? $this->default_season;
        $data = $this->fetch('teams', ['league' => $l, 'season' => $s], 86400 * 7); // Cache teams for 1 week
        return $data['response'] ?? [];
    }

    public function testConnection() {
        // Status endpoint is great for testing credentials
        $data = $this->fetch('status', [], 0);

        if (isset($data['error']) && $data['error']) {
            $msg = "API Connection Failed (HTTP {$data['http_code']}).";
            if ($data['http_code'] == 401 || $data['http_code'] == 403) {
                $msg = "Invalid API Key or Access Denied (HTTP {$data['http_code']}).";
            } elseif ($data['http_code'] == 0) {
                $msg = "Connection Error: " . ($data['curl_error'] ?: "Check API URL");
            }
            return ['success' => false, 'message' => $msg];
        }

        if ($data && isset($data['response']) && !empty($data['response'])) {
            return [
                'success' => true,
                'message' => 'API Connection Successful. Account: ' . ($data['response']['account']['firstname'] ?? 'Active'),
                'data' => $data['response']
            ];
        }

        return [
            'success' => false,
            'message' => 'API Connection Failed. Unexpected response format.'
        ];
    }
}

/**
 * Helper to get the API service
 */
function get_football_api($settings) {
    return new FootballApiService($settings);
}
