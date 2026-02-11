<?php

if (!isset($pdo)) { require_once __DIR__ . '/../includes/bootstrap.php'; }
// migrate/pages/post_detail.php

$post = null;
$comments = [];

// Fetch post from DB
if ($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();

    if ($post) {
        // Fetch approved comments
        $stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = ? AND status = 'approved' ORDER BY date DESC");
        $stmt->execute([$post_id]);
        $comments = $stmt->fetchAll();
    }
}

// Mock post if DB fails
if (!$post && $post_id == 1) {
    $post = [
        'id' => 1,
        'title' => "Arteta vs Slot: Tactical Masterclass Expected at the Emirates",
        'excerpt' => "The battle at the top of the table heats up as league leaders Arsenal host a resurgent Liverpool side in London.",
        'content' => "The Premier League title race takes center stage this weekend as Arsenal face Liverpool. Both managers have shown incredible tactical flexibility this season, with Mikel Arteta's high-press system going up against Arne Slot's direct attacking philosophy. Key battles in midfield will likely decide the outcome of this heavyweight clash.",
        'category' => 'EPL',
        'author' => "James Wilson",
        'date' => date('Y-m-d'),
        'image' => "https://images.unsplash.com/photo-1574629810360-7efbbe195018?auto=format&fit=crop&q=80&w=1200"
    ];
}

if (!$post) {
    http_response_code(404);
    echo "<h1>Post Not Found</h1>";
    exit;
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    if (verify_csrf_token($_POST['csrf_token'])) {
        $author = $_POST['author'];
        $text = $_POST['text'];

        if ($pdo) {
            $stmt = $pdo->prepare("INSERT INTO comments (post_id, author, text, status) VALUES (?, ?, ?, 'pending')");
            $stmt->execute([$post_id, $author, $text]);
        }

        // Notify admin of new comment
        $admin_email = $settings['admin_email'];
        send_email($pdo, $admin_email, "New Comment Pending Moderation", "A new comment has been submitted for post: " . $post['title']);

        $comment_submitted = true;
    }
}

// Handle subscription
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscribe'])) {
    if (verify_csrf_token($_POST['csrf_token'])) {
        $email = $_POST['email'];
        if ($pdo) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO subscribers (email) VALUES (?)");
            $stmt->execute([$email]);
        }

        // Notify admin
        send_email($pdo, $settings['admin_email'], "New Subscriber", "A new user has subscribed: " . $email);

        $subscribed = true;
    }
}

$page_title = $post['title'] . " | " . $settings['name'];
include __DIR__ . '/../includes/header.php';
?>

<div class="container-fluid p-0 overflow-hidden">
    <!-- HERO SECTION -->
    <section class="position-relative" style="height: 60vh; min-height: 500px;">
        <img src="<?php echo e($post['image']); ?>" class="w-100 h-100 object-fit-cover brightness-50" alt="" />
        <div class="position-absolute bottom-0 start-0 w-100 p-4 p-md-5 bg-gradient-to-t from-black to-transparent">
            <div class="container-fluid">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="badge bg-electric-red rounded-0 fw-black italic text-uppercase px-3 py-2"><?php echo e($post['category']); ?></span>
                    <span class="text-white-50 fw-bold text-uppercase" style="font-size: 12px;"><?php echo format_date($post['date']); ?></span>
                </div>
                <h1 class="display-2 font-condensed fw-black text-white italic text-uppercase lh-1 mb-4"><?php echo e($post['title']); ?></h1>
                <div class="border-start border-danger border-4 ps-4">
                    <p class="text-white-50 fw-bold text-uppercase mb-0">REPORTED BY <span class="text-white"><?php echo e($post['author']); ?></span></p>
                </div>
            </div>
        </div>
    </section>

    <!-- ARTICLE CONTENT -->
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (isset($comment_submitted)): ?>
                    <div class="alert alert-success rounded-0 font-condensed fw-bold italic text-uppercase mb-5">
                        Comment submitted for moderation.
                    </div>
                <?php endif; ?>
                <?php if (isset($subscribed)): ?>
                    <div class="alert alert-success rounded-0 font-condensed fw-bold italic text-uppercase mb-5">
                        Subscription confirmed!
                    </div>
                <?php endif; ?>

                <article class="mb-5">
                    <p class="lead fw-bold italic text-white mb-5 pb-4 border-bottom border-white border-opacity-10 fs-3">
                        "<?php echo e($post['excerpt']); ?>"
                    </p>
                    <div class="text-white-50 lh-lg fs-5" style="white-space: pre-wrap;">
                        <?php echo nl2br(e($post['content'])); ?>
                    </div>
                </article>

                <!-- AI PULSE -->
                <div class="card mb-5 border-start border-danger border-5 shadow-lg bg-dark">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="bg-success rounded-circle animate-pulse" style="width: 8px; height: 8px;"></div>
                            <h3 class="h6 text-electric-red fw-black text-uppercase ls-widest mb-0">AI TACTICAL PULSE</h3>
                        </div>
                        <p class="h4 font-condensed italic text-white mb-0">Elite tactical analysis: Technical stability and high-intensity transition phases observed in recent performances.</p>
                    </div>
                </div>

                <!-- SUBSCRIPTION -->
                <section class="bg-black p-4 p-md-5 mb-5 rounded shadow-lg border border-white border-opacity-5">
                    <h3 class="h3 font-condensed fw-black italic text-white text-uppercase">NEVER MISS A WHISTLE</h3>
                    <p class="text-white-50 text-uppercase fw-bold mb-4" style="font-size: 11px;">Get elite reporting sent straight to your inbox.</p>
                    <form method="POST" class="row g-3">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <div class="col-md-9">
                            <input type="email" name="email" required placeholder="SCOUT@FIELD.COM" class="form-control form-control-lg bg-dark border-secondary text-white rounded-0 fw-bold text-uppercase">
                        </div>
                        <div class="col-md-3">
                            <button name="subscribe" class="btn btn-danger btn-lg w-100 rounded-0 fw-black italic text-uppercase">JOIN</button>
                        </div>
                    </form>
                </section>

                <!-- COMMENTS -->
                <section class="pt-5 border-top border-white border-opacity-10">
                    <h2 class="h3 font-condensed fw-black italic text-white text-uppercase mb-5">TERRACE TALK (<?php echo count($comments); ?>)</h2>

                    <form method="POST" class="card bg-dark bg-opacity-50 p-4 p-md-5 mb-5 border-0">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <div class="mb-3">
                            <input required type="text" name="author" placeholder="YOUR NAME" class="form-control bg-dark border-secondary text-white rounded-0 fw-bold">
                        </div>
                        <div class="mb-3">
                            <textarea required name="text" rows="4" placeholder="JOIN THE DEBATE..." class="form-control bg-dark border-secondary text-white rounded-0 fw-bold"></textarea>
                        </div>
                        <button name="submit_comment" class="btn btn-danger btn-lg rounded-0 fw-black italic text-uppercase py-3">POST RESPONSE</button>
                    </form>

                    <div class="space-y-4">
                        <?php foreach ($comments as $c): ?>
                            <div class="card bg-dark border-0 border-start border-secondary border-4 mb-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-electric-red fw-black text-uppercase italic"><?php echo e($c['author']); ?></span>
                                        <span class="text-muted fw-bold text-uppercase" style="font-size: 10px;"><?php echo format_date($c['date']); ?></span>
                                    </div>
                                    <p class="text-white mb-0">"<?php echo e($c['text']); ?>"</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
