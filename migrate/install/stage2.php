<?php
// migrate/install/stage2.php

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];

    try {
        $pdo = new PDO("mysql:host=$db_host", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Create database if not exists
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `$db_name`");

        // Create Tables
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE,
            slug VARCHAR(100) NOT NULL UNIQUE,
            description TEXT
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS posts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            excerpt TEXT,
            content LONGTEXT,
            category_id INT,
            author VARCHAR(100),
            image VARCHAR(255),
            date DATE,
            is_top_story BOOLEAN DEFAULT FALSE,
            meta_title VARCHAR(255),
            meta_description TEXT,
            meta_keywords VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS pages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            content LONGTEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS comments (
            id INT AUTO_INCREMENT PRIMARY KEY,
            post_id INT,
            author VARCHAR(100),
            email VARCHAR(255),
            text TEXT,
            status ENUM('pending', 'approved', 'rejected', 'spam') DEFAULT 'pending',
            date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS subscribers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) NOT NULL UNIQUE,
            date_joined TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
            id INT PRIMARY KEY DEFAULT 1,
            name VARCHAR(255) DEFAULT 'The Global Football Watch',
            tagline VARCHAR(255) DEFAULT 'Your Daily Home of Football News, Fixtures & Transfers',
            logo VARCHAR(255),
            admin_email VARCHAR(255),
            smtp_sender VARCHAR(255),
            smtp_host VARCHAR(255),
            smtp_port INT DEFAULT 587,
            smtp_user VARCHAR(255),
            smtp_pass VARCHAR(255),
            smtp_encryption VARCHAR(10) DEFAULT 'tls',
            api_key VARCHAR(255) DEFAULT '00lee8418970aa40edfd7a4b97cbbb65',
            api_url VARCHAR(255) DEFAULT 'https://v3.football.api-sports.io',
            api_header VARCHAR(255) DEFAULT 'x-apisports-key',
            whatsapp_number VARCHAR(50),
            fb_url VARCHAR(255),
            tw_url VARCHAR(255),
            ig_url VARCHAR(255),
            yt_url VARCHAR(255)
        )");

        // Add reset token fields to users
        $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS reset_token VARCHAR(100) NULL, ADD COLUMN IF NOT EXISTS reset_expires DATETIME NULL");

        // Seed Categories
        $categories = ['EPL', 'UCL', 'Transfers', 'Chelsea', 'Arsenal', 'Liverpool', 'Man City', 'Fixtures', 'Table', 'Top Scorers'];
        $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name, slug) VALUES (?, ?)");
        foreach ($categories as $cat) {
            $stmt->execute([$cat, strtolower(str_replace(' ', '-', $cat))]);
        }

        // Write config.php safely
        $config_content = "<?php\n";
        $config_content .= "define('DB_HOST', " . var_export($db_host, true) . ");\n";
        $config_content .= "define('DB_NAME', " . var_export($db_name, true) . ");\n";
        $config_content .= "define('DB_USER', " . var_export($db_user, true) . ");\n";
        $config_content .= "define('DB_PASS', " . var_export($db_pass, true) . ");\n";

        file_put_contents(__DIR__ . '/../config.php', $config_content);
        $success = true;

    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}
?>

<div class="text-center mb-5">
    <h2 className="display-6 font-condensed fw-black italic text-white text-uppercase">Stage 2: Intelligence Base</h2>
    <p className="text-muted text-uppercase fw-bold ls-widest" style="font-size: 10px;">Database configuration and schema injection</p>
</div>

<?php if ($success): ?>
    <div class="alert alert-success rounded-0 font-condensed fw-bold italic text-uppercase mb-4">
        Database initialized successfully. Configuration file deployed.
    </div>
    <div class="text-end">
        <a href="?stage=3" class="btn btn-danger rounded-0 fw-black italic text-uppercase px-5 py-3">Next: Admin Setup</a>
    </div>
<?php else: ?>
    <?php if ($error): ?>
        <div class="alert alert-danger rounded-0 font-condensed fw-bold italic text-uppercase mb-4">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
        <div class="mb-3">
            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 d-block">Database Host</label>
            <input type="text" name="db_host" required class="form-control bg-dark border-white border-opacity-10 text-white rounded-0" value="localhost">
        </div>
        <div class="mb-3">
            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 d-block">Database Name</label>
            <input type="text" name="db_name" required class="form-control bg-dark border-white border-opacity-10 text-white rounded-0" value="gfw_db">
        </div>
        <div class="mb-3">
            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 d-block">Database User</label>
            <input type="text" name="db_user" required class="form-control bg-dark border-white border-opacity-10 text-white rounded-0" value="root">
        </div>
        <div class="mb-3">
            <label class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 d-block">Database Password</label>
            <input type="password" name="db_pass" class="form-control bg-dark border-white border-opacity-10 text-white rounded-0">
        </div>
        <div class="text-end pt-3">
            <button type="submit" class="btn btn-danger rounded-0 fw-black italic text-uppercase px-5 py-3">Deploy Database</button>
        </div>
    </form>
<?php endif; ?>
