<?php
// migrate/pages/page.php

if (!isset($pdo)) { require_once __DIR__ . '/../includes/bootstrap.php'; }

$stmt = $pdo->prepare("SELECT * FROM pages WHERE slug = ?");
$stmt->execute([$page_slug]);
$page = $stmt->fetch();

if (!$page) {
    http_response_code(404);
    include __DIR__ . '/404.php';
    exit;
}

$page_title = $page['title'] . " | " . $settings['name'];
include __DIR__ . '/../includes/header.php';
?>

<div class="py-12">
    <div class="max-w-4xl mx-auto">
        <h1 class="display-3 font-condensed fw-black italic text-white uppercase mb-8"><?php echo e($page['title']); ?></h1>
        <div class="w-20 h-2 bg-electric-red mb-12"></div>

        <div class="text-white-50 lh-lg fs-5 ck-content">
            <?php echo $page['content']; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
