<?php
// includes/db.php

$config_file = __DIR__ . '/../config.php';

if (file_exists($config_file)) {
    require_once $config_file;
} else {
    // If config doesn't exist, we might be in the middle of installation
    // but usually this file shouldn't be included if not installed.
}

function get_db_connection() {
    if (!defined('DB_HOST')) {
        return null;
    }

    $host = DB_HOST;
    $db   = DB_NAME;
    $user = DB_USER;
    $pass = DB_PASS;
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        // In a real production app, you might want to log this instead of throwing
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }
}

/**
 * Automatically update schema if missing tables/columns
 */
function run_migrations($pdo) {
    // 1. Create Categories table
    $stmt = $pdo->query("SHOW TABLES LIKE 'categories'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("CREATE TABLE categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL UNIQUE,
            slug VARCHAR(100) NOT NULL UNIQUE,
            description TEXT
        )");

        $categories = ['EPL', 'UCL', 'Transfers', 'Chelsea', 'Arsenal', 'Liverpool', 'Man City', 'Fixtures', 'Table', 'Top Scorers'];
        $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name, slug) VALUES (?, ?)");
        foreach ($categories as $cat) {
            $stmt->execute([$cat, strtolower(str_replace(' ', '-', $cat))]);
        }
    }

    // 2. Create Pages table
    $stmt = $pdo->query("SHOW TABLES LIKE 'pages'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("CREATE TABLE pages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            content LONGTEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
    }

    // 3. Update Posts table
    $stmt = $pdo->query("SHOW COLUMNS FROM posts LIKE 'slug'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE posts ADD COLUMN slug VARCHAR(255) NULL AFTER title");
        // Update existing posts with unique slugs
        $stmt = $pdo->query("SELECT id, title FROM posts");
        while ($row = $stmt->fetch()) {
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $row['title']), '-'));
            if (!$slug) $slug = 'post';
            $pdo->prepare("UPDATE posts SET slug = ? WHERE id = ?")->execute([$slug . '-' . $row['id'], $row['id']]);
        }
        $pdo->exec("ALTER TABLE posts MODIFY slug VARCHAR(255) NOT NULL");
        $pdo->exec("ALTER TABLE posts ADD UNIQUE (slug)");
    }

    $stmt = $pdo->query("SHOW COLUMNS FROM posts LIKE 'category_id'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE posts ADD COLUMN category_id INT AFTER slug");
    }

    // 4. Update Users table
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'reset_token'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN reset_token VARCHAR(100) NULL, ADD COLUMN reset_expires DATETIME NULL");
    }

    // 5. Update Comments table
    $stmt = $pdo->query("SHOW COLUMNS FROM comments LIKE 'email'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE comments ADD COLUMN email VARCHAR(255) AFTER author");
    }

    // 6. Update Settings table
    $stmt = $pdo->query("SHOW COLUMNS FROM settings LIKE 'api_key'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE settings
            ADD COLUMN api_key VARCHAR(255) DEFAULT '00lee8418970aa40edfd7a4b97cbbb65',
            ADD COLUMN api_url VARCHAR(255) DEFAULT 'https://v3.football.api-sports.io',
            ADD COLUMN api_header VARCHAR(255) DEFAULT 'x-apisports-key'");
    }

    $stmt = $pdo->query("SHOW COLUMNS FROM settings LIKE 'fb_access_token'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE settings
            ADD COLUMN fb_access_token TEXT,
            ADD COLUMN tw_api_key VARCHAR(255),
            ADD COLUMN tw_api_secret VARCHAR(255),
            ADD COLUMN li_access_token TEXT,
            ADD COLUMN meta_access_token TEXT");
    }

    $stmt = $pdo->query("SHOW COLUMNS FROM settings LIKE 'api_host'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE settings ADD COLUMN api_host VARCHAR(255) DEFAULT 'api-football-v1.p.rapidapi.com'");
    }
}
