<?php
// includes/header.php

// Fetch real matches using FootballApiService
if (!isset($api)) {
    $api = get_football_api($settings);
}
$matches = [];

// Try to fetch live fixtures first
$live_api = $api->getLiveFixtures();
if (!empty($live_api)) {
    foreach ($live_api as $f) {
        $matches[] = [
            'id' => $f['fixture']['id'],
            'homeTeam' => $f['teams']['home']['name'],
            'homeLogo' => $f['teams']['home']['logo'],
            'awayTeam' => $f['teams']['away']['name'],
            'awayLogo' => $f['teams']['away']['logo'],
            'time' => $f['fixture']['status']['elapsed'] . "'",
            'league' => $f['league']['name'],
            'status' => 'LIVE',
            'homeScore' => $f['goals']['home'],
            'awayScore' => $f['goals']['away']
        ];
    }
}

// Then fetch upcoming ones to fill the bar
$api_fixtures = $api->getFixtures(null, null, 10);
if (!empty($api_fixtures)) {
    foreach ($api_fixtures as $f) {
        // Avoid duplicates if a live match is also in fixtures
        $exists = false;
        foreach ($matches as $m) { if ($m['id'] === $f['fixture']['id']) $exists = true; }
        if ($exists) continue;

        $matches[] = [
            'id' => $f['fixture']['id'],
            'homeTeam' => $f['teams']['home']['name'],
            'homeLogo' => $f['teams']['home']['logo'],
            'awayTeam' => $f['teams']['away']['name'],
            'awayLogo' => $f['teams']['away']['logo'],
            'time' => date('H:i', strtotime($f['fixture']['date'])),
            'league' => $f['league']['name'],
            'status' => $f['fixture']['status']['short'],
            'homeScore' => $f['goals']['home'],
            'awayScore' => $f['goals']['away']
        ];
    }
}

// If still empty, use fallback with placeholder images
if (empty($matches)) {
    $matches = [
        ['id' => 'm1', 'homeTeam' => "Arsenal", 'homeLogo' => 'https://media.api-sports.io/football/teams/42.png', 'awayTeam' => "Liverpool", 'awayLogo' => 'https://media.api-sports.io/football/teams/40.png', 'time' => "16:30", 'league' => "Premier League", 'status' => 'SCHEDULED'],
        ['id' => 'm2', 'homeTeam' => "West Ham", 'homeLogo' => 'https://media.api-sports.io/football/teams/48.png', 'awayTeam' => "Man Utd", 'awayLogo' => 'https://media.api-sports.io/football/teams/33.png', 'time' => "14:00", 'league' => "Premier League", 'homeScore' => 2, 'awayScore' => 1, 'status' => 'FINISHED'],
        ['id' => 'm3', 'homeTeam' => "Chelsea", 'homeLogo' => 'https://media.api-sports.io/football/teams/49.png', 'awayTeam' => "Newcastle", 'awayLogo' => 'https://media.api-sports.io/football/teams/34.png', 'time' => "14:00", 'league' => "Premier League", 'status' => 'SCHEDULED']
    ];
}

