<?php
// migrate/includes/header.php

// Attempt to fetch real matches if API KEY is available, else use mock
$api_key = 'YOUR_FOOTBALL_DATA_API_KEY'; // In real app, this would be in settings or config
$matches = [];

if ($api_key !== 'YOUR_FOOTBALL_DATA_API_KEY') {
    $api_url = 'https://api.football-data.org/v4/competitions/PL/matches?status=LIVE,IN_PLAY,FINISHED,SCHEDULED&limit=20';
    $api_data = fetch_with_cache($api_url, $api_key, 300); // 5 min cache
    if ($api_data && isset($api_data['matches'])) {
        foreach ($api_data['matches'] as $m) {
            $matches[] = [
                'id' => $m['id'],
                'homeTeam' => $m['homeTeam']['shortName'] ?: $m['homeTeam']['name'],
                'awayTeam' => $m['awayTeam']['shortName'] ?: $m['awayTeam']['name'],
                'time' => date('H:i', strtotime($m['utcDate'])),
                'league' => 'PL',
                'status' => $m['status'],
                'homeScore' => $m['score']['fullTime']['home'],
                'awayScore' => $m['score']['fullTime']['away']
            ];
        }
    }
}

if (empty($matches)) {
    $matches = [
        ['id' => 'm1', 'homeTeam' => "Arsenal", 'awayTeam' => "Liverpool", 'time' => "16:30", 'league' => "Premier League", 'status' => 'SCHEDULED'],
        ['id' => 'm2', 'homeTeam' => "West Ham", 'awayTeam' => "Man Utd", 'time' => "14:00", 'league' => "Premier League", 'homeScore' => 2, 'awayScore' => 1, 'status' => 'FINISHED'],
        ['id' => 'm3', 'homeTeam' => "Chelsea", 'awayTeam' => "Newcastle", 'time' => "14:00", 'league' => "Premier League", 'status' => 'SCHEDULED'],
        ['id' => 'm4', 'homeTeam' => "Spurs", 'awayTeam' => "Man City", 'time' => "20:00", 'league' => "Premier League", 'status' => 'SCHEDULED']
    ];
}

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo isset($page_title) ? e($page_title) : e($settings['name']); ?></title>
    <!-- Bootstrap 5.3.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Barlow+Condensed:wght@600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
      :root {
        --pitch-dark: #05070a;
        --electric-red: #ff3e3e;
      }

      body {
        font-family: 'Inter', sans-serif;
        background-color: var(--pitch-dark);
        color: #fff;
      }

      h1, h2, h3, h4, h5, h6, .font-condensed {
        font-family: 'Barlow Condensed', sans-serif;
        text-transform: uppercase;
        letter-spacing: -0.01em;
      }

      .text-electric-red { color: var(--electric-red); }
      .bg-electric-red { background-color: var(--electric-red); }
      .border-electric-red { border-color: var(--electric-red); }

      /* Custom Scrollbar */
      ::-webkit-scrollbar { width: 8px; height: 8px; }
      ::-webkit-scrollbar-track { background: #0a0e17; }
      ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 10px; }
      ::-webkit-scrollbar-thumb:hover { background: var(--electric-red); }

      .fixture-scroll::-webkit-scrollbar { height: 4px; }
      .no-scrollbar::-webkit-scrollbar { display: none; }
      .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

      .sharp-text { text-shadow: 0 0 1px rgba(255,255,255,0.1); }

      /* Bootstrap Overrides */
      .card { background-color: #0a0e17; border: 1px solid rgba(255,255,255,0.05); }
      .btn-primary { background-color: var(--electric-red); border-color: var(--electric-red); }
      .btn-primary:hover { background-color: #d32f2f; border-color: #d32f2f; }

      .offcanvas { background-color: #0a0e17; border-right: 1px solid rgba(255,255,255,0.05); }

      .fixture-card { min-width: 220px; border-right: 1px solid rgba(255,255,255,0.05); }

      .group:hover .transition-transform { transform: scale(1.05); }
      .group-hover-red:hover { color: var(--electric-red) !important; }
    </style>
</head>
<body>
    <div class="d-flex flex-column min-vh-screen">

      <!-- HEADER: FIXTURES & MOBILE NAV -->
      <header class="sticky-top bg-black border-bottom border-white border-opacity-10 z-3">
        <div class="d-flex align-items-stretch">
          <button
            class="btn btn-dark rounded-0 border-end border-white border-opacity-10 px-3 d-md-none"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#sidebarOffcanvas"
          >
            <i class="bi bi-list fs-3"></i>
          </button>

          <div class="flex-grow-1 d-flex overflow-x-auto no-scrollbar py-1">
            <?php foreach ($matches as $match): ?>
              <div class="fixture-card p-3 d-flex flex-column justify-content-between h-100 transition-all cursor-pointer">
                <div>
                  <span class="d-block text-uppercase text-secondary fw-black" style="font-size: 9px; letter-spacing: 1px;"><?php echo e($match['league']); ?></span>
                </div>
                <div class="my-2">
                  <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="d-flex align-items-center">
                      <div class="bg-dark rounded-1 me-2 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                        <span class="text-muted fw-bold" style="font-size: 8px;">H</span>
                      </div>
                      <span class="text-uppercase fw-black text-white text-truncate" style="font-size: 11px; max-width: 80px;"><?php echo e($match['homeTeam']); ?></span>
                    </div>
                    <span class="fw-black text-white"><?php echo isset($match['homeScore']) ? e($match['homeScore']) : ''; ?></span>
                  </div>
                  <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                      <div class="bg-dark rounded-1 me-2 d-flex align-items-center justify-content-center" style="width: 20px; height: 20px;">
                        <span class="text-muted fw-bold" style="font-size: 8px;">A</span>
                      </div>
                      <span class="text-uppercase fw-black text-white text-truncate" style="font-size: 11px; max-width: 80px;"><?php echo e($match['awayTeam']); ?></span>
                    </div>
                    <span class="fw-black text-white"><?php echo isset($match['awayScore']) ? e($match['awayScore']) : ''; ?></span>
                  </div>
                </div>
                <div class="pt-2 border-top border-secondary border-opacity-10 d-flex justify-content-between align-items-center">
                  <span class="text-uppercase text-muted fw-bold" style="font-size: 9px;"><?php echo $match['status'] === 'FINISHED' ? 'FINAL' : e($match['time']); ?></span>
                  <span class="badge bg-secondary bg-opacity-10 text-white-50" style="font-size: 8px;">GFW</span>
                </div>
              </div>
            <?php endforeach; ?>
            <div class="fixture-card p-3 d-flex align-items-center">
              <button class="btn btn-outline-light btn-sm rounded-pill text-nowrap fw-black" style="font-size: 10px;">ALL SCORES</button>
            </div>
          </div>
        </div>

        <!-- SECONDARY SPORTS NAV -->
        <div class="bg-dark bg-opacity-50 border-top border-white border-opacity-5 py-2 px-3 overflow-x-auto no-scrollbar">
          <div class="d-flex gap-2">
            <?php
            $sportsFilter = [
                ['name' => 'NFL', 'icon' => '🏈'],
                ['name' => 'FIFA 2026', 'icon' => '⚽'],
                ['name' => 'NCAABK', 'icon' => '🏀'],
                ['name' => 'INDYCAR', 'icon' => '🏎️'],
                ['name' => 'NASCAR', 'icon' => '🏁'],
                ['name' => 'LIV', 'icon' => '⛳'],
                ['name' => 'MLB', 'icon' => '⚾'],
            ];
            foreach ($sportsFilter as $sport): ?>
              <button class="btn btn-sm btn-dark border border-white border-opacity-10 rounded-pill d-flex align-items-center px-3 hover-shadow">
                <span class="me-2"><?php echo $sport['icon']; ?></span>
                <span class="fw-black text-uppercase text-secondary" style="font-size: 9px;"><?php echo e($sport['name']); ?></span>
              </button>
            <?php endforeach; ?>
          </div>
        </div>
      </header>

      <div class="d-flex flex-grow-1">
        <!-- DESKTOP SIDEBAR -->
        <aside class="d-none d-md-flex flex-column bg-dark border-end border-white border-opacity-5" style="width: 240px; position: sticky; top: 120px; height: calc(100vh - 120px);">
          <div class="p-4 mb-4">
            <a href="/" class="text-decoration-none">
              <h1 class="h2 font-condensed fw-black italic text-white mb-0 lh-1">
                <span class="text-electric-red">GLOBAL</span><br/>FOOTBALL
              </h1>
              <small class="text-uppercase text-muted fw-black ls-wider" style="font-size: 10px;">WATCH</small>
            </a>
          </div>

          <nav class="nav flex-column mb-auto">
            <?php
            $navItems = [
                ['name' => 'SCORES', 'path' => '/', 'icon' => 'bi-speedometer2'],
                ['name' => 'WATCH', 'path' => '/watch', 'icon' => 'bi-play-circle'],
                ['name' => 'BETTING', 'path' => '/betting', 'icon' => 'bi-currency-dollar'],
                ['name' => 'STORIES', 'path' => '/stories', 'icon' => 'bi-newspaper'],
            ];
            foreach ($navItems as $item): ?>
              <a
                href="<?php echo e($item['path']); ?>"
                class="nav-link px-4 py-3 fw-black text-uppercase ls-widest <?php echo $_SERVER['REQUEST_URI'] === $item['path'] ? 'text-electric-red' : 'text-secondary hover-white'; ?>"
                style="font-size: 11px;"
              >
                <?php echo e($item['name']); ?>
              </a>
            <?php endforeach; ?>
          </nav>

          <div class="p-4 border-top border-white border-opacity-5">
            <a href="/admin/login" class="nav-link text-secondary fw-black text-uppercase ls-widest mb-3" style="font-size: 10px;">ADMIN PANEL</a>
            <p class="text-muted mb-0" style="font-size: 9px;">© <?php echo date('Y'); ?> GFW NETWORK</p>
          </div>
        </aside>

        <!-- MAIN CONTENT CONTAINER -->
        <main class="flex-grow-1 p-3 p-md-5 container-fluid">
