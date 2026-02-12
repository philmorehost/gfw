<?php
// install/stage3.php

require_once __DIR__ . '/../includes/db.php';
$pdo = get_db_connection();

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_email = $_POST['admin_email'];
    $admin_pass = $_POST['admin_pass'];
    $site_name = $_POST['site_name'];
    $site_tagline = $_POST['site_tagline'];
    $smtp_sender = $_POST['smtp_sender'];

    try {
        // Clear existing users just in case
        $pdo->exec("DELETE FROM users");

        // Hash password
        $hashed_pass = password_hash($admin_pass, PASSWORD_BCRYPT);

        // Insert Admin
        $stmt = $pdo->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->execute([$admin_email, $hashed_pass]);

        // Insert Settings
        $pdo->exec("DELETE FROM settings");
        $stmt = $pdo->prepare("INSERT INTO settings (id, name, tagline, logo, admin_email, smtp_sender, whatsapp_number, fb_url, tw_url, ig_url, yt_url, api_key, api_url, api_header) VALUES (1, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $site_name,
            $site_tagline,
            'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&q=80&w=200&h=60', // default logo
            $admin_email,
            $smtp_sender,
            '+1234567890',
            'https://facebook.com/globalfootballwatch',
            'https://twitter.com/globalfootballwatch',
            'https://instagram.com/globalfootballwatch',
            'https://youtube.com/globalfootballwatch',
            '00lee8418970aa40edfd7a4b97cbbb65',
            'https://v3.football.api-sports.io',
            'x-apisports-key'
        ]);

        $success = true;

    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<div class="text-center mb-5">
    <h2 class="display-6 font-condensed fw-black italic text-white text-uppercase">Stage 3: Identity Setup</h2>
    <p class="text-muted text-uppercase fw-bold ls-widest" style="font-size: 10px;">Establishing administrative command and control</p>
</div>

<?php if ($success): ?>
    <div class="alert alert-success rounded-0 font-condensed fw-bold italic text-uppercase mb-4">
        Admin account and site settings established.
    </div>
    <div class="text-end">
        <a href="?stage=4" class="btn btn-danger rounded-0 fw-black italic text-uppercase px-5 py-3">Finalize Deployment</a>
    </div>
<?php else: ?>
    <?php if ($error): ?>
        <div class="alert alert-danger rounded-0 font-condensed fw-bold italic text-uppercase mb-4">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <h3 class="h6 font-condensed fw-black text-white italic text-uppercase border-bottom border-white border-opacity-10 pb-2 mb-3">Administrator Credentials</h3>
        <div class="mb-3">
            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 d-block">Admin Email</label>
            <input type="email" name="admin_email" required class="form-control bg-dark border-white border-opacity-10 text-white rounded-0" value="admin@example.com">
        </div>
        <div class="mb-3">
            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 d-block">Admin Password</label>
            <input type="password" name="admin_pass" required class="form-control bg-dark border-white border-opacity-10 text-white rounded-0" value="password123">
        </div>

        <h3 class="h6 font-condensed fw-black text-white italic text-uppercase border-bottom border-white border-opacity-10 pb-2 mb-3 mt-4">Site Configuration</h3>
        <div class="mb-3">
            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 d-block">Website Name</label>
            <input type="text" name="site_name" required class="form-control bg-dark border-white border-opacity-10 text-white rounded-0" value="The Global Football Watch">
        </div>
        <div class="mb-3">
            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 d-block">Tagline</label>
            <input type="text" name="site_tagline" required class="form-control bg-dark border-white border-opacity-10 text-white rounded-0" value="The World Watches Football Here.">
        </div>
        <div class="mb-3">
            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 d-block">SMTP Sender Email</label>
            <input type="email" name="smtp_sender" required class="form-control bg-dark border-white border-opacity-10 text-white rounded-0" value="editorial@theglobalfootballwatch.com">
        </div>

        <div class="text-end pt-3">
            <button type="submit" class="btn btn-danger rounded-0 fw-black italic text-uppercase px-5 py-3">Establish Command</button>
        </div>
    </form>
<?php endif; ?>