// Fetch Standings for Header Ticker
$header_standings = $api->getStandings();

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo isset($page_title) ? e($page_title) : e($settings['name']); ?></title>
    <link rel="shortcut icon" href="<?php echo e($settings['logo']); ?>" type="image/x-icon">
    <!-- Bootstrap 5.3.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Barlow+Condensed:wght@600;700;800&family=Oswald:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- API-Sports Widgets -->
    <script type="module" src="https://widgets.api-sports.io/3.1.0/widgets.js"></script>

    <api-sports-widget
        data-type="config"
        data-key="<?php echo e($settings['api_key']); ?>"
        data-sport="football"
        data-theme="dark"
        data-refresh="60">
    </api-sports-widget>

    <style>
      .header-match-ticker {
          width: 100%;
          background: #000;
          border-bottom: 1px solid rgba(255,255,255,0.1);
          padding: 0;
          overflow-x: auto;
      }

      api-sports-widget {
          --api-sports-font-family: 'Inter', sans-serif;
          --api-sports-background-color: transparent;
          --api-sports-text-color: #ffffff;
      }

      :root {
        --pitch-dark: #05070a;
        --electric-red: #ff3e3e;
      }

      body {
        font-family: 'Inter', sans-serif;
        background-color: var(--pitch-dark);
        color: #fff;
        text-rendering: optimizeLegibility;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
      }

      h1, h2, h3, h4, h5, h6, .font-condensed {
        font-family: 'Barlow Condensed', sans-serif;
        text-transform: uppercase;
        letter-spacing: -0.01em;
        font-weight: 800;
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

      .nav-link {
        transition: all 0.2s ease-in-out;
        font-weight: 700 !important;
      }

      /* Bootstrap Overrides */
      .card { background-color: #0a0e17; border: 1px solid rgba(255,255,255,0.05); }
      .btn-primary { background-color: var(--electric-red); border-color: var(--electric-red); }
      .btn-primary:hover { background-color: #d32f2f; border-color: #d32f2f; }

      .offcanvas { background-color: #0a0e17; border-right: 1px solid rgba(255,255,255,0.05); }

      .fixture-card { min-width: 220px; border-right: 1px solid rgba(255,255,255,0.05); }

      .article-content p { margin-bottom: 1.5rem; }
      .article-content h2, .article-content h3 { margin-top: 2rem; margin-bottom: 1rem; color: #fff; }

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

          <div class="flex-grow-1 d-flex align-items-center px-4">
             <a href="/" class="text-decoration-none d-md-none">
                <h1 class="h4 font-condensed fw-black italic text-white mb-0">
                  <span class="text-electric-red">GLOBAL</span> FOOTBALL
                </h1>
             </a>
          </div>
        </div>

        <!-- SECONDARY SPORTS NAV -->
        <div class="bg-dark bg-opacity-50 border-top border-white border-opacity-5 py-2 px-3 overflow-x-auto no-scrollbar">
          <div class="d-flex gap-2">
            <div class="d-flex align-items-center bg-electric-red px-3 rounded-pill me-2">
                <span class="text-white fw-black italic text-uppercase" style="font-size: 10px; letter-spacing: 1px;">PL STANDINGS</span>
            </div>
            <div class="d-flex align-items-center gap-4 py-1">
                <?php foreach (array_slice($header_standings, 0, 10) as $team): ?>
                    <div class="d-flex align-items-center gap-2 text-nowrap">
                        <span class="text-white-50 fw-bold" style="font-size: 10px;"><?php echo $team['rank']; ?></span>
                        <span class="text-white fw-black text-uppercase" style="font-size: 10px;"><?php echo e($team['team']['name']); ?></span>
                        <span class="text-electric-red fw-bold" style="font-size: 10px;"><?php echo $team['points']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="border-start border-white border-opacity-10 mx-2"></div>
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
                <span class="fw-black text-uppercase text-gray-300 hover:text-white transition-colors" style="font-size: 9px;"><?php echo e($sport['name']); ?></span>
              </button>
            <?php endforeach; ?>
          </div>
        </div>
      </header>

      <div class="d-flex flex-grow-1">
        <!-- DESKTOP SIDEBAR -->
        <aside class="d-none d-md-flex flex-column bg-dark border-end border-white border-opacity-5" style="width: 240px; position: sticky; top: 120px; height: calc(100vh - 120px);">
          <div class="p-4 mb-4 text-center">
            <a href="/" class="text-decoration-none">
              <?php if ($settings['logo']): ?>
                <img src="<?php echo e($settings['logo']); ?>" alt="<?php echo e($settings['name']); ?>" class="img-fluid mb-2">
              <?php else: ?>
                <h1 class="h2 font-condensed fw-black italic text-white mb-0 lh-1">
                  <span class="text-electric-red">GLOBAL</span><br/>FOOTBALL
                </h1>
                <small class="text-uppercase text-muted fw-black ls-wider" style="font-size: 10px;">WATCH</small>
              <?php endif; ?>
            </a>
          </div>

          <nav class="nav flex-column mb-auto overflow-y-auto no-scrollbar">
            <a href="/" class="nav-link px-4 py-3 fw-black text-uppercase ls-widest <?php echo $_SERVER['REQUEST_URI'] === '/' ? 'text-electric-red' : 'text-gray-200 hover:text-white'; ?>" style="font-size: 12px;">HOME</a>
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2 px-4 mt-2">Categories</p>
            <?php
            if ($pdo):
            $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
            $nav_categories = $stmt->fetchAll();
            foreach ($nav_categories as $cat): ?>
              <a
                href="/<?php echo e($cat['slug']); ?>"
                class="nav-link px-4 py-2 fw-black text-uppercase ls-widest text-gray-200 hover:text-white"
                style="font-size: 11px;"
              >
                <?php echo e($cat['name']); ?>
              </a>
            <?php endforeach; endif; ?>

            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2 px-4 mt-4">Pages</p>
            <?php
            if ($pdo):
            $stmt = $pdo->query("SELECT * FROM pages ORDER BY title ASC");
            $nav_pages = $stmt->fetchAll();
            foreach ($nav_pages as $page): ?>
              <a
                href="/<?php echo e($page['slug']); ?>"
                class="nav-link px-4 py-2 fw-black text-uppercase ls-widest text-gray-200 hover:text-white"
                style="font-size: 11px;"
              >
                <?php echo e($page['title']); ?>
              </a>
            <?php endforeach; endif; ?>
          </nav>

          <div class="p-4 border-top border-white border-opacity-5">
            <a href="/admin/login" class="nav-link text-secondary fw-black text-uppercase ls-widest mb-3" style="font-size: 10px;">ADMIN PANEL</a>
            <p class="text-muted mb-0" style="font-size: 9px;">© <?php echo date('Y'); ?> GFW NETWORK</p>
          </div>
        </aside>

        <!-- MAIN CONTENT CONTAINER -->
        <main class="flex-grow-1 p-3 p-md-5 container-fluid">
