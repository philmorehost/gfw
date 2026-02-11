<?php
// migrate/pages/home.php

// Fetch Category if filtered
$category = null;
if (isset($category_name)) {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
    $stmt->execute([$category_name]);
    $category = $stmt->fetch();
}

// Fetch Featured Stories
$sql = "SELECT * FROM posts WHERE is_top_story = 1";
if ($category) $sql .= " AND category_id = " . $category['id'];
$sql .= " ORDER BY date DESC LIMIT 5";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$featured_posts = $stmt->fetchAll();

// Fetch Latest News
$sql = "SELECT * FROM posts WHERE is_top_story = 0";
if ($category) $sql .= " AND category_id = " . $category['id'];
$sql .= " ORDER BY date DESC LIMIT 10";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$latest_posts = $stmt->fetchAll();

// Fetch Transfer News
$stmt = $pdo->prepare("SELECT p.* FROM posts p JOIN categories c ON p.category_id = c.id WHERE c.slug = 'transfers' ORDER BY p.date DESC LIMIT 4");
$stmt->execute();
$transfer_posts = $stmt->fetchAll();

// API Data
$api = get_football_api($settings);
$standings = $api->getStandings(39, 2024);
$top_scorers = $api->getTopScorers(39, 2024);
$fixtures = $api->getFixtures(39, 2024, 5);

include __DIR__ . '/../includes/header.php';
?>

