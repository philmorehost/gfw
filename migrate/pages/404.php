<?php
// migrate/pages/404.php

$page_title = "404 - Offside! | " . $settings['name'];
include __DIR__ . '/../includes/header.php';

// Fetch some suggestions
$stmt = $pdo->query("SELECT * FROM posts ORDER BY RAND() LIMIT 4");
$suggestions = $stmt->fetchAll();
?>

<div class="py-20 text-center">
    <div class="mb-10">
        <h1 class="display-1 font-condensed fw-black italic text-electric-red lh-1">404</h1>
        <h2 class="h3 font-condensed fw-black italic text-white tracking-tighter uppercase">OFFSIDE! PAGE NOT FOUND</h2>
        <div class="w-20 h-1 bg-electric-red mx-auto mt-4"></div>
    </div>

    <p class="text-muted max-w-lg mx-auto mb-10 uppercase fw-bold tracking-widest" style="font-size: 11px;">
        It looks like you've wandered into an empty stadium. The page you're looking for doesn't exist or has been transferred to another league.
    </p>

    <!-- SEARCH BOX -->
    <div class="max-w-md mx-auto mb-20">
        <form action="/search" method="GET" class="relative group">
            <input
                type="text"
                name="q"
                placeholder="SEARCH INTELLIGENCE BASE..."
                class="w-full bg-white/5 border border-white/10 rounded-full py-4 px-8 text-white focus:outline-none focus:border-electric-red transition-all"
            >
            <button class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 group-hover:text-electric-red">
                <i class="bi bi-search fs-5"></i>
            </button>
        </form>
    </div>

    <!-- SUGGESTIONS -->
    <div class="max-w-4xl mx-auto">
        <h3 class="font-condensed fw-black italic text-gray-500 mb-8 tracking-widest uppercase" style="font-size: 10px;">Recommended Intelligence</h3>
        <div class="row g-4 text-start">
            <?php foreach ($suggestions as $post): ?>
                <div class="col-md-3">
                    <a href="/post/<?php echo $post['id']; ?>" class="group text-decoration-none">
                        <div class="ratio ratio-1x1 rounded-3 overflow-hidden mb-3 border border-white border-opacity-5">
                            <img src="<?php echo e($post['image']); ?>" class="object-cover transition-transform group-hover:scale-110" alt="">
                        </div>
                        <h4 class="font-condensed fw-black text-white italic group-hover:text-electric-red transition-colors" style="font-size: 12px;"><?php echo e($post['title']); ?></h4>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="mt-20">
        <a href="/" class="btn btn-outline-light rounded-pill px-8 py-3 font-condensed fw-black italic uppercase tracking-widest" style="font-size: 12px;">Back to Home Base</a>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
