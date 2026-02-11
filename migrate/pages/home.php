<?php

if (!isset($pdo)) { require_once __DIR__ . '/../includes/bootstrap.php'; }
// migrate/pages/home.php

$category = isset($category_name) ? $category_name : null;
$posts = [];

// Fetch posts from DB
if ($pdo) {
    if ($category) {
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE category = ? ORDER BY date DESC");
        $stmt->execute([$category]);
    } else {
        $stmt = $pdo->query("SELECT * FROM posts ORDER BY date DESC");
    }
    $posts = $stmt->fetchAll();
}

// If no posts, use some mock data (or just show empty)
if (empty($posts)) {
    $posts = [
        [
            'id' => 1,
            'title' => "Arteta vs Slot: Tactical Masterclass Expected at the Emirates",
            'excerpt' => "The battle at the top of the table heats up as league leaders Arsenal host a resurgent Liverpool side in London.",
            'category' => 'EPL',
            'author' => "James Wilson",
            'date' => date('Y-m-d'),
            'image' => "https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&q=80&w=1200",
            'is_top_story' => 1
        ],
        [
            'id' => 2,
            'title' => "Champions League: Real Madrid's Great Escape",
            'excerpt' => "Once again, the kings of Europe prove they can never be counted out after a dramatic comeback.",
            'category' => 'UCL',
            'author' => "Sarah Hughes",
            'date' => date('Y-m-d'),
            'image' => "https://images.unsplash.com/photo-1556056504-5c7696c4c28d?auto=format&fit=crop&q=80&w=800",
            'is_top_story' => 0
        ]
    ];
}

$featuredStories = array_filter($posts, function($p) { return isset($p['is_top_story']) && $p['is_top_story']; });
if (empty($featuredStories)) $featuredStories = array_slice($posts, 0, 1);
$featuredStories = array_slice($featuredStories, 0, 3);

$sideStories = array_filter($posts, function($p) use ($featuredStories) {
    foreach ($featuredStories as $f) if ($f['id'] == $p['id']) return false;
    return true;
});
$sideStories = array_slice($sideStories, 0, 6);

include __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid py-2">

    <!-- FEATURED STORIES -->
    <section class="mb-5">
        <div class="d-flex align-items-center mb-4 border-bottom border-danger border-4 pb-1 d-inline-block">
            <h2 class="h4 font-condensed fw-black italic text-white mb-0 text-uppercase">FEATURED STORIES</h2>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="row g-4">
                    <?php foreach ($featuredStories as $idx => $post): ?>
                        <div class="<?php echo $idx === 0 ? "col-12" : "col-md-6"; ?>">
                            <a href="/post/<?php echo $post['id']; ?>" class="card border-0 rounded-0 overflow-hidden text-decoration-none group shadow-lg position-relative" style="height: <?php echo $idx === 0 ? '450px' : '350px'; ?>;">
                                <img src="<?php echo e($post['image']); ?>" class="card-img h-100 object-fit-cover brightness-50 transition-transform" alt="" />
                                <div class="card-img-overlay d-flex flex-column justify-content-end p-4">
                                    <span class="badge bg-electric-red rounded-0 mb-3 align-self-start fw-black italic text-uppercase ls-widest"><?php echo e($post['category']); ?></span>
                                    <h3 class="card-title text-white font-condensed fw-black text-uppercase italic <?php echo $idx === 0 ? 'display-5' : 'h3'; ?> lh-1 mb-0">
                                        <?php echo e($post['title']); ?>
                                    </h3>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="border-bottom border-white border-opacity-10 mb-4 pb-2">
                    <h3 class="h5 font-condensed fw-black text-white italic text-uppercase ls-wider">MORE STORIES</h3>
                </div>
                <div class="list-group list-group-flush bg-transparent">
                    <?php foreach ($sideStories as $post): ?>
                        <a href="/post/<?php echo $post['id']; ?>" class="list-group-item list-group-item-action bg-transparent border-0 px-0 mb-3 text-decoration-none">
                            <div class="d-flex align-items-start gap-3">
                                <div class="bg-danger mt-1" style="width: 8px; height: 8px; flex-shrink: 0;"></div>
                                <div>
                                    <h4 class="h6 text-white fw-bold text-uppercase mb-1 line-clamp-2"><?php echo e($post['title']); ?></h4>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-electric-red fw-black text-uppercase" style="font-size: 9px;"><?php echo e($post['category']); ?></span>
                                        <span class="text-muted fw-bold text-uppercase" style="font-size: 9px;">• <?php echo format_date($post['date']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- LATEST NEWS GRID -->
    <section class="mb-5">
        <div class="border-bottom border-white border-opacity-10 mb-4 pb-1">
            <h2 class="h5 font-condensed fw-black italic text-white text-uppercase">LATEST UPDATES</h2>
        </div>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-4">
            <?php foreach (array_slice($posts, 0, 8) as $post): ?>
                <div class="col">
                    <a href="/post/<?php echo $post['id']; ?>" class="card border-0 rounded-0 bg-transparent text-decoration-none h-100 group">
                        <div class="position-relative overflow-hidden mb-3" style="height: 180px;">
                            <img src="<?php echo e($post['image']); ?>" class="w-100 h-100 object-fit-cover transition-transform" alt="" />
                            <span class="position-absolute top-0 start-0 m-2 badge bg-dark bg-opacity-75 rounded-0 fw-black text-uppercase" style="font-size: 8px;"><?php echo e($post['category']); ?></span>
                        </div>
                        <div class="card-body p-0">
                            <h3 class="h6 text-white fw-bold text-uppercase line-clamp-2 group-hover-red mb-2"><?php echo e($post['title']); ?></h3>
                            <p class="text-muted fw-bold text-uppercase mb-0" style="font-size: 10px; letter-spacing: 1px;"><?php echo format_date($post['date']); ?> • <?php echo e($post['author']); ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