<div class="row g-5">
    <!-- LEFT COLUMN: NEWS -->
    <div class="col-lg-8">
        <!-- FEATURED SECTION -->
        <section class="mb-5">
            <h2 class="h4 font-condensed fw-black italic text-white border-bottom border-electric-red border-4 d-inline-block pb-1 mb-4">Featured Stories</h2>
            <?php if (!empty($featured_posts)): ?>
                <div id="featuredCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner rounded-3 overflow-hidden shadow-2xl">
                        <?php foreach ($featured_posts as $index => $post): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?> group cursor-pointer" onclick="location.href='/post/<?php echo $post['id']; ?>'">
                                <div class="ratio ratio-16x9 overflow-hidden">
                                    <img src="<?php echo e($post['image']); ?>" class="object-cover transition-transform duration-700 group-hover:scale-110" alt="<?php echo e($post['title']); ?>">
                                </div>
                                <div class="carousel-caption d-none d-md-block text-start start-0 bottom-0 w-100 p-5 bg-gradient-to-t from-black via-black/80 to-transparent">
                                    <span class="badge bg-electric-red rounded-0 mb-2"><?php echo e($post['category'] ?? 'TOP STORY'); ?></span>
                                    <h3 class="display-5 font-condensed fw-black italic text-white lh-1"><?php echo e($post['title']); ?></h3>
                                    <p class="text-white-50 mt-2"><?php echo e($post['excerpt']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </section>

        <!-- LATEST NEWS -->
        <section class="mb-5">
            <h2 class="h4 font-condensed fw-black italic text-white border-bottom border-electric-red border-4 d-inline-block pb-1 mb-4">Latest Intelligence</h2>
            <div class="row g-4">
                <?php foreach ($latest_posts as $post): ?>
                    <div class="col-md-6 group cursor-pointer" onclick="location.href='/post/<?php echo $post['id']; ?>'">
                        <div class="card h-100 border-0 bg-transparent">
                            <div class="ratio ratio-16x9 rounded-3 overflow-hidden mb-3">
                                <img src="<?php echo e($post['image']); ?>" class="object-cover transition-transform duration-500 group-hover:scale-105" alt="">
                            </div>
                            <div class="card-body p-0">
                                <span class="text-electric-red font-condensed fw-bold ls-widest" style="font-size: 10px;"><?php echo e($post['category'] ?? 'NEWS'); ?></span>
                                <h4 class="h5 font-condensed fw-black text-white italic mt-1 group-hover:text-electric-red transition-colors"><?php echo e($post['title']); ?></h4>
                                <p class="text-muted small"><?php echo e($post['excerpt']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- VIDEO HIGHLIGHTS -->
        <section class="mb-5">
            <h2 class="h4 font-condensed fw-black italic text-white border-bottom border-electric-red border-4 d-inline-block pb-1 mb-4">Video Intelligence</h2>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden bg-black/40 flex items-center justify-center border border-white/5 relative group cursor-pointer">
                        <img src="https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&q=80&w=800" class="object-cover w-full h-full opacity-40 group-hover:opacity-60 transition-opacity" alt="">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="bi bi-play-circle-fill text-6xl text-electric-red group-hover:scale-110 transition-transform"></i>
                        </div>
                    </div>
                    <h4 class="font-condensed fw-black text-white italic mt-2" style="font-size: 13px;">MATCH HIGHLIGHTS: ARSENAL VS LIVERPOOL</h4>
                </div>
                <div class="col-md-6">
                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden bg-black/40 flex items-center justify-center border border-white/5 relative group cursor-pointer">
                        <img src="https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&q=80&w=800" class="object-cover w-full h-full opacity-40 group-hover:opacity-60 transition-opacity" alt="">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="bi bi-play-circle-fill text-6xl text-electric-red group-hover:scale-110 transition-transform"></i>
                        </div>
                    </div>
                    <h4 class="font-condensed fw-black text-white italic mt-2" style="font-size: 13px;">TACTICAL BREAKDOWN: ARNE SLOT'S PHILOSOPHY</h4>
                </div>
            </div>
        </section>

        <!-- TRANSFER NEWS -->
        <section class="mb-5">
            <h2 class="h4 font-condensed fw-black italic text-white border-bottom border-electric-red border-4 d-inline-block pb-1 mb-4">Transfer Intelligence</h2>
            <div class="row g-3">
                <?php foreach ($transfer_posts as $post): ?>
                    <div class="col-md-3 group cursor-pointer" onclick="location.href='/post/<?php echo $post['id']; ?>'">
                        <div class="ratio ratio-1x1 rounded-3 overflow-hidden mb-2">
                            <img src="<?php echo e($post['image']); ?>" class="object-cover transition-transform group-hover:scale-110" alt="">
                        </div>
                        <h4 class="font-condensed fw-black text-white italic" style="font-size: 11px;"><?php echo e($post['title']); ?></h4>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <!-- RIGHT COLUMN: STATS & TABLES -->
    <div class="col-lg-4">
        <!-- STANDINGS -->
        <section class="mb-5 bg-dark p-4 rounded-3 border border-white border-opacity-5">
            <h2 class="h5 font-condensed fw-black italic text-white mb-4">League Table</h2>
            <div class="table-responsive">
                <table class="table table-dark table-sm table-hover border-transparent">
                    <thead class="text-muted uppercase" style="font-size: 9px;">
                        <tr>
                            <th>POS</th>
                            <th>TEAM</th>
                            <th>P</th>
                            <th>GD</th>
                            <th>PTS</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 11px;">
                        <?php foreach (array_slice($standings, 0, 10) as $team): ?>
                            <tr>
                                <td class="fw-black text-muted"><?php echo $team['rank']; ?></td>
                                <td class="fw-bold text-white text-uppercase"><?php echo e($team['team']['name']); ?></td>
                                <td><?php echo $team['all']['played']; ?></td>
                                <td><?php echo $team['goalsDiff']; ?></td>
                                <td class="fw-black text-electric-red"><?php echo $team['points']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-center mt-3">
                <button class="btn btn-outline-secondary btn-sm rounded-pill font-condensed fw-black px-4" style="font-size: 10px;">VIEW FULL TABLE</button>
            </div>
        </section>

        <!-- TOP SCORERS -->
        <section class="mb-5 bg-dark p-4 rounded-3 border border-white border-opacity-5">
            <h2 class="h5 font-condensed fw-black italic text-white mb-4">Golden Boot Race</h2>
            <div class="space-y-4">
                <?php foreach (array_slice($top_scorers, 0, 5) as $index => $player): ?>
                    <div class="d-flex align-items-center justify-content-between p-2 rounded-2 hover:bg-white/5 transition-colors">
                        <div class="d-flex align-items-center">
                            <span class="font-condensed fw-black italic text-muted me-3" style="font-size: 18px;">#<?php echo $index + 1; ?></span>
                            <img src="<?php echo $player['player']['photo']; ?>" class="w-8 h-8 rounded-circle me-3 border border-white border-opacity-10" alt="">
                            <div>
                                <h4 class="font-condensed fw-black text-white italic mb-0" style="font-size: 13px;"><?php echo e($player['player']['name']); ?></h4>
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 9px;"><?php echo e($player['statistics'][0]['team']['name']); ?></small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="display-6 font-condensed fw-black italic text-electric-red" style="font-size: 20px;"><?php echo $player['statistics'][0]['goals']['total']; ?></span>
                            <small class="d-block text-muted uppercase fw-bold" style="font-size: 8px;">GOALS</small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- FIXTURES -->
        <section class="bg-dark p-4 rounded-3 border border-white border-opacity-5">
            <h2 class="h5 font-condensed fw-black italic text-white mb-4">Upcoming Battles</h2>
            <div class="space-y-3">
                <?php foreach ($fixtures as $fix): ?>
                    <div class="p-3 bg-black/40 rounded-3 border border-white border-opacity-5 text-center">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="w-40 text-end">
                                <span class="d-block font-condensed fw-black text-white italic" style="font-size: 12px;"><?php echo e($fix['teams']['home']['name']); ?></span>
                            </div>
                            <div class="bg-electric-red px-2 py-1 rounded-1 font-condensed fw-black italic mx-3" style="font-size: 10px;">VS</div>
                            <div class="w-40 text-start">
                                <span class="d-block font-condensed fw-black text-white italic" style="font-size: 12px;"><?php echo e($fix['teams']['away']['name']); ?></span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted font-bold text-uppercase" style="font-size: 9px;"><?php echo date('D d M - H:i', strtotime($fix['fixture']['date'])); ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
